<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Mobileka\ScopeApplicator\ScopeApplicator;

abstract class Model extends Eloquent
{
    use ScopeApplicator;

    /**
     * @return \Mobileka\ScopeApplicator\Laravel\InputManager
     */
    public function getInputManager()
    {
        return new InputManager;
    }

    /**
     * @param string $method
     * @param array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'handleScopes') {
            $method = 'applyScopes';
            array_unshift($parameters, new static);
        }

        return parent::__callStatic($method, $parameters);
    }
}
