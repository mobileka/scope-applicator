<?php namespace Mobileka\ScopeApplicator\Laravel;

use Mobileka\ScopeApplicator\ScopeApplicator;

abstract class Repository
{
    use ScopeApplicator;

    /**
     * @return \Mobileka\ScopeApplicator\Laravel\InputManager
     */
    public function getInputManager()
    {
        return new InputManager;
    }
}
