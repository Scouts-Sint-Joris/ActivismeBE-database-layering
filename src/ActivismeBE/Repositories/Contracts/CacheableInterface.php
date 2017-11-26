<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Contracts;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Interface CacheableInterface
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Contracts
 */
interface CacheableInterface
{
    /**
     * Set cache repository.
     *
     * @param  CacheRepository $repository
     * @return $this
     */
    public function setCacheRepository(CacheRepository $repository);

    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public function getCacheRepository();

    /**
     * Get Cache key for the method.
     *
     * @param  string $method
     * @param  bool   $args
     * @return string
     */
    public function getCacheKey($method, $args = null);

    /**
     * get cache minutes
     *
     * @return integer
     */
    public function getCacheMinutes();

    /**
     * Skip cache
     *
     * @param  boolean $status
     * @return $this
     */
    public function skipCache($status = true);
}
