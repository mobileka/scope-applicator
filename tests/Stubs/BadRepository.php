<?php namespace Stubs;

class BadRepository
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    /**
     * Returns a an instance of a class wich does not implement InputManagerInterface
     */
    public function getInputManager()
    {
        return \Mockery::mock('\Mobileka\MosaiqHelpers\MosaiqArray');
    }
}
