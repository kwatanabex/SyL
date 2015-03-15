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

require_once dirname(__FILE__) . '/../Socket/SyL_Socket.php';

/**
 * POP3メール受信クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_MailReceivePop3 extends SyL_MailReceiveAbstract
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
    protected $port = 110;
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
     * APOP使用判定
     *
     * @var bool
     */
    private $apop = false;
    /**
     * メールボックス内の総メール数
     *
     * @var int
     */
    private $total_num = null;
    /**
     * メールボックス内の総バイト数
     *
     * @var int
     */
    private $total_bytes = null;
    /**
     * SMTPコマンドログ
     *
     * @var string
     */
    private $command_log = '';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
    }

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
     * 総メール件数を取得
     *
     * @return int 総メール件数
     */
    public function getTotalNum()
    {
        return $this->total_num;
    }

    /**
     * 総メールバイト数を取得
     *
     * @return int 総メールバイト数
     */
    public function getTotalBytes()
    {
        return $this->total_bytes;
    }

    /**
     * APOPを有効にする
     *
     * @param bool APOP判定
     */
    public function enableApop($apop=true)
    {
        $this->apop = (bool)$apop;
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

        if ($this->user == '') {
            throw new SyL_MailParameterException("required user");
        }

        // ソケットオブジェクト作成
        $this->socket = new SyL_Socket($this->host, $this->port);
        $this->socket->setCommandCallback(array($this, 'receiveMessage'));

        try {
            // 接続
            $this->socket->open($connect_timeout, $stream_timeout, $blocking);
            // APOP使用判定
            if ($this->apop) {
                // 接続メッセージ取得
                if (preg_match('/\+OK .*(<[^>]+>)/', $this->socket->receive(), $matches)) {
                    // ダイジェスト送信
                    $this->socket->send('APOP ' . $this->user . ' ' . md5($matches[1] . $this->passwd));
                    try {
                        // ログインメッセージ取得
                        $this->socket->receive();
                    } catch (SyL_MailCommandException $e) {
                        throw new SyL_MailAuthenticationException($e->getMessage());
                    }
                } else {
                    throw new SyL_MailAuthenticationException('APOP challenge not found');
                }
            } else {
                // 接続メッセージ取得
                $this->socket->receive();
                // ユーザー名送信
                $this->socket->send('USER ' . $this->user);
                // ログインメッセージ取得
                $this->socket->receive();
                // パスワード送信
                $this->socket->send('PASS ' . $this->passwd);
                try {
                    // ログインメッセージ取得
                    $this->socket->receive();
                } catch (SyL_MailCommandException $e) {
                    throw new SyL_MailAuthenticationException($e->getMessage());
                }
                // メール数取得
                list($this->total_num, $this->total_bytes) = $this->getStat();
            }
        } catch (Exception $e) {
            $this->quit();
            throw $e;
        }
    }

    /**
     * メール総件数と総バイト数を取得
     *
     * @return array  メール総件数と総バイト数
     */
    public function getStat()
    {
        $this->socket->send('STAT');
        $stats = explode(' ', trim($this->socket->receive()));
        return array($stats[1], $stats[2]);
    }

    /**
     * 各メールごとのバイト数を取得
     *
     * @return array 各メールごとのバイト数
     */
    public function getList()
    {
        $list = array();
        $this->socket->send('LIST');
        $this->socket->receive();
        while (true) {
            $receive = trim($this->socket->receive());
            if (!$receive || ($receive == '.')) {
                break;
            }
            list($num, $bytes) = explode(' ', $receive, 2);
            $list[$num] = $bytes;
        }
        return $list;
    }

    /**
     * メール受信実行
     *
     * @param int メールメッセージ番号
     * @return SyL_MailMessage メールメッセージオブジェクト
     */
    public function receive($num)
    {
        if (!is_numeric($num) || ($num <= 0)) {
            throw new SyL_MailParameterException("invalid mail message number ($num)");
        }

        $data = '';
        $this->socket->send('RETR ' . $num);
        while (true) {
            $receive = $this->socket->receive();
            if (!$receive || (trim($receive) == '.')) {
                break;
            }
            $data .= $receive;
        }

        $message = SyL_MailAbstract::createMessage();
        $message->setMessage($data);

        return $message;
    }

    /**
     * メール削除実行
     *
     * @param int メールメッセージ番号
     */
    public function delete($num)
    {
        if (!is_numeric($num) || ($num <= 0)) {
            throw new SyL_MailParameterException("invalid mail message number ($num)");
        }
        $this->socket->send('DELE ' . $num);
        $this->socket->receive();
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
     * POP3コマンド受信メッセージ
     *
     * @param string send or receive
     * @param string コマンドメッセージ
     */
    public function receiveMessage($type, $message)
    {
        $message = trim($message);
        $this->command_log .= "[{$type}] {$message}\n";
        if ($type == 'receive') {
            if (preg_match('/^\-ERR/', $message)) {
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
