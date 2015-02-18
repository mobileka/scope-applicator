<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Http\Request;
use Mobileka\ScopeApplicator\Contracts\InputManagerInterface;

class InputManager implements InputManagerInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    public $inputManager;

    public function __construct()
    {
        $this->inputManager = Request::createFromGlobals();
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return $this->inputManager->query($key, $default);
    }
}
