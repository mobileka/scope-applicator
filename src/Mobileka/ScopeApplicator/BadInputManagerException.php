<?php namespace Mobileka\ScopeApplicator;

use Exception;

class BadInputManagerException extends Exception
{
    protected $message = 'getInputManager() method must return an instance of a class which implements the InputManagerInterface';
}
