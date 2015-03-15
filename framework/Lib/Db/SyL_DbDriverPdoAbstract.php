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
 * DBクラス（PDO）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_DbDriverPdoAbstract extends SyL_DbAbstract
{
    /**
     * エンコーディング変換フラグ
     * 
     * @var bool
     */
    protected $is_convert_encoding = true;

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
        list($dsn, $username, $password) = $this->getPdoConnectionInfo($database, $username, $password, $hostname, $port);

        try {
            $this->connection = new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => $persistent, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
             throw new SyL_DbConnectException(sprintf('%s (databse: %s, username: %s, port: %s)', $e->getMessage(), $database, $username, $port));
        }
    }

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
    protected abstract function getPdoConnectionInfo($database, $username, $password, $hostname, $port);

    /**
     * DB接続を終了する
     */
    public function close()
    {
        $this->connection = null;
    }

    /**
     * サニタイズしたSQL句を取得する
     *
     * @param string クォート前文字列
     * @return string クォート後文字列
     */
    public function quote($value)
    {
        $result = $this->connection->quote($value);
        if ($result !== false) {
            $result = parent::quote($value);
        }
        return $result;
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        try {
            $this->connection->beginTransaction();
        } catch (PDOException $e) {
            throw new SyL_DbSqlExecuteException($e->getMessage());
        }
    }

    /**
     * トランザクションを確定する
     */
    public function commit()
    {
        try {
            $this->connection->commit();
        } catch (PDOException $e) {
            throw new SyL_DbSqlExecuteException($e->getMessage());
        }
    }

    /**
     * トランザクションを破棄する
     */
    public function rollBack()
    {
        try {
            $this->connection->rollBack();
        } catch (PDOException $e) {
            throw new SyL_DbSqlExecuteException($e->getMessage());
        }
    }

    /**
     * SQLを実行し PDOStatement オブジェクトを取得する
     *
     * @param string SQL文
     * @return PDOStatement PDOStatement オブジェクト
     * @throws SyL_DbSqlExecuteException SQL実行エラー時
     */
    protected function execute($sql)
    {
        $this->handleSql($sql);
        $sql = $this->convertEncoding($sql);
        $stmt = null;
        try {
            $stmt = $this->connection->query($sql);
            if ($stmt === false) {
                throw new Exception($this->getLastErrorMessage());
            }
            $error_info = $stmt->errorInfo();
            if (isset($error_info[0]) && !preg_match('/^0+$/', $error_info[0])) {
                throw new Exception(sprintf('[ErrorNo: %s] %s', $error_info[1], $error_info[2]));
            }
            return $stmt;
        } catch (Exception $e) {
            $stmt = null;
            throw new SyL_DbSqlExecuteException($e->getMessage() . " ({$sql})");
        }
    }

    /**
     * SQLを実行し取得件数を取得（SELECT文用）
     *
     * @param string SQL文
     * @return int 取得件数
     * @throws SyL_InvalidParameterException 解析できない select 文の場合(サブクエリを含む場合など）
     */
    protected function execSelect($sql)
    {
        if (preg_match('/^select .+ from (.+)$/i', trim($sql), $matches)) {
            $sql = "SELECT COUNT(*) FROM {$matches[1]}";
            return (int)$this->queryOne($sql);
        } else {
            throw new SyL_InvalidParameterException("invalid select sql ({$sql})");
        }
    }

    /**
     * SQLを実行し実行結果影響件数を取得（INSERT, UPDATE, DELETE文用）
     *
     * @param string SQL文
     * @return int 実行結果影響件数
     */
    protected function execUpdate($sql)
    {
        $stmt = $this->execute($sql);
        $result = $stmt->rowCount();
        $stmt->closeCursor();
        $stmt = null;
        return $result;
    }

    /**
     * SQL実行のみ
     *
     * @access public
     * @param string SQL文
     * @return boolean 実行OK: true, 実行NG: false
     */
    public function execNoReturn($sql)
    {
        $stmt = $this->execute($sql);
        $stmt->closeCursor();
        $stmt = null;
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * ページングは、ドライバに依存する面が多いため、
     * この抽象クラスではサポートしていない。
     *
     * @param string SQL文
     * @param SyL_Pager ページングオブジェクト
     * @return array 実行結果
     */
    public function query($sql, SyL_Pager $pager=null)
    {
        $stmt = $this->execute($sql);

        $result = array();
        while ($row = $stmt->fetchObject(self::$record_class_name)) {
            $result[] = $this->convertDecoding($row);
        }
        $stmt->closeCursor();
        $stmt = null;

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
        $stmt = $this->execute($sql);

        $result = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
        if ($result !== false) {
            list($result) = $result;
            $result = $this->convertDecoding($result);
        }
        $stmt->closeCursor();
        $stmt = null;

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
        if (!preg_match('/limit/i', $sql)) {
            $sql .= ' limit 1';
        }
        $stmt = $this->execute($sql);

        $result = $stmt->fetchObject(self::$record_class_name);
        if ($result !== false) {
            $result = $this->convertDecoding($result);
        }
        $stmt->closeCursor();
        $stmt = null;

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
        $stmt = $this->execute($sql);
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            fputcsv($stream, $row, $delimiter, $enclosure);
        }
        $stmt->closeCursor();
        $stmt = null;
    }

    /**
     * SQLを実行する準備を行い、SQLステートメントオブジェクトを取得する
     *
     * @param string SQL文
     * @return SyL_DbSqlStatement SQLステートメントオブジェクト
     */
    public function prepare($sql)
    {
        include_once 'SyL_DbSqlStatementPdo.php';
        return new SyL_DbSqlStatementPdo($this, $sql);
    }

    /**
     * 最後に挿入された行の ID あるいはシーケンスの値を取得
     *
     * @param string シーケンス名
     * @return int 最後に挿入された行のID
     */
    public function getLastInsertId($sequence_name='')
    {
        if ($sequence_name) {
            return (int)$this->connection->lastInsertId($sequence_name);
        } else {
            return (int)$this->connection->lastInsertId();
        }
    }

    /**
     * 接続しているDBサーバーのバージョンを取得する
     * 
     * @return string DBのバージョン
     */
    public function getVersion()
    {
        return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
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
        $error_info = $this->connection->errorInfo();
        if (isset($error_info[0]) && !preg_match('/^0+$/', $error_info[0])) {
            return sprintf('[ErrorNo: %s] %s', $error_info[1], $error_info[2]);
        } else {
            return null;
        }
    }

    /**
     * 大文字または小文字参照を取得する
     *
     * @param int 大文字または小文字変換
     */
    public function setFieldCase($field_case)
    {
        parent::setFieldCase($field_case);
        if ($field_case === CASE_UPPER) {
            $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER);
        } else if ($field_case === CASE_LOWER) {
            $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        } else {
            $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        }
    }

    /**
     * エンコード変換（PHP => DB）を行う
     *
     * @param mixed 変換前値
     * @return mixed 変換後値
     */
    public function convertEncoding($value)
    {
        return $this->is_convert_encoding ? parent::convertEncoding($value) : $value;
    }

    /**
     * デコード変換（DB => PHP）を行う
     *
     * @param mixed 変換前値
     * @return mixed 変換後値
     */
    public function convertDecoding($value)
    {
        return $this->is_convert_encoding ? parent::convertDecoding($value) : $value;
    }
}
