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

/** キャッシュ管理クラス */
require_once 'SyL_CacheManagerAbstract.php';
/** DBキャッシュクラス */
require_once 'SyL_CacheEntityDb.php';

/**
 * ファイルキャッシュ管理クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheManagerDb extends SyL_CacheManagerAbstract
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
     */
    public function __construct(SyL_DbAbstract $db, $table, $id, $data, $timestamp)
    {
        parent::__construct();

        $this->db    = $db;
        $this->table = $table;
        $this->id    = $id;
        $this->data  = $data;
        $this->timestamp = $timestamp;
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュキー
     * @return SyL_CacheEntityAbstract キャッシュオブジェクト
     */
    public function create($key)
    {
        $cache = new SyL_CacheEntityDb($this->db, $this->table, $this->id, $this->data, $this->timestamp, $key);
        $cache->setPrefix($this->prefix);
        $cache->setSuffix($this->suffix);
        $cache->setLifeTime($this->life_time);
        $cache->useCrc($this->is_crc);
        $cache->useSerialize($this->is_serialize);
        return $cache;
    }

    /**
     * 期限切れキャッシュを削除する
     */
    public function clean()
    {
        if ($this->life_time == 0) {
            return;
        }

        $sql  = "";
        $sql .= "DELETE FROM ";
        $sql .=   "{$this->table} ";
        $sql .= "WHERE ";
        $sql .=   "(" . time() . " > {$this->timestamp} + {$this->life_time}) ";
        if ($this->prefix && $this->suffix) {
            $sql .= "AND ({$this->id} like '" . $this->db->escape($this->prefix) . "%" . $this->db->escape($this->suffix) . "') ";
        } else if ($this->prefix) {
            $sql .= "AND ({$this->id} like '" . $this->db->escape($this->prefix) . "%') ";
        } else if ($this->suffix) {
            $sql .= "AND ({$this->id} like '%" . $this->db->escape($this->suffix) . "') ";
        }
        $this->db->exec($sql);
    }

    /**
     * キャッシュを全て削除する
     */
    public function cleanAll()
    {
        $sql  = "";
        $sql .= "DELETE FROM ";
        $sql .=   "{$this->table} ";
        $this->db->exec($sql);
    }
}