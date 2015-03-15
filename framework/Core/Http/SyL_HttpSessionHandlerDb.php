<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - Web Application Framework for PHP
 *
 * PHP version 5 (>= 5.2.10)
 *
 * Copyright (C) 2006-2009 k.watanabe
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
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** セッション例外クラス */
requore_once 'SyL_HttpSessionException.php';

/**
 * DB セッションハンドラクラス
 *
 * 例）
 * CREATE TABLE SYL_SESSIONS ( 
 *   session_id varchar(32) NOT NULL, 
 *   session_expires int NOT NULL, 
 *   session_data text,
 *   PRIMARY KEY  (session_id)  
 * ); 
 * のようなテーブルを想定
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
class SyL_HttpSessionHandlerDb
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
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @param string キャッシュテーブル名
     * @param string キャッシュIDカラム名
     * @param string キャッシュデータカラム名
     * @param string キャッシュタイムスタンプカラム名
     */
    private function __construct(SyL_DBAbstract $db, $table, $id, $data, $timestamp)
    {
        ini_set('session.save_handler', 'user');

        session_set_save_handler(
          array($this, 'open'),
          array($this, 'close'),
          array($this, 'read'),
          array($this, 'write'),
          array($this, 'destroy'),
          array($this, 'gc')
        );

        $this->db    = $db;
        $this->table = $table;
        $this->id    = $id;
        $this->data  = $data;
        $this->timestamp = $timestamp;
    }

    /**
     * DB セッションハンドラを初期化する
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @param string キャッシュテーブル名
     * @param string キャッシュIDカラム名
     * @param string キャッシュデータカラム名
     * @param string キャッシュタイムスタンプカラム名
     */
    public static function initialize(SyL_DBAbstract $db, $table, $id, $data, $timestamp)
    {
        static $session = null;
        if (isset($_SESSION)) {
            throw new SyL_HttpSessionStartedException('session started');
        }

        if ($session == null) {
            $classname = __CLASS__;
            $session = new $classname($db, $table, $id, $data, $timestamp);
        }
    }

    /**
     * セッション開始イベント
     *
     * @param string セッション保存パス
     * @param string セッション名
     * @return bool true
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * セッション読み込みイベント
     *
     * @param string セッションID
     * @return string セッションデータ
     */
    public function read($session_id)
    {
        $sql  = "";
        $sql .= "SELECT ";
        $sql .=   "{$this->data} ";
        $sql .= "FROM ";
        $sql .=   "{$this->table} ";
        $sql .= "WHERE ";
        $sql .=   "{$this->id} = '$session_id' and ";
        $sql .=   "{$this->timestamp} >= " . time() . " ";

        $result = $this->db->queryOne($sql);

        $sql  = "";
        if ($result) {
            $sql .= "UPDATE ";
            $sql .=   "{$this->table} ";
            $sql .= "SET ";
            $sql .=   "{$this->timestamp} = " . (time() + session_cache_expire()) . " ";
            $sql .= "WHERE ";
            $sql .=   "{$this->id} = '{$session_id}' ";
        } else {
            $sql .= "INSERT INTO {$this->table} (";
            $sql .=   "{$this->id}, ";
            $sql .=   "{$this->timestamp}, ";
            $sql .=   "{$this->data} ";
            $sql .= ") VALUES ( ";
            $sql .=   "'{$session_id}', ";
            $sql .=   " " . (time() + session_cache_expire()) . ", ";
            $sql .=   "NULL ";
            $sql .= ") ";
            $result = '';
        }
        $this->db->exec($sql);

        return $result;
    }

    /**
     * セッション書き込みイベント
     *
     * @param string セッションID
     * @param string セッションデータ
     * @return bool true
     */
    public function write($session_id, $session_data)
    {
        return true;
    }

    /**
     * セッション終了イベント
     *
     * @return bool true
     */
    public function close()
    {
        return true;
    }

    /**
     * セッション削除イベント
     *
     * @param string セッションID
     * @return bool true
     */
    public function destroy($session_id)
    {
        $sql  = "";
        $sql .= "DELETE FROM  ";
        $sql .=   "{$this->table} ";
        $sql .= "WHERE ";
        $sql .=   "{$this->id} = '$session_id'";
        $this->db->exec($sql);
        return true;
    }

    /**
     * ガベージコレクタイベント
     *
     * @param int セッション保持時間
     * @return bool true
     */
    public function gc($life_time)
    {
        $sql  = "";
        $sql .= "DELETE FROM  ";
        $sql .=   "{$this->table} ";
        $sql .= "WHERE ";
        $sql .=   "{$this->timestamp} < " . time() . " ";
        $this->db->exec($sql);
        return true;
    }
}
