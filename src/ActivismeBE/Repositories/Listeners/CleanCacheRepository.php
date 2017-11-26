<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Listeners;

use ActivismeBE\DatabaseLayering\Repositories\Contracts\RepositoryInterface;
use ActivismeBE\DatabaseLayering\Repositories\Events\RepositoryEventBase;
use ActivismeBE\DatabaseLayering\Repositories\Helpers\CacheKeys;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class CleanCacheRepository
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Listeners
 */
class CleanCacheRepository
{
    /**
     * @var CacheRepository
     */
    protected $cache = null;

    /**
     * @var RepositoryInterface
     */
    protected $repository = null;

    /**
     * @var Model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $action = null;

    /**
     * CleanCacheRepository constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->cache = app(config('repositories.cache.repository', 'cache'));
    }

    /**
     * @param RepositoryEventBase $event
     */
    public function handle(RepositoryEventBase $event)
    {
        try {
            $cleanEnabled = config('repositories.cache.clean.enabled', true);

            if ($cleanEnabled) {
                $this->repository   = $event->getRepository();
                $this->model        = $this->getModel();
                $this->action       = $this->getAction();

                if (config("repositories.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));

                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
