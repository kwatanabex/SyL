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
 * DAOテーブル条件クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbDaoTableConditions
{
    /**
     * テーブル別名
     * 
     * @var string
     */
    private $alias = null;
    /**
     * 条件配列
     * 
     * @var array
     */
    private $wheres = array();
    /**
     * 他の条件オブジェクトとの結合条件
     * 
     * @var array
     */
    private $original_condition = 'AND';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * テーブル別名をセットする
     *
     * @param string テーブル別名
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * 他の条件オブジェクトとの結合条件をセットする
     *
     * @param bool true: AND, false: OR
     */
    public function setOriginalCondition($and)
    {
        $this->original_condition = $and ? 'AND' : 'OR';
    }

    /**
     * 他の条件オブジェクトとの結合条件を取得する
     *
     * @return string 他の条件オブジェクトとの結合条件
     */
    public function getOriginalCondition()
    {
        return $this->original_condition;
    }

    /**
     * where条件を作成する（等価比較）
     *
     * @param string カラム名
     * @param string 値
     * @param bool true: =, false: <>
     * @param bool true: AND, false: OR
     */
    public function addEqual($name, $value, $equal=true, $and=true)
    {
        $op = ($equal) ? '=' : '<>';
        $this->add($name, $value, $op, $and);
    }

    /**
     * where条件を作成する（greater than比較）
     *
     * @param string カラム名
     * @param string 値
     * @param bool true: >=, false: >
     * @param bool true: AND, false: OR
     */
    public function addGt($name, $value, $equal=true, $and=true)
    {
        $op = ($equal) ? '>=' : '>';
        $this->add($name, $value, $op, $and);
    }

    /**
     * where条件を作成する（less than比較）
     *
     * @param string カラム名
     * @param string 値
     * @param bool true: <=, false: <
     * @param bool true: AND, false: OR
     */
    public function addLt($name, $value, $equal=true, $and=true)
    {
        $op = ($equal) ? '<=' : '<';
        $this->add($name, $value, $op, $and);
    }

    /**
     * where条件を作成する（NULL比較）
     *
     * @param string カラム名
     * @param bool true: IS NULL, false: IS NOT NULL
     * @param bool true: AND, false: OR
     */
    public function addNull($name, $equal=true, $and=true)
    {
        $op = ($equal) ? 'IS NULL' : 'IS NOT NULL';
        $this->add($name, null, $op, $and);
    }

    /**
     * where条件を作成する（LIKE比較）
     *
     * @param string カラム名
     * @param string 値
     * @param bool true: LIKE, false: NOT LIKE
     * @param bool true: AND, false: OR
     */
    public function addLike($name, $value, $equal=true, $and=true)
    {
        $op = ($equal) ? 'LIKE' : 'NOT LIKE';
        $this->add($name, $value, $op, $and);
    }

    /**
     * where条件を作成する（IN比較）
     *
     * @param string カラム名
     * @param array 値
     * @param bool true: IN, false: NOT IN
     * @param bool true: AND, false: OR
     */
    public function addIn($name, array $value, $equal=true, $and=true)
    {
        $op = ($equal) ? 'IN' : 'NOT IN';
        $this->add($name, $value, $op, $and);
    }

    /**
     * where条件を作成する（BETWEEN比較）
     *
     * @param string カラム名
     * @param array 値
     * @param bool true: AND, false: OR
     * @throws SyL_InvalidParameterException 値パラメータが配列でない場合
     */
    public function addBetween($name, array $value, $and=true)
    {
        if (count($value) != 2) {
            throw new SyL_InvalidParameterException('array count 2 only');
        }
        $this->add($name, $value, 'BETWEEN', $and);
    }

    /**
     * where条件を作成する
     *
     * @param string カラム名
     * @param mixed 値
     * @param string 演算子
     * @param bool true: AND, false: OR
     */
    private function add($name, $value, $operator, $and)
    {
        $operator = strtoupper($operator);
        $logical  = $and ? 'AND' : 'OR';
        switch ($operator) {
        case '=':
        case '!=':
        case '<>':
        case '>':
        case '>=':
        case '<':
        case '<=':
        case 'NULL':
        case 'IS NULL':
        case 'NOT NULL':
        case 'IS NOT NULL':
        case 'LIKE':
        case 'NOT LIKE':
        case 'IN':
        case 'NOT IN':
        case 'BETWEEN':
            break;
        default:
            throw new SyL_InvalidParameterException("not supported operator ({$operator})");
        }

        $this->wheres[] = array($name, $value, $operator, $logical);
    }

    /**
     * SQL条件文を作成する
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @param SyL_DbDaoTableAbstract DAOテーブルオブジェクト
     * @return string SQL条件文
     */
    public function create(SyL_DbAbstract $db, SyL_DbDaoTableAbstract $table=null)
    {
        $wheres = array();
        $cond   = '';
        $bracket = false;

        foreach ($this->wheres as &$where) {
            list($name, $value, $operator, $logical) = $where;

            if ($table != null) {
                $name_tmp = is_array($name) ? $name[0] : $name;
                $name_tmp = strtoupper($name_tmp);
                if (is_array($value)) {
                    $value = array_map(array($table, 'castValue'), array_pad(array(), count($value), $name_tmp), $value);
                } else {
                    $value = $table->castValue($name_tmp, $value);
                }
            }

            if (is_array($name)) {
                $name[0] = strtoupper($name[0]);
                if ($this->alias) {
                    $name[0] = $this->alias . '.' . $name[0];
                }

                if (count($name) == 1) {
                    $name = $name[0];
                } else {
                    $params = implode(',', array_map(array($db, 'quote'), array_slice($name, 2)));
                    if ($params) {
                        $params = ',' . $params;
                    }
                    $name = $name[1] . '(' . $name[0] . $params . ')';
                }
            } else {
                $name = strtoupper($name);
                if ($this->alias) {
                    $name = $this->alias . '.' . $name;
                }
            }

            switch ($operator) {
            case 'IS NULL':
            case 'IS NOT NULL':
                $wheres[] = $name . ' ' . $operator;
                break;
            case 'IN':
            case 'NOT IN':
                $value = array_map(array($db, 'quote'), $value);
                $wheres[] = $name . ' ' . $operator . ' (' . implode(',', $value) . ') ';
                break;
            case 'BETWEEN':
                $value = array_map(array($db, 'quote'), $value);
                $wheres[] = $name . ' ' . $operator . ' ' . $value[0] . ' AND ' . $value[1] . ' ';
                break;
            case 'LIKE':
            case 'NOT LIKE':
            default:
                $value = $db->quote($value);
                $wheres[] = $name . ' ' . $operator . ' ' . $value . ' ';
            }

            $max = count($wheres) - 1;
            switch ($cond) {
            case 'AND':
                if ($logical != 'AND') {
                    // AND 前の条件) OR (現在の条件
                    $wheres[$max-1] .= ') ';
                    $wheres[$max] = ' ' . $logical . ' (' . $wheres[$max];
                    $bracket = true;
                } else {
                    // AND 前の条件 AND 現在の条件
                    $wheres[$max] = ' ' . $logical . ' ' . $wheres[$max];
                }
                $cond = 'AND';
                break;
            case 'OR':
                if ($logical != 'OR') {
                    // OR 前の条件) AND (現在の条件
                    $max = count($wheres) - 1;
                    $wheres[$max-1] .= ') ';
                    $wheres[$max] = ' ' . $logical . ' (' . $wheres[$max];
                    $bracket = true;
                } else {
                    // OR 前の条件 OR 現在の条件
                    $wheres[$max] = ' ' . $logical . ' ' . $wheres[$max];
                }
                $cond = 'OR';
                break;
            default:
                // 初期はAND
                $cond = 'AND';
            }
        }

        if (count($wheres) > 0) {
            if ($bracket) {
                return ' ((' . implode('', $wheres) . ')) ';
            } else {
                return ' (' . implode('', $wheres) . ') ';
            }
        } else {
            return '';
        }
    }
}
