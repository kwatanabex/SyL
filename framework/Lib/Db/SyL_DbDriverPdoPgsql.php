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

/** DBクラス（PDO） */
require_once 'SyL_DbDriverPdoAbstract.php';

/**
 * DBクラス（PDO::Pgsql）
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
class SyL_DbDriverPdoPgsql extends SyL_DbDriverPdoAbstract
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
        $dsn  = 'pgsql:';
        $dsn .= 'host=' . $hostname;
        $dsn .= ' dbname=' . $database;
        $dsn .= ' user=' . $username;
        $dsn .= ' password=' . $password;
        if ($port) {
            $dsn .= ' port=' . $port;
        }
        return array($dsn, null, null);
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
            $this->execute("SET CLIENT_ENCODING TO {$db_encoding}");
            $this->is_convert_encoding = false;
        }

        parent::setClientEncoding($client_encoding);
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
}
