<?php

/**
 * @covers Mobileka\ScopeApplicator\Laravel\InputManager
 */
class LaravelInputManagerTest extends BaseTestCase
{
    /**
     * @covers Mobileka\ScopeApplicator\Laravel\InputManager::__construct
     */
    public function test_is_instantiable()
    {
        $lim = new Mobileka\ScopeApplicator\Laravel\InputManager;
        assertTrue(method_exists($lim, 'get'), 'LaravelInputManager does not have "get" method');
    }

    /**
     * @covers Mobileka\ScopeApplicator\Laravel\InputManager::get
     */
    public function test_gets_value_from_user_input()
    {
        $lim = new Mobileka\ScopeApplicator\Laravel\InputManager;
        assertNull($lim->get('param'));
        assertSame('no such param', $lim->get('param', 'no such param'));

        // add something to request parameters
        $lim->inputManager->query->set('param', 'hello');

        assertEquals('hello', $lim->get('param'));
    }
}
