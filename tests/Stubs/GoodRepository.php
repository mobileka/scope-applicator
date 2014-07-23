<?php namespace Stubs;

class GoodRepository
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    public $inputManager;

    public function __construct($manager)
    {
        $this->inputManager = $manager;
    }

    public function getInputManager()
    {
        return $this->inputManager;
    }
}
