<?php namespace Stubs\Fake;

class Repository
{
    use \Mobileka\ScopeApplicator\ScopeApplicator;

    public function getInputManager()
    {
        return new InputManager;
    }

    public function getFakeData($allowedScopes = [])
    {
        return $this->applyScopes(new DataProvider, $allowedScopes)->get();
    }
}
