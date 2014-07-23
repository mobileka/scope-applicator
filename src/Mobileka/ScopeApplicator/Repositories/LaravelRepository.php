<?php namespace Mobileka\ScopeApplicator\Repositories;

use Mobileka\ScopeApplicator\InputManagers\LaravelInputManager;

abstract class LaravelRepository
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    public function getInputManager()
    {
        return new LaravelInputManager;
    }
}
