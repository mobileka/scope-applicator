<?php namespace Stubs\Fake;

use Mobileka\ScopeApplicator\Contracts\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @param string $message
     * @return string
     */
    public function log($message)
    {
        return $message;
    }
}
