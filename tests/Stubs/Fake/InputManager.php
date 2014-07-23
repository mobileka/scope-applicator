<?php namespace Stubs\Fake;

use Mobileka\ScopeApplicator\InputManagerInterface;

class InputManager implements InputManagerInterface
{
    public function get($key = null, $default = null)
    {
        if ($key === 'one') {
            return $key;
        }

        if ($key === 'six') {
            return null;
        }

        return (in_array($key, ['one:default', 'five', 'six:default'])) ? '' : $default;
    }
}
