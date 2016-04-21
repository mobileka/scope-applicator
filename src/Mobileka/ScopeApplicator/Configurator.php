<?php namespace Mobileka\ScopeApplicator;

use Mobileka\MosaicArray\MosaicArray;
use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;

class Configurator
{
    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var InputManagerInterface
     */
    protected $inputManager;

    /**
     * @param InputManagerInterface $inputManager
     * @param array                 $scopes
     */
    public function __construct(InputManagerInterface $inputManager, array $scopes = [])
    {
        $this->inputManager = $inputManager;
        $this->scopes = $scopes;
    }

    /**
     * @return \Mobileka\ScopeApplicator\Contracts\InputManagerInterface
     */
    public function getInputManager()
    {
        return $this->inputManager;
    }

    /**
     * Parse scope configuration passed as a second argument for applyScopes method
     *
     * @return array
     */
    public function prepareScopes()
    {
        $result = [];

        foreach ($this->scopes as $key => $scope) {
            // If no scope configuration has been provided, just make scope's name to be its alias
            if (!is_array($scope)) {
                $result[$scope] = ['alias' => $scope];
                continue;
            }

            $result[$key] = $scope;

            // If no alias has been provided, make it to be scope's name
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
    public function parseScopeArguments(array $scope)
    {
        $scope = new MosaicArray($scope);
        $result = [];

        // If "type" key has been provided, we have to typecast the result
        $type = $scope->getItem('type');

        // Get default scope argument value
        $default = $scope->getItem('default');

        // Get request parameter value
        $value = $this->getInputManager()->get($scope['alias'], null);

        // If there are no parameters with the scope name:
        // 1) in a case when no default value is set, return null to ignore this scope
        // 2) if default value is set, return it
        if (is_null($value)) {
            return !is_null($default) ? [$default] : null;
        }

        // If "keys" configuration key has been provided, we are dealing with an array parameter (e.g. <input name="somename[somekey]">)
        $keys = $scope->getItem('keys');

        // If "keys" are empty, we have to perform some DRY actions
        if (is_null($keys)) {
            $keys = ['default'];
            $value = ['default' => $value];
        }

        foreach ((array) $keys as $key) {
            $arg = $this->setType($value[$key], $type);

            // Empty arguments are not allowed by default in order to allow default scope argument values
            // Set allowEmpty option to `true` if you want to change this behavior
            if ($arg !== '' or $scope->getItem('allowEmpty')) {
                $result[] = $arg;
            }
        }

        return $result;
    }

    /**
     * Typecast a variable
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
