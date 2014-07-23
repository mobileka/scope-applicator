<?php namespace Mobileka\ScopeApplicator;

interface InputManagerInterface
{
    /**
     * Return a value from POST or GET parameters by key
     * If nothing found, return $defaultValue
     *
     * @param  string|null $key
     * @param  mixed       $defaultValue
     * @return mixed
     */
    public function get($key = null, $defaultValue = null);
}
