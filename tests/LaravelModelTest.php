<?php

use Stubs\LaravelModel;

/**
 * @covers Mobileka\ScopeApplicator\Laravel\Model
 */
class LaravelModelTest extends BaseTestCase
{

    /**
     * @covers Mobileka\ScopeApplicator\Laravel\Model::getInputManager
     */
    public function test_returns_input_manager_instance()
    {
        $model = new LaravelModel;
        assertInstanceOf('Mobileka\ScopeApplicator\InputManagerInterface', $model->getInputManager());
    }

    /**
     * @covers Mobileka\ScopeApplicator\Laravel\Model::__callStatic
     */
    public function test_handles_scopes()
    {
        assertInstanceOf('Stubs\LaravelModel', LaravelModel::handleScopes(['scope']));
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
