<?php namespace Stubs\Fake;

use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;
use Mobileka\ScopeApplicator\Contracts\LoggerInterface;
use Mobileka\ScopeApplicator\ScopeApplicator;

class Repository
{
    use ScopeApplicator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param null $logger
     */
    public function __construct($logger = null)
    {
        $this->logger = $logger ? : new Logger;
    }

    /**
     * @return InputManagerInterface
     */
    public function getInputManager()
    {
        return new InputManager;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array $allowedScopes
     * @return mixed
     * @throws \Mobileka\ScopeApplicator\BadInputManagerException
     */
    public function getFakeData($allowedScopes = [])
    {
        return $this->applyScopes(new DataProvider, $allowedScopes)->get();
    }
}
