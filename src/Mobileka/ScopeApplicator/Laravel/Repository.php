<?php namespace Mobileka\ScopeApplicator\Laravel;

abstract class Repository
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    public function getInputManager()
    {
        return new InputManager;
    }
}
