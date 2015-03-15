<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** キャッシュ管理クラス */
require_once 'SyL_CacheManagerAbstract.php';
/** Memcache用キャッシュクラス */
require_once 'SyL_CacheEntityMemcache.php';

/**
 * Memcache管理クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheManagerMemcache extends SyL_CacheManagerAbstract
{
    /**
     * Memcacheオブジェクト
     *
     * @var Memcache
     */
    private $memcache = null;
    /**
     * キャッシュの確認CRCを付加するか
     *
     * @var bool
     */
    protected $is_crc = false;
    /**
     * キャッシュをシリアル化するか
     *
     * @var bool
     */
    protected $is_serialize = false;

    /**
     * コンストラクタ
     *
     * @param Memcache Memcacheオブジェクト
     */
    public function __construct(Memcache $memcache)
    {
        parent::__construct();
        $this->memcache = $memcache;
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュキー
     * @return SyL_CacheEntityAbstract キャッシュオブジェクト
     */
    public function create($key)
    {
        $cache = new SyL_CacheEntityMemcache($this->memcache, $key);
        $cache->setPrefix($this->prefix);
        $cache->setSuffix($this->suffix);
        $cache->setLifeTime($this->life_time);
        $cache->useCrc($this->is_crc);
        $cache->useSerialize($this->is_serialize);
        return $cache;
    }

    /**
     * 期限切れキャッシュを削除する
     *
     * 有効期限が切れると自動的に無効になるので、特に何もしない。
     */
    public function clean()
    {
    }

    /**
     * キャッシュを全て削除する
     */
    public function cleanAll()
    {
        $this->memcache->flush();
    }
}