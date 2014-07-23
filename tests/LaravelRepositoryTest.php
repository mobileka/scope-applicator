<?php

/**
 * @covers Mobileka\ScopeApplicator\Repositories\LaravelRepository
 */
class LaravelRepositoryTest extends BaseTestCase
{
    /**
     * @covers Mobileka\ScopeApplicator\Repositories\LaravelRepository::getInputManager
     */
    public function test_returns_input_manager_instance()
    {
        $repository = new Stubs\RealRepository;
        assertInstanceOf('Mobileka\ScopeApplicator\InputManagerInterface', $repository->getInputManager());
    }
}
