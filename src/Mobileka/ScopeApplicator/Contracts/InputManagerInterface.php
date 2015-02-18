<?php namespace Mobileka\ScopeApplicator\Contracts;

interface InputManagerInterface
{
    /**
     * Return a value from an array of Request parameters by key
     * If nothing found, return $defaultValue
     *
     * @param  string|null $key
     * @param  mixed       $defaultValue
     * @return mixed
     */
    public function get($key = null, $defaultValue = null);
}
