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
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

require_once 'SyL_MailSendAbstract.php';
require_once dirname(__FILE__) . '/../Socket/SyL_Socket.php';

/**
 * SMTPメール送信クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_MailSendSmtp extends SyL_MailSendAbstract
{
    /**
     * メールサーバーのホスト名
     *
     * @var string
     */
    protected $host = 'localhost';
    /**
     * メールサーバーのポート番号
     *
     * @var int
     */
    protected $port = 25;
    /**
     * ユーザー名
     *
     * @var string
     */
    protected $user = '';
    /**
     * パスワード
     *
     * @var string
     */
    protected $passwd = '';

    /**
     * ソケットクラス
     *
     * @var SyL_Socket
     */
    private $socket = null;
    /**
     * SMTPコマンドログ
     *
     * @var string
     */
    private $command_log = '';

    /**
     * ホスト名をセットする
     *
     * @param string ホスト名
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * ポート番号をセットする
     *
     * @param string ポート番号
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * ユーザー名をセットする
     *
     * @param string ユーザー名
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * パスワードをセットする
     *
     * @param string パスワード
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    /**
     * メールサーバーに接続
     *
     * @param int 接続タイムアウト
     * @param int ストリームタイムアウト
     * @param bool コマンドブロッキング
     */
    public function connect($connect_timeout=3, $stream_timeout=0, $blocking=true)
    {
        $this->quit();

        $dsn = sprintf('tcp://%s:%s', $this->host, $this->port);
        $this->socket = new SyL_Socket($dsn);
        $this->socket->setCommandCallback(array($this, 'receiveMessage'));

        try {
            // 接続
            $this->socket->open($connect_timeout, $stream_timeout, $blocking);
            // 接続メッセージ取得
            $this->socket->receive();
            // ログインメッセージ送信
            $this->socket->send('HELO ' . $this->host);
            // ログインメッセージ取得
            $this->socket->receive();

            // SMTP AUTH 判定
            if ($this->user) {
                // 認証情報送信
                $this->socket->send('AUTH PLAIN ' . base64_encode("{$this->user}\0{$this->user}\0{$this->passwd}"));
                try {
                    // 認証結果取得
                    $this->socket->receive();
                } catch (SyL_MailCommandException $e) {
                    throw new SyL_MailAuthenticationException($e->getMessage());
                }
            }
        } catch (Exception $e) {
            $this->quit();
            throw $e;
        }
    }

    /**
     * メールを送信する
     *
     * @param SyL_MailMessage メールメッセージオブジェクト
     */
    public function send(SyL_MailMessage $message)
    {
        $from = $message->getFrom();
        // メールヘッダを含む全文取得
        $msg = $message->getMessage(false);
        // 最後に「.」を付加
        $msg .= '.';

        // ソケット作成判定
        $once = false;
        if ($this->socket == null) {
            $once = true;
            $this->connect();
        }

        try {
            // 送信元セットメッセージ送信
            $this->socket->send('MAIL FROM: ' . $from[0]);
            // 送信元セットメッセージ取得
            $this->socket->receive();

            foreach ($message->getRcptTo() as $to) {
                // 送信先セットメッセージ送信
                $this->socket->send('RCPT TO: ' . $to);
                // 送信先セットメッセージ取得
                $this->socket->receive();
            }

            // データセットメッセージ送信
            $this->socket->send('DATA');
            // データセットメッセージ取得
            $this->socket->receive();

            // 送信データメッセージ送信
            $this->socket->send($msg);
            // 送信データメッセージ取得
            $this->socket->receive();

            // リセットメッセージ送信
            $this->socket->send('RSET');
            // リセットメッセージ取得
            $this->socket->receive();

        } catch (Exception $e) {
            if ($once) {
                $this->quit();
            }
            throw new SyL_MailSendException($e->getMessage());
        }

        if ($once) {
            $this->quit();
        }
    }

    /**
     * メールサーバーとの接続を閉じる
     */
    public function quit()
    {
        if (is_object($this->socket)) {
            // 完了メッセージ送信
            $this->socket->send('QUIT');
            // 完了メッセージ取得
            $this->socket->receive();
            // ソケットを閉じる
            $this->socket->close();
        }
        $this->socket = null;
    }

    /**
     * SMTPコマンド受信メッセージ
     *
     * @param string send or receive
     * @param string コマンドメッセージ
     */
    public function receiveMessage($type, $message)
    {
        $message = trim($message);
        $this->command_log .= "[{$type}] {$message}\n";
        if ($type == 'receive') {
            if (preg_match('/^[4|5][0-9]{2}/', $message)) {
                throw new SyL_MailCommandException($message);
            }
        }
    }

    /**
     * ソケットコマンド受信ログを取得
     *
     * @return string ソケットコマンド受信ログ
     */
    public function getCommandLog()
    {
        return $this->command_log;
    }
}
