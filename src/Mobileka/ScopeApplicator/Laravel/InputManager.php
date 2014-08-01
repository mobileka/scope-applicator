<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Http\Request;
use Mobileka\ScopeApplicator\InputManagerInterface;

class InputManager implements InputManagerInterface
{
    public $inputManager;

    public function __construct()
    {
        $this->inputManager = Request::createFromGlobals();
    }

    public function get($key = null, $default = null)
    {
        return $this->inputManager->query($key, $default);
    }
}
