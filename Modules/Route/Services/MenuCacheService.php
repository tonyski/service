<?php

namespace Modules\Route\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Admin\Entities\Admin;

/**
 * 入口方法只存在与查询，新增与删除，需传入对应的 admin 对象
 *
 * Class MenuCacheService
 * @package Modules\Route\Services
 */
class MenuCacheService
{
    const CACHE_PREFIX = 'ADMIN-MENU::';

    /**
     * @param Admin $admin
     * @return array
     */
    public static function find($admin)
    {
        if (!Cache::has(self::getCacheKey($admin->uuid))) return null;
        return self::getAdminCache($admin->uuid);
    }

    /**
     * @param $admin
     * @param $data
     * @return bool
     */
    public static function store($admin, $data)
    {
        return self::storeAdminMenuCache($admin->uuid, $data);
    }

    /**
     * @param array|int $admin
     */
    public static function clear($admin)
    {
        self::clearAdminMenuCache($admin->uuid);
    }

    /**
     * @param string $uuid
     * @return string
     */
    private static function getCacheKey($uuid)
    {
        return self::CACHE_PREFIX . $uuid;
    }

    /**
     * @param $uuid
     * @return array
     */
    private static function getAdminCache($uuid)
    {
        $menuCache = Cache::get(self::getCacheKey($uuid));
        return json_decode($menuCache, true);
    }

    /**
     * @param $uuid
     * @param array $data
     * @return bool
     */
    private static function storeAdminMenuCache($uuid, array $data)
    {
        $key = self::getCacheKey($uuid);
        return Cache::forever($key, json_encode($data));
    }

    /**
     * @param $uuid
     */
    private static function clearAdminMenuCache($uuid)
    {
        $key = self::getCacheKey($uuid);
        if (Cache::has($key)) Cache::forget($key);
    }
}
