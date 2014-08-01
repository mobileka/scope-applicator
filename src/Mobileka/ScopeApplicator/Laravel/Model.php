<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    public function getInputManager()
    {
        return new InputManager;
    }

    public static function __callStatic($method, $parameters)
    {
        if ($method === 'handleScopes') {
            $method = 'applyScopes';
            array_unshift($parameters, new static);
        }

        return parent::__callStatic($method, $parameters);
    }
}
