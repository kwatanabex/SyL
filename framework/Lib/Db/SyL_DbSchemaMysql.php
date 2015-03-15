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
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * DBスキーマ取得クラス (MySQL)
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbSchemaMysql extends SyL_DbSchemaAbstract
{
    /**
     * 接続しているDBに対するテーブル一覧を取得する
     *
     * 取得できるテーブル一覧配列は以下の形式
     * array (
     *  [0] => array (
     *           'name' => テーブル名,
     *           'schema' => スキーマ名,
     *           'owner' => null,
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
     *           'owner' => null,
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
        $result = array();
        $version = $this->db->getVersion();
        if ($version && version_compare($version, '5.0', '>=')) {
            $sql  = "";
            $sql .= "SELECT ";
            $sql .=   "TABLE_SCHEMA, ";
            $sql .=   "TABLE_NAME, ";
            $sql .=   "TABLE_TYPE ";
            $sql .= "FROM ";
            $sql .=   "INFORMATION_SCHEMA.TABLES ";
            $sql .= "WHERE ";
            $sql .=   "TABLE_TYPE = '" . ($view ? 'VIEW' : 'BASE TABLE') . "' ";
            $sql .= "ORDER BY ";
            $sql .=   "TABLE_NAME ";
            foreach ($this->db->query($sql) as $table) {
                $result[] = array(
                  'name'   => $table->TABLE_NAME,
                  'schema' => $table->TABLE_SCHEMA,
                  'owner'  => null
                );
            }
        } else {
            if (!$view) {
                $sql = "SHOW TABLES";
                foreach ($this->db->query($sql) as $table) {
                    foreach ($table as $t) {
                        $result[] = array(
                          'name'   => $t,
                          'schema' => null,
                          'owner'  => null
                        );
                    }
                }
            }
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
     *   S  - 文字列
     *   I  - 整数
     *   N  - 固定小数点
     *   F  - 浮動小数点数
     *   DT - 日時
     *   D  - 日付
     *   T  - 時間
     *
     * MySQL での簡易カラム型の対応は下記
     * S 文字列
     *   CHAR(M)
     *   VARCHAR(M) 
     *   TINYBLOB 
     *   TINYTEXT 
     *   BLOB
     *   TEXT
     *   MEDIUMBLOB 
     *   MEDIUMTEXT 
     *   LONGBLOB 
     *   LONGTEXT 
     * I 整数
     *   TINYINT [UNSIGNED]
     *   SMALLINT [UNSIGNED]
     *   MEDIUMINT [UNSIGNED] 
     *   INT [UNSIGNED]
     *   BIGINT [UNSIGNED]
     *   YEAR[(2|4)]
     * N 固定小数点
     *   FLOAT(M,D) [UNSIGNED] 
     *   DOUBLE(M,D) [UNSIGNED]
     *   DECIMAL[(M[,D])] [UNSIGNED]
     * F 浮動小数点数
     *   FLOAT [UNSIGNED] 
     *   DOUBLE [UNSIGNED]
     * DT 日時
     *   DATETIME
     *   TIMESTAMP 
     * D 日付
     *   DATE
     * T 時間
     *   TIME
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
        $sql = "SHOW COLUMNS FROM {$name}";

        $result = array();
        foreach ($this->db->query($sql) as $column) {
            list($simple_type, $min, $max) = $this->getFormat($column->TYPE);
            $result[$column->FIELD] = array(
              'type'        => $column->TYPE,
              'simple_type' => $simple_type,
              'min'         => $min,
              'max'         => $max,
              'not_null'    => ($column->NULL == 'NO'),
              'default'     => ((($column->DEFAULT !== null) && ($column->DEFAULT !== '')) || (stripos($column->EXTRA, 'auto_increment') !== false))
            );
        }
        if (count($result) == 0) {
            throw new SyL_DbTableNotFoundException("table not found ({$name}");
        }
        return $result;
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
        $sql = "SHOW COLUMNS FROM {$name}";

        $result = array();
        foreach ($this->db->query($sql) as $column) {
            if ($column->KEY == 'PRI') {
                $result[] = $column->FIELD;
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
        $sql = "SHOW CREATE TABLE {$name}";

        $create_table = '';
        foreach ($this->db->queryRecord($sql) as $name => $value) {
            if ($name == 'CREATE TABLE') {
                $create_table = $value;
                break;
            }
        }

        $result = array();
        $reg = '/UNIQUE KEY .+ \(([^\)]+)\)/i';
        if (preg_match_all($reg, $create_table, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tmps = array();
                foreach (explode(',', $match[1]) as $tmp) {
                    if (preg_match('/^`(.+)`$/', trim($tmp), $unique)) {
                        $tmps[] = $unique[1];
                    }
                }
                if (count($tmps) > 0) {
                    $result[] = $tmps;
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
        $sql = "SHOW CREATE TABLE {$name}";

        $create_table = '';
        foreach ($this->db->queryRecord($sql) as $name => $value) {
            if ($name == 'CREATE TABLE') {
                $create_table = $value;
                break;
            }
        }

        $result = array();
        $reg = '/FOREIGN KEY \(([^\)]+)\) REFERENCES ([^ ]+) \(([^\)]+)\)/i';
        if (preg_match_all($reg, $create_table, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $columns  = array();
                $ftable   = '';
                $fcolumns = array();
                foreach (explode(',', $match[1]) as $tmp) {
                    if (preg_match('/^`(.+)`$/', trim($tmp), $matches2)) {
                        $columns[] = $matches2[1];
                    }
                }
                if (preg_match('/^`(.+)`$/', $match[2], $matches2)) {
                    $ftable = $matches2[1];
                } else {
                    $ftable = $match[2];
                }
                foreach (explode(',', $match[3]) as $tmp) {
                    if (preg_match('/^`(.+)`$/', trim($tmp), $matches2)) {
                        $fcolumns[] = $matches2[1];
                    }
                }
                if ((count($columns) > 0) && (count($columns) == count($fcolumns))) {
                    $tmps = array();
                    foreach (array_map(null, $columns, $fcolumns) as $tmp) {
                        $tmps[$tmp[0]] = $tmp[1];
                    }
                    $result[$ftable] = $tmps;
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
        $sql = "SHOW COLUMNS FROM {$name}";
        $result = null;
        foreach ($this->db->query($sql) as $column) {
            if ($column->EXTRA == 'auto_increment') {
                $result = $column->FIELD;
                break;
            }
        }
        return $result;
    }

    /**
     * カラム型に対する情報を取得する
     *
     * @param string カラム型
     * @return array 概要形式
     */
    private function getFormat($type)
    {
        $type = explode(' ', strtolower($type));
        $option = isset($type[1]) ? $type[1] : null;
        $type = $type[0];
        $length = null;
        if (preg_match ('/(.+)\((.+)\)/', $type, $matches)) {
            $type = $matches[1];
            $length = $matches[2];
        }

        $format = array();
        switch ($type) {
        case 'tinyint':
            if ($option == 'unsigned') {
                $format = array('I', '0', '255');
            } else {
                $format = array('I', '-128', '127');
            }
            break;
        case 'smallint':
            if ($option == 'unsigned') {
                $format = array('I', '0', '65535');
            } else {
                $format = array('I', '-32768', '32767');
            }
            break;
        case 'mediumint':
            if ($option == 'unsigned') {
                $format = array('I', '0', '16777215');
            } else {
                $format = array('I', '-8388608', '8388607');
            }
            break;
        case 'int':
            if ($option == 'unsigned') {
                $format = array('I', '0', '4294967295');
            } else {
                $format = array('I', '-2147483648', '2147483647');
            }
            break;
        case 'bigint':
            if ($option == 'unsigned') {
                $format = array('I', '0', '18446744073709551615');
            } else {
                $format = array('I', '-9223372036854775808', '9223372036854775807');
            }
            break;
        case 'decimal':
        case 'float':
        case 'double':
            if ($length) {
                $length = explode(',', $length, 2);
                $i = '0';
                $f = '';
                if (isset($length[1])) {
                    $i = str_repeat('9', $length[0] - $length[1]);
                    $f = str_repeat('9', $length[1]);
                    if (!$f) {
                        $f = '0';
                    }
                } else {
                    $i = str_repeat('9', $length[0]);
                }
                if ($option == 'unsigned') {
                    $format = array('N', '0.0', "{$i}.{$f}");
                } else {
                    $format = array('N', "-{$i}.{$f}", "{$i}.{$f}");
                }
            } else {
                if ($option == 'unsigned') {
                    $format = array('F', '0.0', null);
                } else {
                    $format = array('F', null, null);
                }
            }
            break;
        case 'datetime':
            $format = array('DT', '1000-01-01 00:00:00', '9999-12-31 23:59:59');
            break;
        case 'timestamp':
            $format = array('DT', '1970-01-01 00:00:00', '2036-12-31 23:59:59');
            break;
        case 'date':
            $format = array('D', '1000-01-01', '9999-12-31');
            break;
        case 'time':
            $format = array('T', '00:00:00', '23:59:59');
            break;
        case 'year':
            if ($length == '2') {
                $format = array('I', '1', '99');
            } else {
                $format = array('I', '1901', '2155');
            }
            break;
        case 'char':
             $format = array('S', '0', $length);
            break;
        case 'varchar':
            $format = array('S', '0', $length);
            break;
        case 'tinyblob':
        case 'tinytext':
            $format = array('S', '0', '255');
            break;
        case 'blob':
        case 'text':
            $format = array('S', '0', '65535');
            break;
        case 'mediumblob':
        case 'mediumtext':
            $format = array('S', '0', '16777215');
            break;
        case 'longblob':
        case 'longtext':
            $format = array('S', '0', '4294967295');
            break;
        default:
            $format = array('S', '0', null);
            break;
        }

        return $format;
    }

}
