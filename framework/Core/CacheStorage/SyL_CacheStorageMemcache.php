<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
 *
 * PHP version 5 (>= 5.2.10)
 *
 * Copyright (C) 2006-2011 k.watanabe
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * -----------------------------------------------------------------------------
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

if (!defined('SYL_MEMCACHE_HOST')) {
    define('SYL_MEMCACHE_HOST', 'localhost');
}
if (!defined('SYL_MEMCACHE_PORT')) {
    define('SYL_MEMCACHE_PORT', 11211);
}

/** Memcache用キャッシュクラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Cache/SyL_CacheEntityMemcache.php';

/**
 * Memcacheキャッシュ格納クラス
 *
 * 接続ホスト／ポートを変更する場合は、事前に関連定数にセットする。
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheStorageMemcache extends SyL_CacheStorageAbstract
{
    /**
     * Memcacheオブジェクト
     * 
     * @var Memcache
     */
    private static $memcache = null;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        parent::__construct();

        self::$memcache = new Memcache();
        self::$memcache->pconnect(SYL_MEMCACHE_HOST, SYL_MEMCACHE_PORT);
    }

    /**
     * キャッシュを取得する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return mixed キャッシュ値
     */
    protected function getCache($type, $name, $lifetime)
    {
        return self::createCache($type, $name, $lifetime)->read();
    }

    /**
     * アプリケーションキャッシュを保存する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     */
    protected function setCache($type, $name, $data, $lifetime)
    {
        self::createCache($type, $name, $lifetime)->write($data);
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return SyL_CacheEntityMemcache キャッシュオブジェクト
     */
    private static function createCache($type, $name, $lifetime)
    {
        $key = sprintf('syl_%s.%s', $type, sha1($name));
        $cache = new SyL_CacheEntityMemcache(self::$memcache, $key);
        $cache->setLifeTime($lifetime);
        return $cache;
    }
}
