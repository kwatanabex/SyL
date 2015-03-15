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
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * DBスキーマ取得クラス (SQLite)
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbSchemaSqlite extends SyL_DbSchemaAbstract
{
    /**
     * 接続しているDBに対するテーブル一覧を取得する
     *
     * 取得できるテーブル一覧配列は以下の形式
     * array (
     *  [0] => array (
     *           'name' => テーブル名,
     *           'schema' => スキーマ名,
     *           'owner' => オーナー名,
     *         ),
     *  [1] => ...
     * )
     *
     * @return array テーブル一覧
     */
    public function getTables()
    {
        return $this->getSchemaTables(false);
    }

    /**
     * 接続しているDBに対するビュー一覧を取得する
     *
     * 取得できるビュー一覧配列は以下の形式
     * array (
     *  [0] => array (
     *           'name' => ビュー名,
     *           'schema' => スキーマ名,
     *           'owner' => オーナー名,
     *         ),
     *  [1] => ...
     * )
     *
     * @return array ビュー一覧
     */
    public function getViews()
    {
        return $this->getSchemaTables(true);
    }

    /**
     * 接続しているDBに対するテーブル、またはビュー一覧を取得する
     *
     * @param bool ビュー取得フラグ
     * @return array テーブル、またはビュー一覧
     */
    private function getSchemaTables($view=false)
    {
        $sql = "select name from sqlite_master ";
        if ($view) {
            $sql .= "where type='view'";
        } else {
            $sql .= "where type='table'";
        }

        $result = array();
        foreach ($this->db->query($sql) as $table) {
            $result[] = array(
              'name'   => $table->NAME,
              'schema' => null,
              'owner'  => null
            );
        }

        if (count($result) == 0) {
            $schema = $view ? 'view' : 'table';
            throw new SyL_DbTableNotFoundException("{$schema} not found");
        }

        return $result;
    }

    /**
     * 指定したテーブルに対するカラム情報を取得する
     *
     * 取得できるカラム情報配列は下記の形式
     * array (
     *  [カラム名1] => array (
     *           'type' => カラム型,
     *           'simple_type' => 簡易カラム型,
     *           'min' => 最小値,
     *           'not_null' => NULL不許可,
     *           'default' => デフォルト値の有無,
     *         ),
     *  [カラム名2] => ...
     * )
     *
     * 簡易カラム型の分類は下記の形式
     *   I  - 整数
     *   S  - 文字列
     *   N  - 固定小数点
     *   F  - 浮動小数点数
     *   DT - 日時
     *   D  - 日付
     *   T  - 時間
     *
     * SQLite での簡易カラム型の対応は下記
     * S 文字列
     *   TEXT[(M)] （※「CHAR」「CLOB」「TEXT」のいづれかを含む名称）
     * I 整数
     *   INTEGER （※「INT」を含む名称）
     * N 固定小数点
     *   NUMERIC[(M[,D])]
     * F 浮動小数点数
     *   REAL （※「REAL」「FLOA」「DOUB」のいづれかを含む名称）
     *
     * ※上記以外の型はすべて文字列となる
     *
     * カラムが取得できない場合は、空の配列を返す
     *
     * @param string テーブル名
     * @return array カラム情報
     */
    public function getColumns($name)
    {
        $result = array();
        foreach ($this->getColumnLines($name) as $str) {
            $tmps = explode(' ', trim($str), 2);
            if (preg_match('/^(primary|foreign)|(unique\(?.*\)?)$/i', $tmps[0])) {
                continue;
            }
            $name = $tmps[0];
            $type = '';
            $simple_type = 'S';
            $min = '0';
            $max = null;
            $is_not_null = false;
            $default     = false;
            if (isset($tmps[1])) {
                list($type) = explode(' ', $tmps[1], 2);
                list($simple_type, $min, $max) = $this->getFormat($type);
                if (preg_match('/not ( *)null/i', $tmps[1])) {
                    $is_not_null = true;
                }
                if (preg_match('/default (.+)/i', $tmps[1])) {
                    $default = true;
                } else if (preg_match('/integer (.*)primary ( *)key/i', $tmps[1])) {
                    $default = true;
                }
            }
            $result[$name] = array(
              'type'        => $type,
              'simple_type' => $simple_type,
              'min'         => $min,
              'max'         => $max,
              'not_null'    => $is_not_null,
              'default'     => $default
            );
        }
        return $result;
    }

    /**
     * 指定したテーブルのカラム定義情報を取得する
     *
     * @param string テーブル名
     * @return array カラム定義情報
     */
    private function getColumnLines($name)
    {
        $sql = "select sql from sqlite_master where name='" . $this->db->escape($name) . "'";

        $create_sql = $this->db->queryOne($sql);

        $pos = strpos($create_sql, '(');
        $create_sql = substr($create_sql, $pos+1);
        $pos = strrpos($create_sql, ')');
        $create_sql = substr($create_sql, 0, $pos);

        $i = 0;
        $columns = array();
        foreach (preg_split("/(\([^\)]+\))|('[^']+')|(,)/i", $create_sql, null, PREG_SPLIT_DELIM_CAPTURE) as $str) {
            $str = trim($str);
            if ($str) {
                if ($str == ',') {
                    $i++;
                } else {
                    if (!isset($columns[$i])) {
                        $columns[$i] = $str;
                    } else {
                        if (preg_match('/^\([^\)]+\)/', $str, $matches)) {
                            $columns[$i] .= $str;
                        } else {
                            $columns[$i] .= ' ' . $str;
                        }
                    }
                }
            }
        }
        return $columns;
    }

    /**
     * 指定したテーブルの主キーカラムを取得する
     *
     * 取得できる主キーカラム配列は下記の形式
     * array (
     *     [0] => 主キーカラム1
     *     [1] => ...
     * )
     *
     * 主キーカラムが取得できない場合は、空の配列を返す
     *
     * @param string テーブル名
     * @return array 主キーカラム
     */
    public function getPrimaryColumns($name)
    {
        $result = array();
        foreach ($this->getColumnLines($name) as $str) {
            $tmps = explode(' ', trim($str), 2);
            if (isset($tmps[1])) {
                if (strtolower($tmps[0]) == 'primary') {
                    if (preg_match('/\(([^\)]+)\)/i', $tmps[1], $matches)) {
                        $result = array_map('trim', explode(',', $matches[1]));
                        break;
                    }
                } else {
                    if (preg_match('/primary key/i', $tmps[1])) {
                        $result[] = $tmps[0];
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 指定したテーブルの一意キーカラムを取得する
     *
     * 取得できる一意キーカラム配列は下記の形式
     * array (
     *  [0] => array (
     *          [0] => 一意キーカラム1
     *          [1] => ...
     *         ),
     *  [1] => ...
     * )
     *
     * 一意キーカラムが取得できない場合は、空の配列を返す
     *
     * @param string テーブル名
     * @return array 一意キーカラム
     */
    public function getUniqueColumns($name)
    {
        $result = array();
        foreach ($this->getColumnLines($name) as $str) {
            if (preg_match('/^unique[ ]*\(([^\)]+)\)/i', $str, $matches)) {
                $result[] = array_map('trim', explode(',', $matches[1]));
            } else {
                $tmps = explode(' ', trim($str), 2);
                if (isset($tmps[1])) {
                    if (preg_match('/unique/i', $tmps[1])) {
                        $result[] = array($tmps[0]);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 指定したテーブルの外部キーカラムを取得する
     *
     * 取得できる一意キーカラム配列は下記の形式
     * array (
     *  [外部テーブル名1] => array (
     *                       [元のカラム1] => 外部テーブルのカラム1
     *                       [元のカラム2] => ...
     *         ),
     *  [外部テーブル名2] => ...
     * )
     *
     * 外部キーカラムが取得できない場合は、空の配列を返す
     *
     * @param string テーブル名
     * @return array 外部キーカラム
     */
    public function getForeignColumns($name)
    {
        $result = array();
        foreach ($this->getColumnLines($name) as $str) {
            $tmps = explode(' ', trim($str), 2);
            if (isset($tmps[1])) {
                if (strtolower($tmps[0]) == 'foreign') {
                    if (preg_match('/key[ ]*\(([^\)]+)\)[ ]*references[ ]+([^\( ]+)[ ]*\(([^\)]+)\)/i', $tmps[1], $matches)) {
                        $table = $matches[2];
                        $columns  = array_map('trim', explode(',', $matches[1]));
                        $fcolumns = array_map('trim', explode(',', $matches[3]));
                        foreach (array_map(null, $columns, $fcolumns) as $tmps2) {
                            $result[$table][$tmps2[0]] = $tmps2[1];
                        }
                    }
                } else {
                    if (preg_match('/references[ ]+([^\( ]+)[ ]*\(([^\)]+)\)/i',  $tmps[1], $matches)) {
                        $table   = $matches[1];
                        $column  = $tmps[0];
                        $fcolumn = trim($matches[2]);
                        $result[$table][$column] = $fcolumn;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 指定したテーブルのシーケンス（自動採番）カラムを取得する
     *
     * シーケンスが取得できない場合は、NULLを返す
     *
     * @return string シーケンス（自動採番）カラム
     */
    public function getAutoIncrementColumn($name)
    {
        $result = null;
        foreach ($this->getColumnLines($name) as $str) {
            $tmps = explode(' ', trim($str), 2);
            if (isset($tmps[1])) {
                if (preg_match ('/integer[ ]+primary[ ]+key/i', $tmps[1])) {
                    $result = $tmps[0];
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * データ型に対応する SQLite データ型を取得する
     *
     * @param string カラム
     * @return string SQLite データ型
     */
    private function getFormat($type)
    {
        $format = array();
        if (preg_match('/(text|char|clob)/i', $type)) {
            // TEXT
            $option = null;
            if (preg_match('/\((.+)\)/', $type, $matches)) {
                $option = $matches[1];
            }
            $format = array('S', '0', $option);
        } else if (preg_match('/int/i', $type)) {
            // INTEGER
            $format = array('I', null, null);
        } else if (preg_match('/(real|floa|doub)/i', $type)) {
            // REAL
            $format = array('F', null, null);
        } else if (preg_match('/blob/i', $type) || ($type == '')) {
            // NONE
            $format = array('S', '0', null);
        } else if (preg_match('/numeric/i', $type)) {
            // NUMERIC
            $option = null;
            if (preg_match('/\((.+)\)/', $type, $matches)) {
                $length = explode(',', $matches[1], 2);
                $i = '0';
                $f = '0';
                if (isset($length[1])) {
                    $i = str_repeat('9', $length[0] - $length[1]);
                    $f = str_repeat('9', $length[1]);
                    if (!$f) {
                        $f = '0';
                    }
                } else {
                    $i = str_repeat('9', $length[0]);
                }
                $format = array('N', "-{$i}.{$f}", "{$i}.{$f}");
            } else {
                $format = array('F', null, null);
            }
        } else {
            // etc
            $format = array('S', '0', null);
        }
        return $format;
    }

}
