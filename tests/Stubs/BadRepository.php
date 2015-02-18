<?php namespace Stubs;

use Mobileka\ScopeApplicator\ScopeApplicator;

class BadRepository
{
    use ScopeApplicator;

    /**
     * Returns a an instance of a class which does not implement InputManagerInterface
     */
    public function getInputManager()
    {
        return \Mockery::mock('\Mobileka\MosaicHelpers\MosaicArray');
    }
}
