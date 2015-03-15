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
 * DB操作クラス（MySQL）
 *
 * MySQL vrersion 4.1 以上
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbDriverMysql extends SyL_DbAbstract
{
    /**
     * オートコミット
     *
     * @var bool
     */
    private static $autocommit = null;

    /**
     * DB接続を開始する
     * 
     * @param string データベース名
     * @param string ユーザー名
     * @param string パスワード
     * @param string ホスト名
     * @param string ポート番号
     * @param bool 持続的接続
     * @throws SyL_DbConnectException DB接続エラー時
     */
    public function open($database, $username, $password, $hostname, $port, $persistent)
    {
        if ($port) {
            $hostname .= ':' . $port;
        }

        try {
            $this->connection = $persistent ? mysql_pconnect($hostname, $username, $password) : mysql_connect($hostname, $username, $password);
            if (!mysql_select_db($database, $this->connection)) {
                throw new Exception($this->getLastErrorMessage());
            }
         } catch (Exception $e) {
             $this->close();
             throw new SyL_DbConnectException($e->getMessage() . " (databse: {$database}, username: {$username}, port: {$port})");
         }
    }

    /**
     * DB接続を終了する
     */
    public function close()
    {
        if ($this->connection != null) {
            mysql_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * クライアント側エンコーティングを設定する
     *
     * 事前に self::getEncodingTable() メソッドで、
     * エンコーディング変換を定義する必要あり。
     * 
     * @param string クライアント側エンコーティング
     */
    public function setClientEncoding($client_encoding)
    {
        $db_encoding = $this->getNativeClientEncoding($client_encoding);
        if ($db_encoding) {
            if (function_exists('mysql_set_charset')) {
                mysql_set_charset($db_encoding, $this->connection);
            } else {
                $this->execute("SET NAMES {$db_encoding}");
            }
        }

        parent::setClientEncoding($client_encoding);
    }

    /**
     * サニタイズ（無効化）する
     * 
     * @param string サニタイズ対象文字列
     * @return string サニタイズ後文字列
     */
    public function escape($value)
    {
        return mysql_real_escape_string($value, $this->connection);
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        if (self::$autocommit === null) {
            self::$autocommit = ($this->queryOne('SELECT @@autocommit') == '1');
        }
        $this->execNoReturn('SET AUTOCOMMIT=0');
        $this->execNoReturn('BEGIN');
    }

    /**
     * トランザクションを確定する
     */
    public function commit()
    {
        $autocommit = self::$autocommit ? '1' : '0';
        $this->execNoReturn('COMMIT');
        $this->execNoReturn('SET AUTOCOMMIT=' . $autocommit);
    }

    /**
     * トランザクションを破棄する
     */
    public function rollBack()
    {
        $autocommit = self::$autocommit ? '1' : '0';
        $this->execNoReturn('ROLLBACK');
        $this->execNoReturn('SET AUTOCOMMIT=' . $autocommit);
    }

    /**
     * SQLを実行し結果リソースを取得する
     *
     * @param string SQL文
     * @return resource SQL実行結果リソース
     * @throws SyL_DbSqlExecuteException SQL実行エラー時
     */
    private function execute($sql)
    {
        $this->handleSql($sql);
        try {
            $query = mysql_query($sql, $this->connection);
            if (!$query) {
                throw new Exception($this->getLastErrorMessage());
            }
            return $query;
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage() . " ({$sql})");
        }
    }

    /**
     * SQLを実行し取得件数を取得（SELECT文用）
     *
     * @param string SQL文
     * @return int 取得件数
     */
    protected function execSelect($sql)
    {
        $query = $this->execute($sql);
        $result = mysql_num_rows($query);
        mysql_free_result($query);
        return $result;
    }

    /**
     * SQLを実行し実行結果影響件数を取得（INSERT, UPDATE, DELETE文用）
     *
     * @param string SQL文
     * @return int 実行結果影響件数
     */
    protected function execUpdate($sql)
    {
        $this->execute($sql);
        return mysql_affected_rows($this->connection);
    }

    /**
     * SQL実行のみ（SELECT, INSERT, UPDATE, DELETE以外）
     *
     * @param string SQL文
     */
    protected function execNoReturn($sql)
    {
        $this->execute($sql);
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
            $sql_page = $sql . ' LIMIT ' . ($pager->getStartRecord() - 1) . ', ' . $pager->getPageCount();
            $result = $this->query($sql_page);
        } else {
            $query = $this->execute($sql);
            if (is_resource($query)) {
                while ($row = mysql_fetch_object($query, self::$record_class_name)) {
                    $result[] = $row;
                }
                mysql_free_result($query);
            }
            $query = null;
        }

        return $result;
    }

    /**
     * SQL実行し結果結果の最初のレコードの最初のカラムを取得する
     *
     * レコードが取得できない場合、false を返却する。
     *
     * @param string SQL文
     * @return string 実行結果
     */
    public function queryOne($sql)
    {
        if (preg_match('/select .+ from (.+)$/i', ltrim($sql), $matches)) {
            if (stripos($matches[1], 'limit') === false) {
                $sql .= ' limit 1';
            }
        }
        $query = $this->execute($sql);
        $result = mysql_fetch_row($query);
        if ($result !== false) {
            list($result) = $result;
        }
        mysql_free_result($query);

        return $result;
    }

    /**
     * SQL実行し結果結果の最初のレコードを取得する
     *
     * レコードが取得できない場合、false を返却する。
     *
     * @param string SQL文
     * @return string 実行結果
     */
    public function queryRecord($sql)
    {
        if (preg_match('/select .+ from (.+)$/i', ltrim($sql), $matches)) {
            if (stripos($matches[1], 'limit') === false) {
                $sql .= ' limit 1';
            }
        }
        $query = $this->execute($sql);
        $result = mysql_fetch_object($query, self::$record_class_name);
        if ($result === null) {
            $result = false;
        }
        mysql_free_result($query);

        return $result;
    }

    /**
     * SQL実行しファイルストリームに書き込む
     *
     * @param resource ファイルストリーム
     * @param string SQL文
     * @param string 区切り文字
     * @param string 囲む文字
     */
    public function writeStreamCsv(&$stream, $sql, $delimiter=',', $enclosure='"')
    {
        $query = $this->execute($sql);
        while ($row = mysql_fetch_row($query)) {
            fputcsv($stream, $row, $delimiter, $enclosure);
        }
        mysql_free_result($query);
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
            $fields  = array();
            $datas   = array();
            $updates = array();
            foreach ($columns as $column => $value) {
                $fields[] = $column;
                if (is_array($value) && (count($value) == 2)) {
                    $datas[] = $value[1] ? $this->quote($value[0]) : $value[0];
                    $updates[] = $column . " = " . ($value[1] ? $this->quote($value[0]) : $value[0]);
                } else {
                    $datas[] = $this->quote($value);
                    $updates[] = $column . " = " . $this->quote($value);
                }
            }
            $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $datas) . ") ON DUPLICATE KEY UPDATE " . implode(',', $updates);
            return $this->exec($sql);
        } else {
            return parent::execPerform($table, $columns, $action, $where);
        }
    }

    /**
     * 最後に挿入された行の ID あるいはシーケンスの値を取得する
     *
     * @param string シーケンス名
     * @return int 最後に挿入された行のID
     */
    public function getLastInsertId($sequence_name='')
    {
        return mysql_insert_id($this->connection);
    }

    /**
     * 接続しているDBサーバーのバージョンを取得する
     * 
     * @return string DBのバージョン
     */
    public function getVersion()
    {
        return mysql_get_server_info($this->connection);
    }

    /**
     * 最後に起こったエラーメッセージを取得する
     *
     * エラーが起きていない場合は、nullを返却する
     *
     * @return string 最後に起こったエラーメッセージ
     */
    public function getLastErrorMessage()
    {
        $code = mysql_errno($this->connection);
        if ($code != 0) {
            return sprintf('[ErrorNo: %s] %s', $code, mysql_error($this->connection));
        } else {
            return null;
        }
    }
}
