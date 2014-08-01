<?php

abstract class BaseTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Invoke any method of any class
     *
     * @param  string $class
     * @param  string $method
     * @param  mixed  $params
     * @return mixed
     */
    public function invokeMethod($class, $method, $params = [])
    {
        $reflectedMethod = new ReflectionMethod($class, $method);
        $reflectedMethod->setAccessible(true);

        return $reflectedMethod->invokeArgs($class, $params);
    }
}
