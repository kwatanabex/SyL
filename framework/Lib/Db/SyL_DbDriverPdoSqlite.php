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

/** DBクラス（PDO） */
require_once 'SyL_DbDriverPdoAbstract.php';

/**
 * DBクラス（PDO::Sqlite）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbDriverPdoSqlite extends SyL_DbDriverPdoAbstract
{
    /**
     * PDO DBに接続するための情報を取得する
     * 
     * @param string データベース名
     * @param string ユーザー名
     * @param string パスワード
     * @param string ホスト名
     * @param string ポート番号
     * @return array PDOクラスのコンストラクタ引数前3つ。array($dsn, $username, $password)
     */
    protected function getPdoConnectionInfo($database, $username, $password, $hostname, $port)
    {
        $dsn  = 'sqlite:' . $database;
        return array($dsn, null, null);
    }

    /**
     * サニタイズ（無効化）する
     * 
     * @param string サニタイズ対象文字列
     * @return string サニタイズ後文字列
     */
    public function escape($value)
    {
        return str_replace("'", "''", $value);
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * ページングオブジェクトを指定した場合は、
     * 指定ページデータのみ取得される。
     * ページングオブジェクトは、予め getPager メソッドで
     * 取得したオブジェクトを指定する。
     *
     * @param string SQL文
     * @param SyL_Pager ページングオブジェクト
     * @return array 実行結果
     */
    public function query($sql, SyL_Pager $pager=null)
    {
        $result = array();
        if ($pager != null) {
            // 件数取得SQL実行
            $sql_count = "SELECT COUNT(*) FROM ({$sql}) AS SyL$";
            $record = $this->queryOne($sql_count);
            $pager->setSum($record);
            // ページ単位レコード取得
            $sql_page = $sql . ' LIMIT ' . $pager->getPageCount() . ' OFFSET ' . ($pager->getStartRecord() - 1);
            $result = $this->query($sql_page);
        } else {
            $result = parent::query($sql);
        }

        return $result;
    }

    /**
     * 指定形式のパラメータを引数に、SQL（DML）を組み立て実行する
     *
     * クォートは型により自動で行われる。関数などクォートしたくない場合は、
     * カラム値に array(値, false) を指定する
     *
     * 例） カラムとデータ配列
     * array(
     *  'id'       => array(1, false),
     *  'name'     => $name,
     *  'address'  => $adress,
     *  'datetime' => array('current_timestamp', false),
     *  ...
     * );
     *
     * @param string テーブル名
     * @param array カラムとデータ配列
     * @param string insert or update or delete or replace
     * @param string 条件パラメータ（update, delete時のみ）
     * @return bool 実行結果 true: OK、false: エラー
     * @throws SyL_InvalidParameterException actionパラメータの指定値が insert or update or delete 以外の場合
     */
    public function execPerform($table, $columns, $action, $where='')
    {
        if (strtolower($action) == 'replace') {
            $fields = array();
            $datas  = array();
            foreach ($columns as $column => $value) {
                $fields[] = $column;
                if (is_array($value) && (count($value) == 2)) {
                    $datas[] = $value[1] ? $this->quote($value[0]) : $value[0];
                } else {
                    $datas[] = $this->quote($value);
                }
            }
            $sql = "INSERT OR REPLACE INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $datas) . ")";
            return $this->exec($sql);
        } else {
            return parent::execPerform($table, $columns, $action, $where);
        }
    }
}
