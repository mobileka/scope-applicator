<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Support\Facades\Log;
use Mobileka\ScopeApplicator\Contracts\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @param string $message
     * @codeCoverageIgnore
     */
    public function log($message)
    {
        Log::warning($message);
    }
}
