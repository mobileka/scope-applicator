<?php

/**
 * @covers Mobileka\ScopeApplicator\Laravel\InputManager
 */
class LaravelInputManagerTest extends BaseTestCase
{
    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\InputManager::__construct
     */
    public function is_instantiable()
    {
        $lim = new Mobileka\ScopeApplicator\Laravel\InputManager;
        assertTrue(method_exists($lim, 'get'), 'LaravelInputManager does not have "get" method');
    }

    /**
     * @test
     * @covers Mobileka\ScopeApplicator\Laravel\InputManager::get
     */
    public function gets_value_from_user_input()
    {
        $lim = new Mobileka\ScopeApplicator\Laravel\InputManager;
        assertNull($lim->get('param'));
        assertSame('no such param', $lim->get('param', 'no such param'));

        // add something to request parameters
        $lim->inputManager->query->set('param', 'hello');

        assertEquals('hello', $lim->get('param'));
    }
}
