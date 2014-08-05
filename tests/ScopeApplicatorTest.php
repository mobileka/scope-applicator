<?php

/**
 * @covers Mobileka\ScopeApplicator\ScopeApplicator
 */
class ScopeApplicatorTest extends BaseTestCase
{
    protected $realRepository;
    protected $fixtures;

    public function setUp()
    {
        $this->realRepository = new Stubs\RealRepository;
        $this->fixtures = require __DIR__.'/Fixtures/fixtures.php';
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     */
    public function test_applies_scopes()
    {
        $data = [
            'one' => ['val' => 1],
            'five' => ['val' => 5],
            'six' => ['val' => 6],
        ];

        $repository = new Stubs\Fake\Repository(new Stubs\Fake\InputManager);

        // no allowed scopes
        assertSame($data, $repository->getFakeData());

        // a normal scope with a single mandatory argument. See Stubs\Fake\DataProvider::one
        assertSame($data['one'], $repository->getFakeData(['one']));

        // a scope with two arguments
        assertSame(
            array_slice($data, 1, null, true),
            $repository->getFakeData([
                'between' => [
                    'keys' => ['min', 'max']
                ]
            ])
        );

        // a scope with two arguments with allowed empty values
        assertSame(
            array_slice($data, 0, -1, true),
            $repository->getFakeData([
                'between' => [
                    'alias' => 'between:empty',
                    'keys' => ['min', 'max'],
                    'allowEmpty' => true
                ]
            ])
        );

        // a scope with no arguments. See Stubs\Fake\DataProvider::five
        assertSame($data['five'], $repository->getFakeData(['five']));

        // a scope that does not exist
        assertSame($data, $repository->getFakeData(['no']));

        // a scope with a default value provided. See Stubs\Fake\DataProvider::six
        assertSame($data['one'], $repository->getFakeData(['six' => ['default' => 'one']]));

        // a scope with a default argument. See Stubs\Fake\DataProvider::six
        assertSame($data['six'], $repository->getFakeData(['six' => ['alias' => 'six:default']]));
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::applyScopes
     * @expectedException Exception
     */
    public function test_throws_invalid_input_manager_exception()
    {
        $badRepository = new Stubs\BadRepository;
        $badRepository->applyScopes([]);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::validateInputManager
     */
    public function test_validates_input_manager()
    {
        $goodRepository = new Stubs\GoodRepository(Mockery::mock('Mobileka\ScopeApplicator\InputManagerInterface'));
        $badRepository = new Stubs\BadRepository;

        assertTrue($this->invokeMethod($this->realRepository, 'validateInputManager'));
        assertTrue($this->invokeMethod($goodRepository, 'validateInputManager'));
        assertFalse($this->invokeMethod($badRepository, 'validateInputManager'));
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::parseScopeConfiguration
     */
    public function test_parses_scope_configuration()
    {
        $repository = new Stubs\GoodRepository(Mockery::mock('Mobileka\ScopeApplicator\InputManagerInterface'));

        foreach ($this->fixtures as $fixture) {
            assertEquals($fixture['result'], $this->invokeMethod($repository, 'parseScopeConfiguration', [$fixture['allowedScopes']]));
        }
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::setType
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function test_sets_variable_type()
    {
        foreach (['bool', 'string', 'int', 'array', 'null'] as $type) {
            foreach ([1, true, 'somestring', null] as $variable) {
                assertInternalType(
                    $type,
                    $this->invokeMethod($this->realRepository, 'setType', [$variable, $type]),
                    "Impossible to convert $variable to $type type"
                );
            }
        }

        // should issue a warning
        $this->invokeMethod($this->realRepository, 'setType', [1, 'unknown_type']);
    }

    /**
     * @covers Mobileka\ScopeApplicator\ScopeApplicator::parseScopeArguments
     */
    public function test_parses_scope_arguments()
    {
        foreach ($this->fixtures as $case => $fixture) {
            list($inputManagerMock, $expectedResult) = $this->prepareData($case);
            $repository = new Stubs\GoodRepository($inputManagerMock);

            assertSame(
                $expectedResult,
                $this->invokeMethod($repository, 'parseScopeArguments', [$fixture['result']['scope']]),
                'test_parses_scope_arguments:: ' . $case . ' has failed'
            );
        }
    }

    protected function prepareData($case)
    {
        switch ($case) {
            case 'firstCase':
                $result = [
                    Mockery::mock('Mobileka\ScopeApplicator\InputManagerInterface')
                        ->shouldReceive('get')
                        ->withArgs(['scope', null])
                        ->once()
                        ->andReturn('5')
                        ->mock(),
                    ['5']
                ];
                break;

            case 'secondCase':
                $result = [
                    Mockery::mock('Mobileka\ScopeApplicator\InputManagerInterface')
                        ->shouldReceive('get')
                        ->withArgs(['scopeAlias', null])
                        ->once()
                        ->andReturn(['firstKey' => '5', 'secondKey' => '6'])
                        ->mock(),
                    [5, 6]
                ];
                break;

            default:
                $result = [
                    Mockery::mock('Mobileka\ScopeApplicator\InputManagerInterface')
                        ->shouldReceive('get')
                        ->withArgs(['scope', null])
                        ->once()
                        ->andReturn(['6'])
                        ->mock(),
                    [false]
                ];
                break;
        }

        return $result;
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
