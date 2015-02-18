<?php namespace Stubs;

use Mobileka\ScopeApplicator\InputManagerInterface;
use Mobileka\ScopeApplicator\ScopeApplicator;

class GoodRepository
{
    use ScopeApplicator;

    /**
     * @var \Mobileka\ScopeApplicator\InputManagerInterface
     */
    public $inputManager;

    /**
     * @param $manager
     */
    public function __construct(InputManagerInterface $manager)
    {
        $this->inputManager = $manager;
    }

    /**
     * @return \Mobileka\ScopeApplicator\InputManagerInterface
     */
    public function getInputManager()
    {
        return $this->inputManager;
    }
}
