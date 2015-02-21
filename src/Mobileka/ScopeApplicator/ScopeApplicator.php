<?php namespace Mobileka\ScopeApplicator;

use ErrorException;
use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;
use Mobileka\ScopeApplicator\Contracts\LoggerInterface;

/**
 * Easily filter data based on named scopes in models
 */
trait ScopeApplicator
{
    /**
     * Provide a way to get request parameters
     *
     * @return InputManagerInterface
     */
    abstract public function getInputManager();

    /**
     * @return LoggerInterface
     */
    abstract public function getLogger();

    /**
     * @param array $scopes
     * @return \Mobileka\ScopeApplicator\Configurator
     */
    public function getConfigurator($scopes)
    {
        return new Configurator($this->getInputManager(), $scopes);
    }

    /**
     * Apply scopes
     *
     * @param  mixed $dataProvider
     * @param  array $allowedScopes
     * @throws BadInputManagerException
     * @return mixed
     */
    public function applyScopes($dataProvider, array $allowedScopes = [])
    {
        // Validate getInputManager() implementation
        if (!$this->validateInputManager()) {
            throw new BadInputManagerException;
        }

        // If there are no allowed scopes, just return the $dataProvider
        if ($allowedScopes) {
            $configurator = $this->getConfigurator($allowedScopes);
            $scopes = $configurator->prepareScopes();

            foreach ($scopes as $scope => $config) {
                $scopeArguments = $configurator->parseScopeArguments($config);

                // If null, we should ignore this scope
                if (!is_null($scopeArguments)) {
                    try {
                        $dataProvider = call_user_func_array([$dataProvider, $scope], $scopeArguments);
                    } catch (ErrorException $e) {
                        $this->getLogger()->log($e);
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
}
