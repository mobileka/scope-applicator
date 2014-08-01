<?php

/**
 * @covers Mobileka\ScopeApplicator\Laravel\Repository
 */
class LaravelRepositoryTest extends BaseTestCase
{
    /**
     * @covers Mobileka\ScopeApplicator\Laravel\Repository::getInputManager
     */
    public function test_returns_input_manager_instance()
    {
        $repository = new Stubs\RealRepository;
        assertInstanceOf('Mobileka\ScopeApplicator\InputManagerInterface', $repository->getInputManager());
    }
}
