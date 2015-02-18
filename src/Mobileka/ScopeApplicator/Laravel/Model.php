<?php namespace Mobileka\ScopeApplicator\Laravel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Mobileka\ScopeApplicator\ScopeApplicator;

abstract class Model extends Eloquent
{
    use ScopeApplicator;

    /**
     * @return \Mobileka\ScopeApplicator\Laravel\InputManager
     */
    public function getInputManager()
    {
        return new InputManager;
    }

    /**
     * @return \Mobileka\ScopeApplicator\Contracts\LoggerInterface
     */
    public function getLogger()
    {
        return new Logger;
    }

    /**
     * @param  array $scopes
     * @return mixed
     */
    public static function handleScopes($scopes = [])
    {
        return parent::__callStatic('applyScopes', [new static, $scopes]);
    }
}
