<?php namespace Mobileka\ScopeApplicator\InputManagers;

use Illuminate\Http\Request as InputManager;
use Mobileka\ScopeApplicator\InputManagerInterface;

class LaravelInputManager implements InputManagerInterface
{
    public $inputManager;

    public function __construct()
    {
        $this->inputManager = InputManager::createFromGlobals();
    }

    public function get($key = null, $default = null)
    {
        return $this->inputManager->query($key, $default);
    }
}
