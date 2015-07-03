<?php
use Mobileka\ScopeApplicator\Configurator;
use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;

/**
 * @covers Mobileka\ScopeApplicator\Configurator
 */
class ConfiguratorTest extends BaseTestCase
{
    /**
     * @var array
     */
    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = require __DIR__ . '/Fixtures/fixtures.php';
    }

    /**
     * @param InputManagerInterface|null $manager
     * @param array                      $scopes
     * @return \Mobileka\ScopeApplicator\Configurator
     */
    public function getInstance($manager = null, $scopes = [])
    {
        $manager = $manager ?: Mockery::mock('Mobileka\ScopeApplicator\Contracts\InputManagerInterface');

        return $configurator = new Configurator(
            $manager,
            $scopes
        );
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::prepareScopes
     * @test
     */
    public function prepares_scope_configuration()
    {
        $configurator = $this->getInstance(
            null,
            [
                'scope',
                'aliasedScope' => [
                    'alias' => 'scopeAlias',
                    'keys' => ['firstKey', 'secondKey'],
                    'type' => 'int'
                ],
                'booleanScope' => [
                    'type' => 'boolean'
                ]
            ]
        );

        $expect = [
            'scope' => ['alias' => 'scope'],
            'aliasedScope' => [
                'alias' => 'scopeAlias',
                'keys' => ['firstKey', 'secondKey'],
                'type' => 'int'
            ],
            'booleanScope' => [
                'alias' => 'booleanScope',
                'type' => 'boolean'
            ],
        ];

        $result = $configurator->prepareScopes();

        assertEquals($expect, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::setType
     * @expectedException PHPUnit_Framework_Error_Warning
     * @test
     */
    public function sets_variable_type()
    {
        $configurator = $this->getInstance();

        foreach (['bool', 'string', 'int', 'array', 'null'] as $type) {
            foreach ([1, true, 'somestring', null] as $variable) {
                $result = $this->invokeMethod($configurator, 'setType', [$variable, $type]);

                assertInternalType(
                    $type,
                    $result,
                    "Impossible to convert $variable to $type type"
                );
            }
        }

        // should issue a warning
        $this->invokeMethod($configurator, 'setType', [1, 'unknown_type']);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::parseScopeArguments
     * @test
     */
    public function parses_scope_arguments()
    {
        $configurator = $this->getInstance($this->getInputManagerMock(['scope'], '5'));

        $result = $configurator->parseScopeArguments(['alias' => 'scope']);

        assertSame(['5'], $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::parseScopeArguments
     * @test
     */
    public function parses_aliased_scope_arguments()
    {
        $configurator = $this->getInstance(
            $this->getInputManagerMock(['scopeAlias'], ['firstKey' => '5', 'secondKey' => '6'])
        );

        $result = $configurator->parseScopeArguments(
            [
                'alias' => 'scopeAlias',
                'keys' => ['firstKey', 'secondKey'],
                'type' => 'int'
            ]
        );

        assertSame([5, 6], $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::parseScopeArguments
     * @test
     */
    public function parses_boolean_scope_arguments()
    {
        $configurator = $this->getInstance($this->getInputManagerMock(['scope'], ['6']));

        $result = $configurator->parseScopeArguments(
            [
                'alias' => 'scope',
                'type' => 'boolean'
            ]
        );

        assertSame([false], $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::parseScopeArguments
     * @test
     */
    public function ignores_scope_names_which_return_null_from_input_manager()
    {
        $configurator = $this->getInstance($this->getInputManagerMock(['scope'], null));

        $result = $configurator->parseScopeArguments(['alias' => 'scope']);

        assertSame(null, $result);
    }

    /**
     * @covers Mobileka\ScopeApplicator\Configurator::parseScopeArguments
     * @test
     */
    public function returns_default_value_if_scope_name_returns_null_from_input_manager()
    {
        $configurator = $this->getInstance($this->getInputManagerMock(['scope'], null));

        $result = $configurator->parseScopeArguments(['alias' => 'scope', 'default' => 5]);

        assertSame([5], $result);
    }

    /**
     * @param array $args
     * @param mixed $return
     * @return mixed
     */
    protected function getInputManagerMock(array $args, $return)
    {
        $args[] = null;

        return Mockery::mock('Mobileka\ScopeApplicator\Contracts\InputManagerInterface')
            ->shouldReceive('get')
            ->withArgs($args)
            ->once()
            ->andReturn($return)
            ->mock();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
