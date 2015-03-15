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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** キャッシュクラス */
require_once 'SyL_CacheEntityAbstract.php';

/**
 * Memcache用キャッシュクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheEntityMemcache extends SyL_CacheEntityAbstract
{
    /**
     * Memcacheオブジェクト
     *
     * @var Memcache
     */
    private $memcache = null;
    /**
     * Memcache圧縮フラグ
     *
     * @var int
     */
    private $compress = 0;
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
     * @param string キャッシュキー
     */
    public function __construct(Memcache $memcache, $key)
    {
        parent::__construct($key);
        $this->memcache = $memcache;
    }

    /**
     * Memcache圧縮フラグをセットする
     *
     * @param int Memcache圧縮フラグ
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * キャッシュの更新時間を更新する
     *
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheException キャッシュ更新時例外
     */
    public function updateCacheTime()
    {
        $key = $this->getKey();
        try {
            $data = $this->memcache->get($key);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
        if ($data === false) {
            throw new SyL_CacheNotFoundException("cache data not found ({$key})");
        }

        $this->memcache->replace($key, $data, $this->compress, $this->life_time);
    }

    /**
     * キャッシュを読み込む
     *
     * @return mixed キャッシュデータ
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheInvalidHashException キャッシュのハッシュ値が一致しない場合
     * @throws SyL_CacheException キャッシュ読み込み時例外
     */
    public function read()
    {
        $key = $this->getKey();
        $data = null;
        try {
            $data = $this->memcache->get($key);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
        if ($data === false) {
            throw new SyL_CacheNotFoundException("cache data not found ({$key})");
        }

        if ($this->is_crc) {
            $hash = substr($data, 0, 32);
            $data = substr($data, 32);
            $rhash = $this->getCrc($data);
            if ($hash != $rhash) {
                $this->remove();
                throw new SyL_CacheInvalidHashException("invalid hash (expected: {$hash} - actual: {$rhash})");
            }
        }

        return $this->is_serialize ? unserialize($data) : $data;
    }

    /**
     * キャッシュを保存する
     *
     * @param mixed キャッシュデータ
     * @throws SyL_CacheException キャッシュ保存時エラーの場合
     */
    public function write($data)
    {
        if ($this->is_serialize) {
            $data = serialize($data);
        }
        if ($this->is_crc) {
            $data = $this->getCrc($data) . $data;
        }

        try {
            if (!$this->memcache->set($this->getKey(), $data, $this->compress, $this->life_time)) {
                throw new Exception('cache write failed');
            }
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
    }

    /**
     * キャッシュを削除する
     *
     * @throws SyL_CacheException キャッシュ削除時例外
     */
    public function remove()
    {
        try {
            $this->memcache->delete($this->getKey());
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
    }
}