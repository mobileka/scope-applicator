<?php

/**
 * @covers Mobileka\ScopeApplicator\ScopeApplicator
 */
class ScopeApplicatorTest extends BaseTestCase
{
    /**
     * @var array
     */
    protected $testData = [
        'one' => ['val' => 1],
        'five' => ['val' => 5],
        'six' => ['val' => 6],
    ];

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_regular_scope()
    {
        $expect = $this->testData;
        $repository = new Stubs\Fake\Repository;

        $result = $repository->getFakeData();

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_a_single_mandatory_argument()
    {
        $expect = $this->testData['one'];
        $repository = new Stubs\Fake\Repository;

        // a scope with a single mandatory argument. See Stubs\Fake\DataProvider::one
        $result = $repository->getFakeData(['one']);

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_two_arguments()
    {
        $expect = array_slice($this->testData, 1, null, true);
        $repository = new Stubs\Fake\Repository;

        // a scope with two arguments
        $result = $repository->getFakeData(
            [
                'between' => [
                    'keys' => ['min', 'max']
                ]
            ]
        );

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_two_arguments_empty_values_allowed()
    {
        $expect = array_slice($this->testData, 0, -1, true);
        $repository = new Stubs\Fake\Repository;

        // a scope with two arguments with allowed empty values
        $result = $repository->getFakeData(
            [
                'between' => [
                    'alias' => 'between:empty',
                    'keys' => ['min', 'max'],
                    'allowEmpty' => true
                ]
            ]
        );

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_no_arguments()
    {
        $expect = $this->testData['five'];
        $repository = new Stubs\Fake\Repository;

        // a scope with no arguments. See Stubs\Fake\DataProvider::five
        $result = $repository->getFakeData(['five']);

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_non_existent_scope()
    {
        $expect = $this->testData;
        $repository = new Stubs\Fake\Repository;


        // a scope that does not exist
        $result = $repository->getFakeData(['no']);

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_a_default_value()
    {
        $expect = $this->testData['one'];
        $repository = new Stubs\Fake\Repository;

        // a scope with a default value provided. See Stubs\Fake\DataProvider::six
        $result = $repository->getFakeData(['six' => ['default' => 'one']]);

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     */
    public function applies_a_scope_with_a_default_argument()
    {
        $expect = $this->testData['six'];
        $repository = new Stubs\Fake\Repository;

        // a scope with a default argument. See Stubs\Fake\DataProvider::six
        $result = $repository->getFakeData(['six' => ['alias' => 'six:default']]);

        assertSame($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @test
     * @expectedException \ErrorException
     */
    public function throws_error_exception()
    {
        $repository = new Stubs\Fake\Repository;
        $repository->getFakeData(['error']);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @expectedException Mobileka\ScopeApplicator\BadInputManagerException
     * @test
     */
    public function throws_invalid_input_manager_exception()
    {
        $badRepository = new Stubs\BadRepository;
        $badRepository->applyScopes([]);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::validateInputManager
     * @test
     */
    public function validates_input_manager()
    {
        $goodRepository = new Stubs\GoodRepository(Mockery::mock('Mobileka\ScopeApplicator\Contracts\InputManagerInterface'));
        $badRepository = new Stubs\BadRepository;

        assertTrue($this->invokeMethod($goodRepository, 'validateInputManager'));
        assertFalse($this->invokeMethod($badRepository, 'validateInputManager'));
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
