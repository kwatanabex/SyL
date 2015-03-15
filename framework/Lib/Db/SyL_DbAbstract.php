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

/** DB関連エラー例外クラス */
require_once 'SyL_DbException.php';
/** テーブルレコードクラス */
require_once 'SyL_DbRecord.php';
/** データソース文字列クラス */
require_once dirname(__FILE__) . '/../Util/SyL_UtilDsn.php';

/**
 * DBクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_DbAbstract
{
    /**
     * コネクションリソース、 PDOの場合はオブジェクト
     * 
     * @var mixed
     */
    protected $connection = null;
    /**
     * クライアント側エンコーディング
     * 
     * @var string
     */
    private $client_encoding = '';
    /**
     * DBサーバー側エンコーディング
     * 
     * @var string
     */
    private $server_encoding = '';

    /**
     * データベースオブジェクト配列
     * 
     * @var array
     */
    private static $instances = array();
    /**
     * エンコーディング変換テーブル
     * 
     * @var array
     */
    private $encoding_table = array();

    /**
     * 結果セットレコードクラス名
     *
     * @var string
     */
    protected static $record_class_name = 'SyL_DbRecord';

    /**
     * 最後に実行したSQL
     * 
     * @var string
     */
    private $last_sql = '';
    /**
     * SQLログ取得コールバックメソッド
     * 
     * @var mixed
     */
    private $callback_sql = null;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * SyL_DBクラスのインスタンス取得
     *
     * 接続文字列のフォーマットは、SyL_UtilDsn に依存
     *
     * PDOの場合のdbtype
     *   pdo.mysql
     *
     * @param string 接続文字列
     * @param bool 強制接続フラグ
     * @return SyL_DBAbstract DBオブジェクト
     * @see SyL_UtilDsn
     */
    public static function getInstance($dsn, $force=false)
    {
        if (isset(self::$instances[$dsn]) && !$force) {
            $type = gettype(self::$instances[$dsn]->getResource());
            if (($type != 'NULL') && ($type != 'unknown type')) {
                return self::$instances[$dsn];
            }
            unset(self::$instances[$dsn]);
        }
        
        if (!$dsn) {
            throw new SyL_InvalidParameterException('SyL_DB connectionString is empty');
        }

        list($type, $database, $username, $password, $hostname, $port, $persistent, $server_encoding, $client_encoding, $encoding_table) = self::parseDsn($dsn);
        $classname = 'SyL_DbDriver' . ucfirst($type);
        include_once $classname . '.php';
        $conn = new $classname();
        if ($encoding_table) {
            $conn->addEncodingTable($encoding_table[0], $encoding_table[1]);
        }
        if ($server_encoding) {
            $conn->setServerEncoding($server_encoding);
        }
        $conn->open($database, $username, $password, $hostname, $port, $persistent);
        if ($client_encoding) {
            $conn->setClientEncoding($client_encoding);
        }
        self::$instances[$dsn] = $conn;
        return self::$instances[$dsn];
    }

    /**
     * プール中の全インスタンスを終了する
    */
    public static function closeInstances()
    {
        foreach (array_keys(self::$instances) as $name) {
            self::$instances[$name]->close();
            self::$instances[$name] = null;
        }
        self::$instances = array();
    }

    /**
     * 接続文字列を分解する
     *
     * client_encoding: PHPクライアント側のPHP文字コード
     * server_encoding: DBサーバー側のPHP文字コード
     * encoding_table: array(PHPクライアント側のPHP文字コード, DBサーバー側のDB文字コード)
     *
     * @param string 接続文字列
     * @return array array(type, database, username, password, hostname, port, persistent, server_encoding, client_encoding, encoding_table);
     * @throws SyL_InvalidParameterException encoding_tableパラメータ不正
     */
    private static function parseDsn($dsn)
    {
        $type     = null;
        $database = null;
        $username = null;
        $password = null;
        $hostname = null;
        $port     = null;
        $persistent = false;
        $server_encoding = null;
        $client_encoding = null;
        $encoding_table  = null;

        list($type, $username, $password, $hostname, $port, $database, $parameters) = SyL_UtilDsn::parse($dsn);

        $type = implode('', array_map('ucfirst', explode('.', $type)));

        if (!$hostname) {
            $hostname = 'localhost';
        }
        if ($database) {
            $database = substr($database, 1);
        }

        foreach ($parameters as $name => $value) {
            switch ($name) {
            case 'client_encoding':
                $client_encoding = $value;
                break;
            case 'server_encoding':
                $server_encoding = $value;
                break;
            case 'encoding_table':
                $values = explode(':', $value, 2);
                if (count($values) == 2) {
                    $encoding_table = $values;
                } else {
                    throw new SyL_InvalidParameterException("invalid encoding_table ({$value})");
                }
                break;
            case 'persistent':
                $persistent = ($value == 'true');
                break;
            }
        }

        return array($type, $database, $username, $password, $hostname, $port, $persistent, $server_encoding, $client_encoding, $encoding_table);
    }

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
    public abstract function open($database, $username, $password, $hostname, $port, $persistent);

    /**
     * DB接続を終了する
     */
    public abstract function close();

    /**
     * PHP,DB変換エンコーディングテーブルを追加する
     *
     * @param string PHPエンコーディング
     * @param string DBエンコーディング
     */
    public function addEncodingTable($php_encoding, $db_encoding)
    {
        $encoding = array(strtolower($php_encoding), strtolower($db_encoding));
        foreach ($this->encoding_table as $encoding_table) {
            if (($encoding_table[0] == $encoding[0]) && ($encoding_table[1] == $encoding[1])) {
                return;
            }
        }
        array_unshift($this->encoding_table, $encoding);
    }

    /**
     * PHPクライアントエンコーディングをDBクライアントエンコーディングに変換する
     *
     * @param string PHPクライアントエンコーディング
     * @return string DBクライアントエンコーディング
     */
    public function getNativeClientEncoding($client_encoding)
    {
        $client_encoding = strtolower($client_encoding);
        $db_encoding = null;
        foreach ($this->encoding_table as $encoding_table) {
            if ($encoding_table[0] == $client_encoding) {
                $db_encoding = $encoding_table[1];
                break;
            }
        }
        return $db_encoding;
    }

    /**
     * クライアント側エンコーティングを設定する
     * 
     * @param string クライアント側エンコーティング
     */
    public function setClientEncoding($client_encoding)
    {
        $this->client_encoding = $client_encoding;
    }

    /**
     * DBサーバー側エンコーディングを設定する
     * 
     * @param string DBサーバー側エンコーディング
     */
    public function setServerEncoding($server_encoding)
    {
        $this->server_encoding = $server_encoding;
    }

    /**
     * サニタイズ（無効化）する
     * 
     * @param string サニタイズ対象文字列
     * @return string サニタイズ後文字列
     */
    public function escape($value)
    {
        return addslashes($value);
    }

    /**
     * サニタイズしたSQL句を生成する
     *
     * @param string クォート前文字列
     * @return string クォート後文字列
     */
    public function quote($value)
    {
        if (($value === '') || ($value === null)) {
            return 'NULL';
        } else if (is_int($value) || is_float($value)) {
            return (string)$value;
        } else if ($value instanceof DateTime) {
            return "'" . $value->format('Y-m-d H:i:s') . "'";
        } else {
            return "'" . $this->escape($value) . "'";
        }
    }

    /**
     * サニタイズしたLIKE SQL句を生成する
     *
     * @param string クォート前文字列
     * @return string クォート後文字列
     */
    public function quoteLike($value)
    {
        $value = $this->escape($value);
        $value = str_replace(array('%','_'), array('\%', '\_'), $value);
        return "'%" . $value . "%'";
    }

    /**
     * トランザクションを開始する
     */
    public abstract function beginTransaction();

    /**
     * トランザクションを確定する
     */
    public abstract function commit();

    /**
     * トランザクションを破棄する
     */
    public abstract function rollBack();

    /**
     * SQLを実行し、結果取得する
     * 
     * ・SQL文が、select句の場合
     *   実行結果を取得件数として取得
     * ・SQL文が、insert, update, delete句の場合
     *   実行結果を影響件数として取得
     * ・SQL文が、上記以外の場合
     *   nullを返却
     *
     * @param string SQL文
     * @return mixed 実行結果
     */
    public function exec($sql)
    {
        // メソッド名抽出
        list($method) = explode(' ', $sql, 2);

        // SQL実行
        $result = null;
        switch (strtolower($method)) {
        case 'select':
            $result = $this->execSelect($sql);
            break;
        case 'insert':
        case 'update':
        case 'delete':
            $result = $this->execUpdate($sql);
            break;
        default:
            $this->execNoReturn($sql);
            break;
        }
        return $result;
    }

    /**
     * SQLを実行し取得件数を取得（SELECT文用）
     *
     * @param string SQL文
     * @return int 取得件数
     */
    protected abstract function execSelect($sql);

    /**
     * SQLを実行し実行結果影響件数を取得（INSERT, UPDATE, DELETE文用）
     *
     * @param string SQL文
     * @return int 実行結果影響件数
     */
    protected abstract function execUpdate($sql);

    /**
     * SQL実行のみ（SELECT, INSERT, UPDATE, DELETE以外）
     *
     * @param string SQL文
     */
    protected abstract function execNoReturn($sql);

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
    public abstract function query($sql, SyL_Pager $pager=null);

    /**
     * SQL実行し結果結果の最初のレコードの最初のカラムを取得する
     *
     * @param string SQL文
     * @return string 実行結果
     */
    public abstract function queryOne($sql);

    /**
     * SQL実行し結果結果の最初のレコードを取得する
     *
     * @param string SQL文
     * @return string 実行結果
     */
    public abstract function queryRecord($sql);

    /**
     * SQL実行しファイルストリームに書き込む
     *
     * @param resource ファイルストリーム
     * @param string SQL文
     * @param string 区切り文字
     * @param string 囲む文字
     */
    public abstract function writeStreamCsv(&$stream, $sql, $delimiter=',', $enclosure='"');

    /**
     * ページングオブジェクトを取得する
     *
     * @param int 1ページの表示件数
     * @param int 表示対象ページ数 
     * @return SyL_Pager ページングオブジェクト
     */
    public function getPager($count, $page=1)
    {
        include_once dirname(__FILE__) . '/../SyL_Pager.php';
        $pager = new SyL_Pager($count);
        $pager->setCurrentPage($page);
        return $pager;
    }

    /**
     * SQLを実行する準備を行い、SQLステートメントオブジェクトを取得する
     *
     * @param string SQL文
     * @return SyL_DbSqlStatement SQLステートメントオブジェクト
     */
    public function prepare($sql)
    {
        include_once 'SyL_DbSqlStatementEmulator.php';
        return new SyL_DbSqlStatementEmulator($this, $sql);
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
        switch (strtolower($action)) {
        case 'insert':
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
            $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $datas) . ")";
            break;

        case 'update':
            $updates = array();
            foreach ($columns as $column => $value) {
                if (is_array($value) && (count($value) == 2)) {
                    $updates[] = $column . " = " . ($value[1] ? $this->quote($value[0]) : $value[0]);
                } else {
                    $updates[] = $column . " = " . $this->quote($value);
                }
            }

            if ($where) {
                $where = "WHERE " . $where;
            }
            $sql = "UPDATE {$table} SET " . implode(',', $updates) . " " . $where;
            break;

        case 'delete':
            $sql = "DELETE FROM {$table}";
            if ($where) {
                $sql .= " WHERE " . $where;
            }
            break;

        case 'replace':
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
            if ($this->queryOne($sql) > 0) {
                return $this->execPerform($table, $columns, 'update', $where);
            } else {
                return $this->execPerform($table, $columns, 'insert');
            }
            break;

        default:
            throw new SyL_InvalidParameterException('execPerform method failed. action prameter is insert or update or delete or replace only');
        }

        return $this->exec($sql);
    }

    /**
     * 最後に挿入された行の ID あるいはシーケンスの値を取得する
     *
     * ※使用できない DB の場合は false を返す
     *
     * @param string シーケンス名
     * @return int 最後に挿入された行のID
     */
    public abstract function getLastInsertId($sequence_name='');

    /**
     * 接続しているDBサーバーのバージョンを取得する
     * 
     * @return string DBのバージョン
     */
    public abstract function getVersion();

    /**
     * 最後に起こったエラーメッセージを取得する
     *
     * エラーが起きていない場合は、nullを返却する
     *
     * @return string 最後に起こったエラーメッセージ
     */
    public abstract function getLastErrorMessage();

    /**
     * 結果セットレコードクラス名をセットする
     *
     * @param string 結果セットレコードクラス名
     */
    public static function setRecordClassName($record_class_name)
    {
        self::$record_class_name = $record_class_name;
    }

    /**
     * 結果セットレコードクラス名を取得する
     *
     * @return string 結果セットレコードクラス名
     */
    public static function getRecordClassName()
    {
        return self::$record_class_name;
    }

    /**
     * エンコード変換（PHP => DB）を行う
     *
     * @param mixed 変換前値
     * @return mixed 変換後値
     */
    public function convertEncoding($value)
    {
        if ($this->server_encoding) {
            if ($this->client_encoding != $this->server_encoding) {
                $this->convertEncodingRecursive($value, $this->client_encoding, $this->server_encoding);
            }
        }
        return $value;
    }

    /**
     * デコード変換（DB => PHP）を行う
     *
     * @param mixed 変換前値
     * @return mixed 変換後値
     */
    public function convertDecoding($value)
    {
        if ($this->client_encoding) {
            if ($this->client_encoding != $this->server_encoding) {
                $this->convertEncodingRecursive($value, $this->server_encoding, $this->client_encoding);
            }
        }
        return $value;
    }

    /**
     * エンコード変換を行う
     *
     * @param mixed 変換前値
     * @param string 変換前エンコーディング
     * @param string 変換後エンコーディング
     */
    private function convertEncodingRecursive(&$value, $input_encoding, $output_encoding)
    {
        if (is_array($value) || ($value instanceof SyL_DbRecord)) {
            foreach ($value as &$tmp) {
                $this->convertEncodingRecursive($tmp, $input_encoding, $output_encoding);
            }
        } else {
            if (is_scalar($value)) {
                if ($input_encoding) {
                    $value = mb_convert_encoding($value, $output_encoding, $input_encoding);
                } else {
                    $value = mb_convert_encoding($value, $output_encoding);
                }
            }
        }
    }

    /**
     * SQL文をハンドルするコールバックメソッドをセットする
     *
     * @param mixed SQL文をハンドルするコールバックメソッド
     * @throws SyL_InvalidParameterException コールバック関数として適用できない場合
     */
    public function setCallbackSql($callback_sql)
    {
        if (is_callable($callback_sql)) {
            $this->callback_sql = $callback_sql;
        } else {
            throw new SyL_InvalidParameterException('invalid argument. not function or method (' . print_r($callback_sql, true) . ')');
        }
    }

    /**
     * 実行するSQL文をハンドルする
     *
     * 実行するSQL文をハンドルするコールバックメソッドが
     * セットされていない場合の何も処理しないダミーメソッド。
     *
     * @param string SQL文
     */
    protected function handleSql($sql)
    {
        $this->last_sql = $sql;
        if ($this->callback_sql) {
            call_user_func($this->callback_sql, $sql);
        }
    }

    /**
     * オリジナルリソース、またはオブジェクトを取得する
     *
     * @return mixed オリジナルリソース、またはオブジェクト
     */
    public function getResource()
    {
        return $this->connection;
    }

    /**
     * スキーマオブジェクトを取得する
     *
     * @return SyL_DbSchemaAbstract スキーマオブジェクト
     */
    public function getSchema()
    {
        include_once 'SyL_DbSchemaAbstract.php';
        return SyL_DbSchemaAbstract::createInstance($this);
    }
}
