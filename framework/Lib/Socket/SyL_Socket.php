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
 * @subpackage SyL.Lib.Socket
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ソケット関連の例外クラス */
require_once 'SyL_SocketException.php';

/**
 * ソケットクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Socket
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_Socket
{
    /**
     * 接続文字列
     * 
     * @var string
     */
    private $dsn;

    /**
     * ソケットコネクション
     * 
     * @var resource
     */
    private $conn = null;
    /**
     * トレース取得コールバックメソッド
     * 
     * @var mixed
     */
    private $trace_callback_func = null;

    /**
     * コンストラクタ
     * 
     * @param string 接続文字列
     */
    public function __construct($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * ソケットを開く
     * 
     * @param int 接続タイムアウト
     * @param int ストリームタイムアウト
     * @param bool ストリームのブロックモード
     * @throws SyL_SocketConnectException ソケット接続例外時
     */
    public function open($connect_timeout=3, $stream_timeout=null, $blocking=true)
    {
        try {
            $error_no     = null;
            $error_string = null;
            $this->conn = stream_socket_client($this->dsn, $error_no, $error_string, $connect_timeout);
            if ($error_string) {
                throw new Exception("socket connect error (No.{$error_no} {$error_string} to server: {$this->dsn}");
            }
        } catch (Exception $e) {
            throw new SyL_SocketConnectException($e->getMessage());
        }
        if (is_numeric($stream_timeout) && ($stream_timeout > 0)) {
            stream_set_timeout($this->conn, $stream_timeout);
        }

        stream_set_blocking($this->conn, $blocking);
    }

    /**
     * ソケット接続確認
     * 
     * @return true: OK, false: NG
     */
    public function isSocket()
    {
        return is_resource($this->conn);
    }

    /**
     * ソケットを閉じる
     */
    public function close()
    {
        if ($this->isSocket()) {
            fclose($this->conn);
        }
        $this->conn = null;
    }

    /**
     * コマンドをサーバーする
     * 
     * @param string 送信コマンド
     * @param int ストリームタイムアウト
     */
    public function send($command, $stream_timeout=null)
    {
        if (!$this->isSocket()) return;
        $this->trace('send', $command);
        fwrite($this->conn, $command . "\r\n");
        if (is_numeric($stream_timeout) && ($stream_timeout > 0)) {
            stream_set_timeout($this->conn, $stream_timeout);
        }
    }

    /**
     * 結果を個別に取得
     * 
     * @return string 取得データ
     * @throws SyL_SocketReadException ソケット情報取得例外時
     * @throws SyL_SocketTimeoutException ソケット取得タイムアウト時
     */
    public function receive()
    {
        if (!$this->isSocket()) return null;
        $receive = null;
        try {
            $receive = fgets($this->conn);
            $stream = stream_get_meta_data($this->conn);
            if (isset($stream['timed_out']) && $stream['timed_out']) {
                throw new SyL_SocketTimeoutException('socket timeout');
            }
            if ($receive === false) {
                throw new Exception('socket read false');
            }
        } catch (SyL_SocketTimeoutException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SyL_SocketReadException($e->getMessage());
        }
        $this->trace('receive', $receive);
        return $receive;
    }

    /**
     * 結果を全て取得
     * 
     * @return string 取得データ
     */
    public function receiveAll()
    {
        $receive = '';
        while ($this->conn && !feof($this->conn)) {
            $tmp = '';
            try {
                $tmp = $this->receive();
            } catch (SyL_SocketReadException $e) {
                break;
            }
            $receive .= $tmp;
        }
        return $receive;
    }

    /**
     * トレース取得メソッドをセット
     * 
     * @param mixed デバック文字列取得メソッド
     */
    public function setCommandCallback($trace_callback_func)
    {
        $this->trace_callback_func = $trace_callback_func;
    }

    /**
     * トレース取得をメソッド実行
     * 
     * @param string send or receive
     * @param string ソケット送受信文字列
     */
    private function trace($type, $message)
    {
        if ($this->trace_callback_func != null) {
            call_user_func($this->trace_callback_func, $type, $message);
        }
    }
}

