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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * DAOテーブルクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_DbDaoTableAbstract
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected $table = '';
    /**
     * テーブル別名
     * 
     * @var string
     */
    protected $alias = '';
    /**
     * プライマリキーカラム
     * 
     * @var array
     */
    protected $primary = array(
/*
      'ELEMENT_ID'
*/
);
    /**
     * ユニークキーカラム
     * 
     * @var array
     */
    protected $uniques = array(
/*
      [0] => array(
          'COLUMN1',
          'COLUMN2'
      ),
*/
);
    /**
     * 外部キーカラム
     * 
     * @var array
     */
    protected $foreigns = array(
/*
      'STOCK' => array(
          'STOCK_CODE' => 'STOCK_CODE',
      ),
*/
);
    /**
     * カラム定義
     *
     * @var array
     */
    protected $columns = array();

    /**
     * 対象カラム値
     * 
     * @var array
     */
    private $data_columns = array();
    /**
     * 対象カラムのSQL関数
     * 
     * @var array
     */
    private $data_functions = array();
    /**
     * 条件オブジェクト
     * 
     * @var array
     */
    private $conditions = array();
    /**
     * ソートカラム
     * 
     * @var array
     */
    protected $sorts = array();
    /**
     * グループ化カラム
     * 
     * @var array
     */
    protected $groups = array();

    /**
     * コンストラクタ
     *
     * @throws SyL_InvalidParameterException テーブルやカラムが定義されていない場合
     */
    public function __construct()
    {
        if (!$this->table) {
            throw new SyL_InvalidParameterException('table not defined');
        }
        if (count($this->columns) == 0) {
            throw new SyL_InvalidParameterException('columns not defined');
        }
/*
        $this->table   = strtoupper($this->table);
        $this->primary = array_map('strtoupper', $this->primary);
*/
        $this->columns = array_change_key_case($this->columns, CASE_UPPER);
    }

    /**
     * カラム値を追加する（オーバーロード）
     *
     * @param string カラム名
     * @param mixed カラム値
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * カラム値を追加する
     *
     * value は、select ならカラム別名、その他は登録／更新データとなる。
     *
     * @param string カラム名
     * @param mixed カラム値
     * @param array SQL関数
     * @throws SyL_InvalidParameterException カラムが定義されていない場合
     */
    public function set($name, $value=null, $function=array())
    {
        $name = strtoupper($name);
        if (!array_key_exists($name, $this->columns)) {
            throw new SyL_InvalidParameterException("`{$name}' column not defined");
        }
        if (count($function) > 0) {
            $this->data_functions[$name] = $function;
        }
        $this->data_columns[$name] = $value;
    }

    /**
     * テーブル名を取得する
     *
     * @param bool 別名追加フラグ
     * @return string テーブル名
     */
    public function getName($is_alias=false)
    {
        if ($is_alias && $this->alias) {
            return $this->table . ' ' . $this->alias;
        } else {
            return $this->table;
        }
    }

    /**
     * テーブル別名をセットする
     *
     * @param string テーブル別名
     */
    public function setAliasName($alias)
    {
        $this->alias = $alias;
    }

    /**
     * テーブル別名を取得する
     *
     * @param bool 別名が存在しない場合、テーブル名を返すフラグ
     * @return string テーブル別名
     */
    public function getAliasName($if_not_table=false)
    {
        if ($this->alias) {
            return $this->alias;
        } else {
            return $if_not_table ? $this->table : null;
        }
    }

    /**
     * 全てのカラム名を取得する
     *
     * @param bool SELECTカラム
     * @return array 全てのカラム名
     */
    public function getColumnNames($select=false)
    {
        if ($select && (count($this->data_columns) > 0)) {
            return array_keys($this->data_columns);
        } else {
            return array_keys($this->columns);
        }
    }

    /**
     * カラム定義を取得する
     *
     * @return array カラム定義
     */
    public function getColumnSchema($name)
    {
        if (!isset($this->columns[$name])) {
            throw new SyL_KeyNotFoundException("column not defined ({$name})");
        }
        return $this->columns[$name];
    }

    /**
     * DAOカラム定義上の値にキャストする
     *
     * @param string カラム名
     * @param mixed カラム前の値
     * @return mixed キャスト後の値
     */
    public function castValue($name, $value)
    {
        if ($value === '') {
            $value = null;
        }

        $schema = $this->getColumnSchema($name);
        switch ($schema['type']) {
        case 'I':
            // 整数型
            if ($value !== null) {
                $value = (int)$value;
            }
            break;
        case 'F':
        case 'N':
            // 浮動小数点型
            // 桁数固定数値型
            if ($value !== null) {
                $value = (float)$value;
            }
            break;
        case 'D':
            // 日付型
            if ($value !== null) {
                $value = ($value instanceof DateTime) ? $value->format('Y-m-d') : (string)$value;
            }
            break;
        case 'DT':
            // 日時型
            if ($value !== null) {
                $value = ($value instanceof DateTime) ? $value->format('Y-m-d H:i:s') : (string)$value;
            }
            break;
        case 'T':
            // 時間型
            if ($value !== null) {
                $value = ($value instanceof DateTime) ? $value->format('H:i:s') : (string)$value;
            }
            break;
        case 'S':
        case 'M':
            // 文字列型（バイト）
            // 文字列型（文字長）
            if ($value !== null) {
                $value = (string)$value;
            }
            break;
        default:
            // 未定義型
            throw new SyL_InvalidParameterException('undefined column type (' + $schema['type'] + ')');
        }

        return $value;
    }

    /**
     * 主キーを取得する
     *
     * @param bool 別名追加フラグ
     * @return array 主キー
     */
    public function getPrimary($is_alias=false)
    {
        if ($is_alias) {
            $alias = $this->getAliasName(true);
            $primary = array();
            foreach ($this->primary as $column) {
                $primary[] = "{$alias}.{$column}";
            }
            return $primary;
        } else {
            return $this->primary;
        }
    }

    /**
     * 一意キーを取得する
     *
     * @param bool 別名追加フラグ
     * @return array 一意キー
     */
    public function getUniques($is_alias=false)
    {
        if ($is_alias) {
            $alias = $this->getAliasName(true);
            $uniques = array();
            foreach ($this->uniques as $unique) {
                $tmp = array();
                foreach ($unique as $column) {
                    $tmp[] = "{$alias}.{$column}";
                }
                $uniques[] = $tmp;
            }
            return $uniques;
        } else {
            return $this->uniques;
        }
    }

    /**
     * 外部キーを取得する
     *
     * @param bool 別名追加フラグ
     * @return array 外部キー
     */
    public function getForeigns($is_alias=false)
    {
        if ($is_alias) {
            $alias = $this->getAliasName(true);
            $foreigns = array();
            foreach ($this->foreigns as $name => $foreign) {
                $tmp = array();
                foreach ($foreign as $column1 => $column2) {
                    $tmp["{$alias}.{$column1}"] = $column2;
                }
                $foreigns[$name] = $tmp;
            }
            return $foreigns;
        } else {
            return $this->foreigns;
        }
    }

    /**
     * DAOテーブル条件オブジェクトをセットする
     *
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function addCondition(SyL_DbDaoTableConditions $condition)
    {
        $condition->setAlias($this->getAliasName(true));
        $this->conditions[] = $condition;
    }

    /**
     * DAOテーブル条件オブジェクトを取得
     *
     * @return array DAOテーブル条件オブジェクト
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * ソートカラムを追加する
     *
     * @param string ソートカラム
     * @param bool ソート順
     * @throws SyL_InvalidParameterException カラムが定義されていない場合
     */
    public function addSortColumn($name, $asc=true)
    {
        $name = strtoupper($name);
        if (array_key_exists($name, $this->columns)) {
            $this->sorts[] = array($name, $asc, false);
        } else if (array_search($name, $this->data_columns) !== false) {
            $this->sorts[] = array($name, $asc, true);
        } else {
            throw new SyL_InvalidParameterException("`{$name}' column not defined");
        }
    }

    /**
     * ソートカラムを取得する
     *
     * @param bool 主キーソート追加フラグ
     * @return array ソートカラム
     */
    public function getSortColumns()
    {
        $result = array();
        foreach ($this->sorts as $sort) {
            $asc = ($sort[1]) ? 'ASC' : 'DESC';
            if ($sort[2]) {
                $result[] = $sort[0] . ' ' . $asc;
            } else {
                $result[] = $this->getAliasName(true) . '.' . $sort[0] . ' ' . $asc;
            }
        }
        return $result;
    }

    /**
     * グループ化カラムを追加する
     *
     * @param string グループ化カラム
     * @throws SyL_InvalidParameterException カラムが定義されていない場合
     */
    public function addGroupColumn($name)
    {
        $name = strtoupper($name);
        if (array_key_exists($name, $this->columns)) {
            $this->groups[] = array($name, false);
        } else if (array_search($name, $this->data_columns) !== false) {
            $this->groups[] = array($name, true);
        } else {
            throw new SyL_InvalidParameterException("`{$name}' column not defined");
        }
    }

    /**
     * グループ化カラムを取得する
     *
     * @return array グループ化カラム
     */
    public function getGroupColumns()
    {
        $result = array();
        foreach ($this->groups as $group) {
            if ($group[1]) {
                $result[] = $group[0];
            } else {
                $result[] = $this->getAliasName(true) . '.' . $group[0];
            }
        }
        return $result;
    }

    /**
     * SELECT SQL用の項目を取得する
     *
     * @param array 除外カラム
     * @return array SELECT SQL用の項目
     */
    public function getSelectColumns(array $exclude_columns=array())
    {
        $names = array();
        if (count($this->data_columns) > 0) {
            $names = array_keys($this->data_columns);
        } else {
            $names = array_keys($this->columns);
        }

        $names = array_diff($names, $exclude_columns);

        $columns = array();
        foreach ($names as $name) {
            $column_tmp = '';
            if (isset($this->data_functions[$name])) {
                $funcs    = $this->data_functions[$name];
                $funcname = array_shift($funcs);
                $func = implode(',', $funcs);
                if ($func) {
                    $func = ',' . $func;
                }
                $column_tmp = $funcname . '(' . $this->getAliasName(true) . '.' . $name . $func . ')';
            } else {
                $column_tmp = $this->getAliasName(true) . '.' . $name;
            }
            if (isset($this->data_columns[$name])) {
                $column_tmp .= ' AS ' . $this->data_columns[$name];
            }
            $columns[] = $column_tmp;
        }
        return $columns;
    }

    /**
     * DML用の項目を取得する
     *
     * @param bool テーブル別名追加フラグ
     * @return array DML用の項目
     */
    public function getDataColumns($is_alias=false)
    {
        $columns = array();
        foreach ($this->data_columns as $name => $value) {
            if (isset($this->data_functions[$name])) {
                $funcs    = $this->data_functions[$name];
                $funcname = array_shift($funcs);
                $funcs    = array_map(array($this, 'castValue'), $funcs);
                $funcs    = array_map(array($conn, 'quote'), $funcs);
                $func     = implode(',', $funcs);
                $value    = "{$funcname}({$func})";
            } else {
                $value = $this->castValue($name, $value);
            }

            if ($is_alias && $this->alias) {
                $columns[$this->getAliasName(true) . '.' . $name] = $value;
            } else {
                $columns[$name] = $value;
            }
        }
        return $columns;
    }
}
