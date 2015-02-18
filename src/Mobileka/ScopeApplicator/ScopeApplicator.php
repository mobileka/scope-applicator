<?php namespace Mobileka\ScopeApplicator;

use ErrorException;
use Exception;
use Mobileka\MosaicArray\MosaicArray;

/**
 * This trait allows us easily filter data based on named scopes in our models
 */
trait ScopeApplicator
{
    /**
     * Provide a way to get request parameters
     *
     * @return \Mobileka\ScopeApplicator\InputManagerInterface
     */
    abstract public function getInputManager();

    /**
     * Apply scopes
     *
     * @param  mixed $dataProvider
     * @param  array $allowedScopes
     * @throws \Exception
     * @return mixed
     */
    public function applyScopes($dataProvider, $allowedScopes = [])
    {
        // Validate getInputManager() implementation
        if (!$this->validateInputManager()) {
            throw new Exception(
                'getInputManager() method must return an instance of a class which implements the InputManagerInterface'
            );
        }

        // If there are no allowed scopes, just return the $dataProvider
        if ($allowedScopes) {
            // Parse configuration
            $scopes = $this->parseScopeConfiguration($allowedScopes);

            foreach ($scopes as $scope => $config) {
                // Parse scope arguments
                $scopeArguments = $this->parseScopeArguments($config);

                // If parseScopeArguments() returns null, we should ignore this scope
                if (!is_null($scopeArguments)) {
                    try {
                        // Apply scopes
                        $dataProvider = call_user_func_array([$dataProvider, $scope], $scopeArguments);
                    } catch (ErrorException $e) {
                        //just ignore this scope
                    }
                }
            }
        }

        return $dataProvider;
    }

    /**
     * Make sure that getInputManger returns an instance of InputManagerInterface
     *
     * @return bool
     */
    protected function validateInputManager()
    {
        return $this->getInputManager() instanceof InputManagerInterface;
    }

    /**
     * Parse scope configuration passed as a second argument for applyScopes method
     *
     * @param array $scopes
     * @internal param array $scope
     * @return array
     */
    protected function parseScopeConfiguration(array $scopes)
    {
        $result = [];

        foreach ($scopes as $key => $scope) {
            // If no scope configuration has been provided, just make the scope name to be its alias as well
            if (!is_array($scope)) {
                $result[$scope] = ['alias' => $scope];
                continue;
            }

            $result[$key] = $scope;

            // If no alias provided, make it to be the scope's name
            if (!isset($scope['alias'])) {
                $result[$key]['alias'] = $key;
            }
        }

        return $result;
    }

    /**
     * Parse scope arguments from request parameters
     *
     * @param  array $scope
     * @return array
     */
    protected function parseScopeArguments(array $scope)
    {
        $scope = new MosaicArray($scope);
        $result = [];

        // If "type" key is provided, we should typecast the result
        $type = $scope->getItem('type');

        // Get default scope argument value
        $default = $scope->getItem('default');

        // Get request parameter value
        $value = $this->getInputManager()->get($scope['alias'], null);

        // If there are no or empty parameters with the scope, return default value
        if ($default and (is_null($value) or $value === '')) {
            return [$default];
        }

        // If there are no parameters with the scope name:
        // 1) in a case when no default value is set, return null to ignore this scope
        // 2) if default value is set, return it
        if (is_null($value)) {
            return $default ? [$default] : null;
        }

        // If "keys" configuration key is provided, we are dealing with an array parameter (e.g. <input name="somename[somekey]">)
        $keys = $scope->getItem('keys');

        // If "keys" are empty, we need to perform some DRY actions
        if (is_null($keys)) {
            $keys = ['default'];
            $value = ['default' => $value];
        }

        foreach ((array) $keys as $key) {
            $arg = $this->setType($value[$key], $type);

            // Empty arguments are not allowed by default to allow default scope argument values
            // Set allowEmpty option to true if you want to change this behavior
            if ($arg !== '' or $scope->getItem('allowEmpty')) {
                $result[] = $arg;
            }
        }

        return $result;
    }

    /**
     * Convert a provided variable to a provided type
     *
     * @param  mixed       $variable
     * @param  string|null $type
     * @return mixed
     */
    protected function setType($variable, $type)
    {
        // Do nothing if $type is not a string
        if (is_string($type)) {
            if (in_array($type, ['bool', 'boolean'])) {
                // Only 1, '1', true and 'true' values will be converted to boolean true
                // Any other value will be converted to boolean false
                $variable = in_array($variable, [1, '1', true, 'true'], true);
            } else {
                settype($variable, $type);
            }
        }

        return $variable;
    }
}
