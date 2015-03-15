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
 * @package   SyL.Lib
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * コンソール表示クラス
 *
 * @package   SyL.Lib
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
class SyL_Console
{
    /**
     * シェル接頭辞
     *
     * @var string
     */
    private $shell_prefix = '> ';
    /**
     * シェルとして実行するときの引数
     * ※falseの場合はコマンド実行不可
     *
     * @var string
     */
    private $shell_string = '\!';
    /**
     * 一括実行時にコマンドラインを終了する文字列
     *
     * @var array
     */
    private $exit_strings = array(
      'quit',
      'exit',
      '\q'
    );
    /**
     * コマンドラインに表示するメッセージ
     *
     * [message]
     * [default_message] > 
     *
     * array(
     *   [0] => array( [message], [default_message] ),
     *   ...
     * );
     *
     * @var array
     */
    private $command_messages = array();
    /**
     * コマンドラインから取得した入力値を取得するコールバック関数
     *
     * @var array
     */
    private $callback_func = array();
    /**
     * 表示用変換エンコード
     *
     * @var array
     */
    private $output_encoding = '';
    /**
     * プログラム側のエンコード
     * 
     * @var string
     */
    private $internal_encoding = '';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        if (ob_get_level() == 0) {
            ob_start();
        }
        $this->callback_func = array($this, 'doCallback');
    }

    /**
     * シェル接頭辞をセットする
     *
     * @param string シェル接頭辞
     */
    public function setShellPrefix($shell_prefix)
    {
        $this->shell_prefix = $shell_prefix;
    }

    /**
     * 表示用変換エンコードをセットする
     *
     * @param string 表示用変換エンコード
     */
    public function setOutputEncoding($output_encoding)
    {
        $this->output_encoding = $output_encoding;
    }

    /**
     * プログラム側エンコードをセットする
     *
     * @param string プログラム側エンコード
     */
    public function setInternalEncoding($internal_encoding)
    {
        $this->internal_encoding = $internal_encoding;
    }

    /**
     * 終了文字を追加する
     *
     * @param string 終了文字
     */
    public function addExitString($exit_string)
    {
        $this->exit_strings[] = $exit_string;
    }

    /**
     * シェル文字を変更する
     * ※falseの場合はコマンド実行不可
     *
     * @param string シェル文字
     */
    public function setShellString($shell_string)
    {
        $this->shell_string = $shell_string;
    }

    /**
     * 一括実行用、表示するメッセージをセット
     *
     * @param string 表示メッセージ
     * @param string コマンド補足メッセージ
     */
    public function addMessage($message, $message_default='')
    {
        $this->command_messages[] = array($message, $message_default);
    }

    /**
     * 一括実行用、コマンドラインからの入力値を取得するコールバック関数をセット
     *
     * @param mixed コールバック関数
     */
    public function setCallbackFunc($callback_func)
    {
        $this->callback_func = $callback_func;
    }

    /**
     * デフォルトコールバック関数
     *
     * @param string 入力値
     */
    public function doCallback($console, $return, $shell)
    {
    }

    /**
     * 一括実行スタート
     *
     * @param bool コマンドループフラグ
     */
    public function start($loop=false)
    {
        while (true) {
            $shell = '';
            if ($this->command_messages) {
                $command_message = array_shift($this->command_messages);
                $return = $this->getInput($command_message[0], $command_message[1]);
            } else {
                if (!$loop) {
                    break;
                }
                $return = $this->getInput('');
            }
            if (in_array($return, $this->exit_strings)) {
                break;
            }
            if ($this->shell_string && (substr($return, 0, strlen($this->shell_string)) == $this->shell_string)) {
                $shell = shell_exec(substr($return, strlen($this->shell_string)));
            }
            // コールバック関数実行
            call_user_func_array($this->callback_func, array($this, $return, $shell));
        }
    }

    /**
     * 表示を出力する
     *
     * @param string 表示メッセージ
     * @param bool 改行フラグ
     */
    public function stdout($message, $newline=true)
    {
        $this->stdmessage($message, 'out', $newline);
    }

    /**
     * エラー表示を出力する
     *
     * @param string エラー表示メッセージ
     * @param bool 改行フラグ
     */
    public function stderr($message, $newline=true)
    {
        $this->stdmessage($message, 'err', $newline);
    }

    /**
     * 表示を出力する
     *
     * @param string 表示メッセージ
     * @param string 表示タイプ
     * @param bool 改行フラグ
     */
    private function stdmessage($message, $type='out', $newline=true)
    {
        if (!is_scalar($message)) {
            $message = print_r($message, true);
        }

        if ($this->output_encoding) {
            if ($this->internal_encoding) {
                $message = mb_convert_encoding($message, $this->output_encoding, $this->internal_encoding);
            } else {
                $message = mb_convert_encoding($message, $this->output_encoding);
            }
        }
        if ($newline) {
            $message .= "\n";
        }
        if ($type == 'err') {
            fwrite(STDERR, $message);
        } else {
            fwrite(STDOUT, $message);
        }
        ob_flush();
    }

    /**
     * 入力値を取得する
     *
     * @param string 表示メッセージ
     * @param string コマンド補足メッセージ
     * @return string 入力値
     */
    public function getInput($message, $message_default='')
    {
        if ($message_default != '') {
            $message_default .= ' ';
        }
        $message_default .= $this->shell_prefix;
        if ($message) {
            $this->stdout($message);
        }
        $this->stdout($message_default, false);
        ob_flush();

        return trim(fgets(STDIN,256));
    }
}
