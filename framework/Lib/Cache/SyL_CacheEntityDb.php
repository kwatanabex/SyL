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

/** キャッシュクラス */
require_once 'SyL_CacheEntityAbstract.php';

/**
 * DBキャッシュクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheEntityDb extends SyL_CacheEntityAbstract
{
    /**
     * DBオブジェクト
     *
     * @var SyL_DbAbstract
     */
    private $db = null; 
    /**
     * キャッシュテーブル名
     *
     * @var string
     */
    private $table = '';
    /**
     * キャッシュIDカラム名
     *
     * @var string
     */
    private $id = '';
    /**
     * キャッシュデータカラム名
     *
     * @var string
     */
    private $data;
    /**
     * キャッシュタイムスタンプカラム名
     *
     * @var string
     */
    private $timestamp;
    /**
     * キャッシュの確認CRCを付加するか
     *
     * @var bool
     */
    protected $is_crc = false;

    /**
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @param string キャッシュテーブル名
     * @param string キャッシュIDカラム名
     * @param string キャッシュデータカラム名
     * @param string キャッシュタイムスタンプカラム名
     * @param string キャッシュキー
     */
    public function __construct(SyL_DbAbstract $db, $table, $id, $data, $timestamp, $key)
    {
        parent::__construct($key);

        $this->db    = $db;
        $this->table = $table;
        $this->id    = $id;
        $this->data  = $data;
        $this->timestamp = $timestamp;
    }

    /**
     * キャッシュの更新時間を更新する
     *
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheException キャッシュ更新時例外
     */
    public function updateCacheTime()
    {
        $result = 0;
        try {
            $sql = sprintf("UPDATE %s SET %s = %s WHERE %s = '%s' ", $this->table, $this->timestamp, time(), $this->id, $this->db->escape($this->getKey()));
            $result = $this->db->exec($sql);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
        if ($result == 0) {
            throw new SyL_CacheNotFoundException('cache data not found (' . $this->getKey() . ')');
        }
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
        $result = array();
        try {
            $sql = sprintf("SELECT %s, %s FROM %s WHERE %s = '%s' ", $this->data, $this->timestamp, $this->table, $this->id, $this->db->escape($this->getKey()));
            $result = $this->db->queryRecord($sql);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }

        // キャッシュ存在判定
        if (count($result) == 0) {
            throw new SyL_CacheNotFoundException('cache data not found (' . $this->getKey() . ')');
        }

        // キャッシュファイルOK
        $data  = $result[$this->data];
        $mtime = $result[$this->timestamp];

        // キャッシュ有効期間切れチェック
        if ($this->life_time > 0) {
            $life_time = $mtime + $this->life_time;
            if ($life_time < time()) {
                $this->remove();
                throw new SyL_CacheNotFoundException("cache expired ({$this->life_time})");
            }
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
     * @throws SyL_CacheException キャッシュ保存時例外
     */
    public function write($data)
    {
        // データのシリアル化判定
        if ($this->is_serialize) {
            $data = serialize($data);
        }
        // CRC追加判定
        if ($this->is_crc) {
            $data = $this->getCrc($data) . $data;
        }

        $columns = array();
        $columns[$this->id] = $this->getKey();
        $columns[$this->timestamp] = time();
        $columns[$this->data] = $data;

        try {
            $this->db->execPerform($this->table, $columns, 'replace', $this->id . '=' . $this->getKey());
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
            $sql = sprintf("DELETE FROM %s WHERE %s = '%s' ", $this->table, $this->id, $this->db->escape($this->getKey()));
            $this->db->exec($sql);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
    }
}