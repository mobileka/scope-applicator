<?php namespace Stubs\Fake;

use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;

class InputManager implements InputManagerInterface
{
    public function get($key = null, $default = null)
    {
        switch ($key) {
            case 'one':
                return $key;

            case 'six':
                return null;

            case 'between':
                return ['min' => 2, 'max' => 6];

            case 'between:empty':
                return ['min' => '', 'max' => 5];

            case 'five':
            case 'six:default':
            case 'error':
                return '';

            default:
                return $default;
        }
    }
}
