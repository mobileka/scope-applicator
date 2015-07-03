<?php namespace Stubs;

use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;
use Mobileka\ScopeApplicator\ScopeApplicator;
use Stubs\Fake\Logger;

class GoodRepository
{
    use ScopeApplicator;

    /**
     * @var InputManagerInterface
     */
    protected $inputManager;

    /**
     * @param $manager
     */
    public function __construct(InputManagerInterface $manager)
    {
        $this->inputManager = $manager;
    }

    /**
     * @return InputManagerInterface
     */
    public function getInputManager()
    {
        return $this->inputManager;
    }

    /**
     * @return \Stubs\Fake\Logger
     */
    public function getLogger()
    {
        return new Logger;
    }
}
