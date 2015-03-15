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
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** DBクラス */
require_once 'SyL_DbAbstract.php';
/** DAOテーブルクラス */
require_once 'SyL_DbDaoTableAbstract.php';
/** DAOテーブル条件クラス */
require_once 'SyL_DbDaoTableConditions.php';
/** DAO関連条件クラス */
require_once 'SyL_DbDaoTableRelations.php';

/**
 * DAOクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbDao
{
    /**
     * DBオブジェクト
     *
     * @var SyL_DbAbstract
     */
    private $db = null;

    /**
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     */
    public function __construct(SyL_DbAbstract $db)
    {
        $this->db = $db;
    }

    /**
     * DAOテーブル条件オブジェクトを作成する
     *
     * @return SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function createCondition()
    {
        return new SyL_DbDaoTableConditions();
    }

    /**
     * DAO関連条件オブジェクト作成を作成する
     *
     * @return SyL_DbDaoTableRelations DAO関連条件オブジェクト
     */
    public function createRelation()
    {
        return new SyL_DbDaoTableRelations();
    }

    /**
     * ページ表示パラメータをセット
     *
     * @param int 1ページの表示件数
     * @param int 表示対象ページ数 
     * @return SyL_Pager ページオブジェクト
     */
    public function getPager($count, $page=1)
    {
        return $this->db->getPager($count, $page);
    }

    /**
     * DBからデータを取得する
     *
     * @param array テーブルオブジェクト 
     * @param SyL_DbDaoTableRelations DAO関連条件オブジェクト
     * @param SyL_Pager ページオブジェクト
     * @return array 取得データ
     */
    public function select(array $tables, SyL_DbDaoTableRelations $relation=null, SyL_Pager $pager=null)
    {
        $sql = $this->createSelectSql($tables, $relation);
        return $this->db->query($sql, $pager);
    }

    /**
     * SQL実行しファイルストリームに書き込む
     *
     * @param resource ファイルストリーム
     * @param array テーブルオブジェクト 
     * @param SyL_DbDaoTableRelations DAO関連条件オブジェクト
     * @param string 区切り文字
     * @param string 囲む文字
     */
    public function writeStreamCsv(&$stream, array $tables, SyL_DbDaoTableRelations $relation=null, $delimiter=',', $enclosure='"')
    {
        $sql = $this->createSelectSql($tables, $relation);
        $this->db->writeStreamCsv($stream, $sql, $delimiter, $enclosure);
    }

    /**
     * SELECT SQL文 を作成する
     *
     * @param array テーブルオブジェクト 
     * @param SyL_DbDaoTableRelations DAO関連条件オブジェクト
     * @return string SELECT SQL文
     */
    private function createSelectSql(array $tables, SyL_DbDaoTableRelations $relation=null)
    {
        $columns = array();
        $selects = array();
        $froms   = array();
        $where   = null;
        $groups  = array();
        $sorts   = array();
        $this->select_headers = array();
        foreach ($tables as &$table) {
            $selects = array_merge($selects, $table->getSelectColumns($columns));
            $froms[] = $table->getName(true);
            $where   = $this->createWhere($table->getConditions(), $table, $where);
            $groups  = array_merge($groups, $table->getGroupColumns());
            $sorts   = array_merge($sorts, $table->getSortColumns());
            // 同一名カラム統一用の配列
            $columns = array_merge($columns, $table->getColumnNames(true));
        }

        $from = '';
        if ($relation != null) {
            $join = $relation->create($this->db);
            if ($join) {
                $from = $join;
            }
        }
        if (!$from) {
            $from = implode(', ', $froms);
        }
        $group_by = implode(', ', $groups);
        $order_by = implode(', ', $sorts);

        $sql  = "";
        $sql .= "SELECT ";
        if (count($selects) > 0) {
        $sql .=   implode(', ', $selects) . " ";
        } else {
        $sql .=   "* ";
        }
        $sql .= "FROM ";
        $sql .=   $from . " ";
        if ($where) {
        $sql .= "WHERE ";
        $sql .=   $where . " ";
        }
        if ($group_by) {
        $sql .= "GROUP BY ";
        $sql .=   $group_by . " ";
        }
        if ($order_by) {
        $sql .= "ORDER BY ";
        $sql .=   $order_by . " ";
        }

        return $sql;
    }

    /**
     * テーブルに登録する
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @return int 影響件数
     */
    public function insert(SyL_DbDaoTableAbstract $table)
    {
        return $this->exec($table, 'insert');
    }

    /**
     * テーブルを更新する
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @return int 影響件数
     */
    public function update(SyL_DbDaoTableAbstract $table)
    {
        return $this->exec($table, 'update');
    }

    /**
     * テーブルを削除する
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @return int 影響件数
     */
    public function delete(SyL_DbDaoTableAbstract $table)
    {
        return $this->exec($table, 'delete');
    }

    /**
     * DML系SQLを実行する
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @param string insert or update or delete
     * @return int 影響件数
     */
    private function exec(SyL_DbDaoTableAbstract $table, $action)
    {
        $conditions = $table->getConditions();
        foreach ($conditions as &$condition) {
            // DML系は別名無効
            $condition->setAlias(null);
        }
        $where  = $this->createWhere($conditions, $table);
        return $this->db->execPerform($table->getName(), $table->getDataColumns(), $action, $where);
    }

    /**
     * SQL条件を作成する
     *
     * @param array DAOテーブル条件オブジェクトの配列
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @param string 直前のSQL条件
     * @return string SQL条件
     */
    private function createWhere(array $conditions, SyL_DbDaoTableAbstract $table, $where='')
    {
        if (count($conditions) == 0) {
            return $where;
        }

        foreach ($conditions as &$condition) {
            $tmp = $condition->create($this->db, $table);
            if ($tmp) {
                if ($where) {
                    $where .= $condition->getOriginalCondition();
                }
                $where .= ' ' . $tmp;
            }
        }
        return $where;
    }

    /**
     * DBオブジェクトを取得する
     *
     * @return SyL_DbAbstract DBオブジェクト
     */
    public function getDB()
    {
        return $this->db;
    }
}
