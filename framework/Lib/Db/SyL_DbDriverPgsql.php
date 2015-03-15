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
 * DB操作クラス（PostgreSQL）
 *
 * PostgreSQL 7.4 以上
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbDriverPgsql extends SyL_DbAbstract
{
    /**
     * DB接続を開始する
     * 
     * @param string データベース名
     * @param string ユーザー名
     * @param string パスワード
     * @param string ホスト名
     * @param string ポート番号
     * @param bool 持続的接続
     */
    public function open($database, $username, $password, $hostname, $port, $persistent)
    {
        $connection_string  = '';
        $connection_string .= 'dbname=' . $database;
        $connection_string .= ' user='   . $username;
        $connection_string .= ' host=' . $hostname;
        $connection_string .= ' password=' . $password;
        if ($port) {
            $connection_string .= ' port=' . $port;
        }

        try {
            $this->connection = $persistent ? pg_pconnect($connection_string) : pg_connect($connection_string);
        } catch (Exception $e) {
            throw new SyL_DbConnectException($e->getMessage() . " (databse: {$database}, username: {$username}, port: {$port})");
        }
    }

    /**
     * DB接続を終了する
     */
    public function close()
    {
        if ($this->connection != null) {
            pg_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * クライアント側エンコーティングを設定する
     *
     * 事前に self::getEncodingTable() メソッドで、
     * エンコーディング変換を定義する必要
     * 
     * @param string クライアント側エンコーティング
     */
    public function setClientEncoding($client_encoding)
    {
        $db_encoding = $this->getNativeClientEncoding($client_encoding);
        if ($db_encoding) {
            pg_set_client_encoding($this->connection, $db_encoding);
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
        return pg_escape_string($this->connection, $value);
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        $this->execNoReturn('BEGIN');
    }

    /**
     * トランザクションを確定する
     */
    public function commit()
    {
        $this->execNoReturn('COMMIT');
    }

    /**
     * トランザクションを破棄する
     */
    public function rollBack()
    {
        $this->execNoReturn('ROLLBACK');
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
            $query = pg_query($this->connection, $sql);
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
        $result = pg_num_rows($query);
        pg_free_result($query);
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
        $query = $this->execute($sql);
        return pg_affected_rows($query);
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
            $sql_page = $sql . ' LIMIT ' . $pager->getPageCount() . ' OFFSET ' . ($pager->getStartRecord() - 1);
            $result = $this->query($sql_page);
        } else {
            $query = $this->execute($sql);
            while ($row = pg_fetch_object($query, null, self::$record_class_name)) {
                $result[] = $row;
            }
            pg_free_result($query);
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

        $result = false;
        if (pg_num_rows($query) > 0) {
            list($result) = pg_fetch_row($query);
        }
        pg_free_result($query);

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

        $result = false;
        if (pg_num_rows($query) > 0) {
            $result = pg_fetch_object($query, null, self::$record_class_name);
        }
        pg_free_result($query);

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
        while ($row = pg_fetch_row($query)) {
            fputcsv($stream, $row, $delimiter, $enclosure);
        }
        pg_free_result($query);
    }

    /**
     * SQLを実行する準備を行い、SQLステートメントオブジェクトを取得する
     *
     * @param string SQL文
     * @return SyL_DbSqlStatement SQLステートメントオブジェクト
     */
    public function prepare($sql)
    {
        include_once 'SyL_DbSqlStatementPgsql.php';
        return new SyL_DbSqlStatementPgsql($this, $sql);
    }

    /**
     * 最後に挿入された行の ID あるいはシーケンスの値を取得
     *
     * @param string シーケンス名
     * @return int 最後に挿入された行のID
     */
    public function getLastInsertId($sequence_name='')
    {
        return (int)$this->queryOne("SELECT CURRVAL('{$sequence_name}')");
    }

    /**
     * 接続しているDBサーバーのバージョンを取得する
     * 
     * @return string DBのバージョン
     */
    public function getVersion()
    {
        $result = $this->queryOne('SELECT VERSION()');
        $version = null;
        if (preg_match('/^PostgreSQL (.+) on/', trim($result), $matches)) {
            $version = $matches[1];
        }
        return $version;
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
        $error_message = pg_last_error($this->connection);
        return $error_message ? $error_message : null;
    }
}
