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
 * DBスキーマ取得クラス (PostgreSQL)
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbSchemaPgsql extends SyL_DbSchemaAbstract
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
        $sql = "";
        $version = $this->db->getVersion();
        if ($version && version_compare($version, '7.4', '>=')) {
            $sql .= "SELECT ";
            $sql .=   "c.relname, ";
            $sql .=   "t.schemaname, ";
            $sql .=   "pg_get_userbyid(c.relowner) AS owner, ";
            $sql .=   "c.relkind ";
            $sql .= "FROM ";
            $sql .=   "pg_class c ";
            $sql .=   "LEFT OUTER JOIN " . ($view ? 'pg_views' : 'pg_tables') . " t ";
            $sql .=     "ON c.relname = t." . ($view ? 'viewname' : 'tablename') . " ";
            $sql .= "WHERE ";
            $sql .=   "c.relkind = '" . ($view ? 'v' : 'r') . "' AND ";
            $sql .= "t.schemaname <> 'information_schema' AND ";
            $sql .= "t.schemaname <> 'pg_catalog' ";
            $sql .= "ORDER BY ";
            $sql .=   "c.relname ";
        } else {
            $sql .= "SELECT ";
            $sql .=   "c.relname, ";
            $sql .=   "null AS schemaname, ";
            $sql .=   "pg_get_userbyid(c.relowner) AS owner, ";
            $sql .=   "c.relkind ";
            $sql .= "FROM ";
            $sql .=   "pg_class c ";
            $sql .= "WHERE ";
            $sql .=   "c.relkind IN ('r', 'v') AND ";
            $sql .=   "c.relname NOT LIKE 'pg#_%' ESCAPE '#' ";
            $sql .= "ORDER BY ";
            $sql .=   "c.relname ";
        }

        $result = array();
        foreach ($this->db->query($sql) as $table) {
            $result[] = array(
              'name'   => $table->RELNAME,
              'schema' => $table->SCHEMANAME,
              'owner'  => $table->OWNER
            );
        }
        if (count($result) == 0) {
            throw new SyL_DbTableNotFoundException("table not found ({$tablename}");
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
     * PostgreSQL での簡易カラム型の対応は下記
     * M 文字列（文字長）
     *   varchar[(n)]
     *   char[(n)]
     *   text
     * I 整数
     *   smallint,
     *   integer,
     *   bigint
     *   serial
     *   bigserial
     * N 固定小数点
     *   decimal[(M[,D])]
     *   numeric[(M[,D])]
     * F 浮動小数点数
     *   real
     *   double precision
     * DT 日時
     *   timestamp [without time zone]
     * D 日付
     *   date
     * T 時間
     *   time [without time zone]
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
        $sql  = "";
        $sql .= "SELECT ";
        $sql .=   "a.attname, ";
        $sql .=   "t.typname, ";
        $sql .=   "case t.typname ";
        $sql .=     "when 'bpchar' then atttypmod - 4 ";
        $sql .=     "when 'varchar' then atttypmod - 4 ";
        $sql .=     "when 'numeric' then (atttypmod - 4) / 65536 ";
        $sql .=     "else a.attlen ";
        $sql .=   "end as atttypmod, ";
        $sql .=   "case t.typname ";
        $sql .=     "when 'numeric' then (atttypmod - 4) % 65536 ";
        $sql .=     "when 'decimal' then (atttypmod - 4) % 65536 ";
        $sql .=     "else 0 ";
        $sql .=   "end as atttypmod1, ";
        $sql .=   "case a.attnotnull ";
        $sql .=     "when true then '1' ";
        $sql .=     "else '0' ";
        $sql .=   "end as attnotnull, ";
        $sql .=   "case a.atthasdef ";
        $sql .=     "when true then '1' ";
        $sql .=     "else '0' ";
        $sql .=   "end as atthasdef ";
        $sql .= "FROM ";
        $sql .=   "pg_class c, ";
        $sql .=   "pg_attribute a, ";
        $sql .=   "pg_type t ";
        $sql .= "WHERE ";
        $sql .=   "c.oid = a.attrelid AND ";
        $sql .=   "a.atttypid = t.oid AND ";
        $sql .=   "c.relname = '" . $this->db->escape($name) . "' AND  ";
        $sql .=   "a.attnum > 0 ";
        $sql .= "ORDER BY ";
        $sql .=   "a.attnum ";

        $result = array();
        foreach ($this->db->query($sql) as $column) {
            list($simple_type, $min, $max) = $this->getFormat($column->TYPNAME, $column->ATTTYPMOD, $column->ATTTYPMOD1);
            $result[$column->ATTNAME] = array(
              'type'        => $column->TYPNAME,
              'simple_type' => $simple_type,
              'min'         => $min,
              'max'         => $max,
              'not_null'    => ($column->ATTNOTNULL == '1'),
              'default'     => ($column->ATTHASDEF == '1')
            );
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
        $sql  = "";
        $sql .= "SELECT ";
        $sql .=   "a.attname ";
        $sql .= "FROM  ";
        $sql .=   "pg_class c, ";
        $sql .=   "pg_attribute a ";
        $sql .= "WHERE ";
        $sql .=   "c.oid = a.attrelid AND ";
        $sql .=   "exists(SELECT * FROM pg_constraint ct WHERE c.oid = ct.conrelid and a.attnum = ANY(ct.conkey) and ct.contype = 'p') AND ";
        $sql .=   "c.relname = '" . $this->db->escape($name) . "' ";
        $sql .= "ORDER BY ";
        $sql .=   "a.attnum ";

        $result = array();
        foreach ($this->db->query($sql) as $column) {
            $result[] = $column->ATTNAME;
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
        $sql  = "";
        $sql .= "SELECT ";
        $sql .=   "ct.conname, ";
        $sql .=   "a.attname ";
        $sql .= "FROM ";
        $sql .=   "pg_class c, ";
        $sql .=   "pg_constraint ct, ";
        $sql .=   "pg_attribute a ";
        $sql .= "WHERE ";
        $sql .=   "c.oid = ct.conrelid AND ";
        $sql .=   "c.oid = a.attrelid AND ";
        $sql .=   "a.attnum = any(ct.conkey) AND ";
        $sql .=   "c.relname = '" . $this->db->escape($name) . "' AND ";
        $sql .=   "ct.contype = 'u' AND ";
        $sql .=   "a.attnum > 0 ";

        $i = 0;
        $keys   = array();
        $result = array();
        foreach ($this->db->query($sql) as $column) {
            $num = '';
            if (isset($keys[$column->CONNAME])) {
                $num = $keys[$column->CONNAME];
            } else {
                $keys[$column->CONNAME] = $i;
                $result[$i] = array();
                $num = $i++;
            }
            $result[$num][] = $column->ATTNAME;
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
        $sql  = "";
        $sql .= "SELECT ";
        $sql .=   "(select c1.relname from pg_class c1 where c1.oid = ct.confrelid) as relname, ";
        $sql .=   "ct.conkey, ";
        $sql .=   "ct.confkey ";
        $sql .= "FROM ";
        $sql .=   "pg_class c, ";
        $sql .=   "pg_constraint ct ";
        $sql .= "WHERE ";
        $sql .=   "c.oid = ct.conrelid and ";
        $sql .=   "ct.contype = 'f' and ";
        $sql .=   "c.relname = '" . $this->db->escape($name) . "' ";

        $result = array();
        foreach ($this->db->query($sql) as $column) {
            $tmps = array();
            $columns  = explode(',', substr($column->CONKEY, 1, -1));
            $fcolumns = explode(',', substr($column->CONFKEY, 1, -1));
            foreach (array_map(null, $columns, $fcolumns) as $tmp) {
                $sql  = "";
                $sql .= "select ";
                $sql .= "(select a.attname from pg_class c, pg_attribute a where c.oid = a.attrelid AND c.relname = '" . $this->db->escape($name) . "' and a.attnum = {$tmp[0]}) as attname1, ";
                $sql .= "(select a.attname from pg_class c, pg_attribute a where c.oid = a.attrelid AND c.relname = '" . $this->db->escape($column->RELNAME) . "' and a.attnum = {$tmp[1]}) as attname2 ";
                $record = $this->db->queryRecord($sql);
                $tmps[$record->ATTNAME1] = $record->ATTNAME2;
            }
            $result[$column->RELNAME] = $tmps;
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
        $sql  = "";
        $sql .= "SELECT ";
        $sql .= "  a.attname ";
        $sql .= "FROM ";
        $sql .= "  pg_class cs1 ";
        $sql .= "    inner join pg_depend d  ";
        $sql .= "      on cs1.oid = d.objid ";
        $sql .= "    inner join pg_class cs2 ";
        $sql .= "      on cs2.oid = d.refobjid ";
        $sql .= "    inner join pg_attribute a  ";
        $sql .= "      on d.refobjid = a.attrelid AND ";
        $sql .= "         d.refobjsubid = a.attnum ";
        $sql .= "WHERE ";
        $sql .= "  cs1.relkind = 'S' AND ";
        $sql .= "  cs2.relname = '" . $this->db->escape($name) . "' ";

        return $this->db->queryOne($sql);
    }

    /**
     * カラム型に対する情報を取得する
     *
     * @param string カラム型
     * @param string 長さ
     * @param string 長さ（小数）
     * @return array 概要形式
     */
    private function getFormat($type, $len1, $len2)
    {
        $format = array();
        switch ($type) {
        case 'int2':
            $format = array('I', '-32768', '32767');
            break;
        case 'int4':
            $format = array('I', '-2147483648', '2147483647');
            break;
        case 'int8':
            $format = array('I', '-9223372036854775808', '9223372036854775807');
            break;
        case 'float4':
        case 'float8':
            $format = array('F', null, null);
            break;
        case 'numeric':
            if ($len1 > 0) {
                $i = '0';
                $f = '0';
                if ($len2 > 0) {
                    $i = str_repeat('9', $len1 - $len2);
                    $f = str_repeat('9', $len2);
                } else {
                    $i = str_repeat('9', $len1);
                }
                $format = array('N', "-{$i}.{$f}", "{$i}.{$f}");
            } else {
                $format = array('F', null, null);
            }
            break;
        case 'varchar':
            if ($len1 > 0) {
                $format = array('M', '0', "{$len1}");
            } else {
                $format = array('M', '0', null);
            }
            break;
        case 'bpchar':
            $format = array('M', '0', $len1);
            break;
        case 'text':
            $format = array('M', '0', null);
            break;
        case 'timestamp':
        case 'timestamptz':
            $format = array('DT', null, null);
            break;
        case 'date':
            $format = array('D', null, null);
            break;
        case 'time':
        case 'timetz':
            $format = array('T', '00:00:00', '23:59:59');
            break;
        default:
            $format = array('S', '0', null);
            break;
        }
        return $format;
    }

}
