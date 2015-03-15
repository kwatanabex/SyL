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

/**
 * DAO関連条件クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbDaoTableRelations
{
    /**
     * 条件配列
     * 
     * @var array
     */
    private $relations = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * addInnerJoinメソッドのエイリアス
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト1
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト2
     * @param array 関連配列
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function addJoin(SyL_DbDaoTableAbstract $table1, SyL_DbDaoTableAbstract $table2, $columns, SyL_DbDaoTableConditions $condition=null)
    {
        $this->addInnerJoin($table1, $table2, $columns, $condition);
    }

    /**
     * 結合条件を作成する（等価結合）
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト1
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト2
     * @param array 関連配列
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function addInnerJoin(SyL_DbDaoTableAbstract $table1, SyL_DbDaoTableAbstract $table2, $columns, SyL_DbDaoTableConditions $condition=null)
    {
        $this->add($table1, $table2, '=', $columns, $condition);
    }

    /**
     * 結合条件を作成する（左外部結合）
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト1
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト2
     * @param array 関連配列
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function addLeftJoin(SyL_DbDaoTableAbstract $table1, SyL_DbDaoTableAbstract $table2, $columns, SyL_DbDaoTableConditions $condition=null)
    {
        $this->add($table1, $table2, '+', $columns, $condition);
    }

    /**
     * 結合条件を作成する
     *
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト1
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト2
     * @param string 演算子
     * @param array 関連配列
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    private function add(SyL_DbDaoTableAbstract $table1, SyL_DbDaoTableAbstract $table2, $operator, $columns, SyL_DbDaoTableConditions $condition=null)
    {
        switch ($operator) {
        case '=':
        case '+':
            break;
        default:
            throw new SyL_InvalidParameterException("not supported operator ({$operator})");
        }
        $this->relations[] = array(&$table1, &$table2, $operator, $columns, &$condition);
    }

    /**
     * 結合条件文を作成する
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @return string SQL結合条件文
     */
    public function create(SyL_DbAbstract $conn)
    {
        $from = '';
        $first = true;
        $aliases = array();
        foreach ($this->relations as &$relation) {
            list($table1, $table2, $operator, $columns, $condition) = $relation;
            $table_name1  = $table1->getName();
            $table_name2  = $table2->getName();
            $table_alias1 = $table1->getAliasName(true);
            $table_alias2 = $table2->getAliasName(true);
            if ($first) {
                $from .= $table_name1 . ' ';
                if ($table_name1 != $table_alias1) {
                    $from .= $table_alias1 . ' ';
                }
                $aliases[] = $table_alias1;
                $first = false;
            }

            // 既に結合したテーブルを検索する
            $join1 = $join2 = false;
            $join_alias = '';
            if (array_search($table_alias1, $aliases) !== false) {
                $join_alias = $table_alias2;
                $join1 = true;
            }
            if (array_search($table_alias2, $aliases) !== false) {
                $join_alias = $table_alias1;
                $join2 = true;
            }

            if ($join1 && $join2) {
                // 既に結合済みのテーブルはスキップ
                continue;
            }

            switch ($operator) {
            case '=':
                $from .= 'INNER JOIN ';
                break;
            case '+':
                $from .= 'LEFT OUTER JOIN ';
                break;
            default:
                throw new SyL_InvalidParameterException("not supported operator ({$operator})");
            }

            if ($join_alias == $table_alias1) {
                $from .= $table_name1 . ' ';
                if ($table_name1 != $table_alias1) {
                    $from .= $table_alias1 . ' ';
                }
                $aliases[] = $table_alias1;
            } else if ($join_alias == $table_alias2) {
                $from .= $table_name2 . ' ';
                if ($table_name2 != $table_alias2) {
                    $from .= $table_alias2 . ' ';
                }
                $aliases[] = $table_alias2;
            }

            $join_columns = array();
            foreach ($columns as $column) {
                $tmp = array_map('trim', explode(',', $column));
                $column_name1 = $table_alias1 . '.' . $tmp[0];
                $column_name2 = $table_alias2 . '.' . ((count($tmp) > 1) ? $tmp[1] : $tmp[0]);
                $join_columns[] = $column_name1 . ' = ' . $column_name2;
            }
            $from .= 'ON ' . implode(' AND ', $join_columns) . ' ';

            if ($condition != null) {
                $from .= ' AND ' . $condition->create($conn) . ' ';
            }
        }

        return $from;
    }
}
