<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @package    SyL.Core
 * @subpackage SyL.Core.ErrorHandler
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 例外ハンドラクラス
 *
 * この例外クラスを使用する場合は、個別に
 *   set_error_handler
 *   set_exception_handler
 * は使用しないでください。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.ErrorHandler
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ErrorHandlerAbstract
{
    /**
     * フレームワークイベントトリガクラス
     * 
     * @var SyL_EventDispatcher
     */
    private static $dispatcher = null;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * イベントハンドラを登録
     *
     * @param SyL_EventDispatcher フレームワークイベントトリガオブジェクト
     */
    public static function startup(SyL_EventDispatcher $dispatcher)
    {
        if (self::$dispatcher != null) {
            throw new SyL_InvalidOperationException('already startup');
        }

        $classname = '';
        $name = SyL_CustomClass::getErrorHandlerClass();
        if ($name) {
            $classname = SyL_Loader::userLib($name);
            if (!is_subclass_of($classname, __CLASS__)) {
                throw new SyL_InvalidClassException("invalid error handler class `{$classname}'. not extends `" . __CLASS__ . "' class");
            }
        } else {
            $classname = 'SyL_ErrorHandler' . SYL_APP_TYPE;
            include_once $classname . '.php';
        }

        $error_handler = new $classname();
        set_error_handler(array($error_handler, 'triggerError'));
        set_exception_handler(array($error_handler, 'triggerException'));

        self::$dispatcher = $dispatcher;
    }

    /**
     * イベントハンドラを解除
     */
    public static function shutdown()
    {
        if (self::$dispatcher != null) {
            restore_exception_handler();
            restore_error_handler();
        }
    }

    /**
     * コンポーネントを取得
     *
     * @param string コンポーネント名
     * @return SyL_ContainerComponentInterface コンポーネントオブジェクト
     */
    protected static function getComponent($name)
    {
        return self::$dispatcher->getComponent($name);
    }

    /**
     * カスタムエラーハンドラ
     *
     * @param int エラーのレベル
     * @param string エラーメッセージ
     * @param string エラーが発生したファイル名
     * @param int エラーが発生した行番号
     */
    public final function triggerError($errno, $errstr, $errfile, $errline)
    {
        // 現在のエラーレベルからエラー画面を表示するか判定
        if ($errno & error_reporting()) {
            throw new ErrorException($errstr, $errno, $errno, $errfile, $errline);
        } else {
            $error_message = "background logging out of error_reporting. level:{$errno} message: {$errstr} file: {$errfile} line: {$errline}";
            switch ($errno) {
            case 1:
            case 2:
            case 256:
            case 512:
                // ワーニング以上は、エラーとして処理停止
                throw new ErrorException($errstr, $errno, $errno, $errfile, $errline);
                break;
            case 8:
            case 1024:
                SyL_Logger::notice($error_message);
                break;
            default:
                SyL_Logger::debug($error_message);
                break;
            }
        }
    }

    /**
     * カスタム例外ハンドラ
     *
     * @param Exception スローされた例外オブジェクト
     */
    public final function triggerException(Exception $e)
    {
        restore_error_handler();
        restore_exception_handler();

        // エラーイベント実行
        try {
            self::$dispatcher->errorStream();
        } catch (Exception $e) {
            $error_message  = get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
            echo $error_message;
            SyL_Logger::error($error_message);
        }

        if (!headers_sent()) {
            while (ob_get_level()) {
                ob_end_clean();
            }
        }

        $code = $e->getCode();
        if ($code == 0) {
            // 未定義はキャッチできる致命的なエラー
            $code = E_RECOVERABLE_ERROR;
        }

        try {
            if ($e instanceof SyL_ResponseNotFoundException) {
                // 404のみスタックトレースを出力しない
                $this->writeLog($e, E_NOTICE);
                // 404 Not Found
                $this->handleNotFoundError($e);

            } else if ($e instanceof SyL_ResponseForbiddenException) {
                // 403 Forbidden
                $this->writeLog($e, E_NOTICE);
                $this->handleForbiddenError($e);

            } else {
                $this->writeLog($e, $code);
                $this->handleError($e);
            }
        } catch (Exception $e2) {
            echo 'uncatchable error in ' . get_class($this) . ': ' . $e2->getMessage();
            SyL_Logger::error('uncatchable error in ' . get_class($this) . ': ' . $e2->getMessage());
        }
    }

    /**
     * 権限が無い場合の処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected abstract function handleForbiddenError(Exception $e);

    /**
     * リソースがない場合の処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected abstract function handleNotFoundError(Exception $e);

    /**
     * 通常エラー処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected abstract function handleError(Exception $e);

    /**
     * エラーメッセージ
     *
     * @param Exception スローされた例外オブジェクト
     * @return string エラーメッセージ
     */
    protected static function getErrorMessage(Exception $e)
    {
        $code = $e->getCode();
        if ($code == 0) {
            // 未定義はキャッチできる致命的なエラー
            $code = E_RECOVERABLE_ERROR;
        }
        $message = trim($e->getMessage());
        $file    = $e->getFile();
        $line    = $e->getLine();

        $type = '';
        if ($e instanceof SyL_Exception) {
            $type = '[' . get_class($e) . ']';
        } else {
            $type = '[' . self::getErrorType($code) . ']';
        }

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $etmp = $e->getPrevious();
            while ($etmp) {
                $message .= ' -> ' . $etmp->getMessage();
                $etmp = $etmp->getPrevious();
            }
        }
        return sprintf('%s %s in %s on line %s', $type, $message, $file, $line);
    }

    /**
     * エラータイプを返す
     *
     * @param  int    エラーNo
     * @return string エラータイプ名
     */
    private static function getErrorType($code)
    {
        switch ($code) {
        case E_ERROR           : return 'Error';
        case E_WARNING         : return 'Warning';
        case E_PARSE           : return 'Parsing Error';
        case E_NOTICE          : return 'Notice';
        case E_CORE_ERROR      : return 'Core Error';
        case E_CORE_WARNING    : return 'Core Warning';
        case E_COMPILE_ERROR   : return 'Compile Error';
        case E_COMPILE_WARNING : return 'Compile Warning';
        case E_USER_ERROR      : return 'User Error';
        case E_USER_WARNING    : return 'User Warning';
        case E_USER_NOTICE     : return 'User Notice';
        case E_STRICT          : return 'Strict';
        case E_RECOVERABLE_ERROR : return 'Recoverable Error';
        default:
            if (defined('E_DEPRECATED') && ($code == E_DEPRECATED)) {
                return 'Deprecated';
            }
            if (defined('E_USER_DEPRECATED') && ($code == E_USER_DEPRECATED)) {
                return 'User Deprecated';
            }
        }

        return 'unknonwn';
    }

    /**
     * トレース情報を整形
     *
     * @param array Exceptionから取得したトレース情報
     * @return array 整形後トレース情報
     */
    protected static function getTrace(Exception $e)
    {
        $error_trace = array();
        $no = 1;

        foreach (array_reverse($e->getTrace()) as $values) {
            if (isset($values['class'])) {
                // カスタムハンドラメソッド以降を非表示
                if (is_subclass_of($values['class'], 'SyL_ErrorHandlerAbstract')) {
                    break;
                }
            }

            $tmp = array();
            $tmp['no']   = $no++;
            $tmp['file'] = isset($values['file']) ? $values['file'] : '-';
            $tmp['line'] = isset($values['line']) ? $values['line'] : '-';
            if (isset($values['class']) && isset($values['type'])) {
                $tmp['function'] = $values['class'] . $values['type'] . $values['function'];
            } else {
                $tmp['function'] = (isset($values['function']) && ($values['function'] != 'unknown')) ? $values['function'] : '-';
            }
            if (isset($values['args']) && is_array($values['args'])) {
                $formats = array();
                foreach ($values['args'] as $arg) {
                    $format = gettype($arg);
                    switch ($format) {
                    case 'object':
                        $format = get_class($arg);
                        break;
                    case 'resource':
                        $format = get_resource_type($arg);
                        break;
                    }
                    $formats[] = $format;
                }
                $tmp['function'] .= '(' . implode(', ', $formats) . ')';
            }
            array_unshift($error_trace, $tmp);
        }

        $tmp = array();
        $tmp['no']   = $no++;
        $tmp['file'] = $e->getFile();
        $tmp['line'] = $e->getLine();
        $tmp['function'] = '*** ' . get_class($e) . ' ***';
        array_unshift($error_trace, $tmp);

        $previous = $e->getPrevious();
        if ($previous) {
            $error_trace = array_merge($error_trace, self::getTrace($previous));
        }

        return $error_trace;
    }

    /**
     * エラートレースを取得する
     *
     * @param Exception 例外
     * @return string エラートレース
     */
    private static function getTraceMessage(Exception $e)
    {
        $error_message = '';
        foreach (self::getTrace($e) as $values) {
            $error_message .= "[file] "     . $values['file']     . " ";
            $error_message .= "[line] "     . $values['line']     . " ";
            $error_message .= "[function] " . $values['function'] . PHP_EOL;
        }
        return $error_message;
    }

    /**
     * 例外メッセージを取得する
     *
     * @param Exception 例外
     * @return string エラートレース
     */
    public static function getExceptionMessage(Exception $e)
    {
        $error_message  = self::getErrorMessage($e);
        $error_message .= PHP_EOL;
        $error_message .= self::getTraceMessage($e);
        return $error_message;
    }

    /**
     * ログを保存
     *
     * @param Exception 例外
     * @param int エラーコード
     */
    private function writeLog(Exception $e, $code)
    {
        $error_message = self::getExceptionMessage($e);
        $error_message .= PHP_EOL;
        $error_message .= $this->getExtraLog();

        // エラーレベル毎にログ保存
        switch ($code) {
        case E_ERROR:
        case E_USER_ERROR:
            SyL_Logger::error($error_message);
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_RECOVERABLE_ERROR:
            SyL_Logger::warn($error_message);
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_STRICT:
            SyL_Logger::notice($error_message);
            break;
        default:
            SyL_Logger::info($error_message);
        }
    }

    /**
     * その他ログを保存
     *
     * @return string その他ログ
     */
    protected function getExtraLog()
    {
        return '';
    }

    /**
     * エラーメールを送信
     *
     * @param Exception 例外
     * @param string メール送信アドレス
     */
    protected function sendErrorMail(Exception $e, $address)
    {
        // 日付取得
        $date = date('Y-m-d H:i:s');
        // SyL環境
        $syl_version = SYL_VERSION;
        $syl_dir     = SYL_DIR;
        $project_dir = SYL_PROJECT_DIR;
        $appname     = SYL_APP_NAME;
        // トレース付きエラーメッセージ取得
        $error_message  = self::getExceptionMessage($e);
        $error_message .= PHP_EOL;
        $error_message .= $this->getExtraLog();
        // PHP環境
        $version_php = PHP_VERSION;
        $uname       = php_uname();

        $subject = "SyL Framework Error Report - {$appname} - {$date}";
        $body = <<< EOF
SyL Framework Error info. {$date}
----------------------------------------
SyL Framework : {$syl_version} 
SYL_DIR : {$syl_dir} 
SYL_PROJECT_DIR : {$project_dir} 
APP_NAME : {$appname}
----------------------------------------

{$error_message}

----------------------------------------
PHP {$version_php}
{$uname}
EOF;
        // 送信前ログ
        SyL_Logger::notice("send error mail -> {$address}");
        SyL_Logger::info("error mail Subject: {$subject}");
        SyL_Logger::info("error mail From: {$address}");
        SyL_Logger::info("error mail To: {$address}");

        // エラーメール送信処理
        include_once SYL_FRAMEWORK_DIR . '/Lib/Mail/SyL_MailSendAbstract.php';

        $message = SyL_MailAbstract::createMessage();
        $message->setSubject($subject);
        $message->setFrom($address);
        $message->addTo($address);
        $message->setBody($body);

        try {
            $type = SyL_Config::get('SYL_SENDMAIL_CONNECTION_STRING');
            $mail = SyL_MailSendAbstract::createInstance($type);
            $mail->send($message);
            if (method_exists($mail, 'getCommandLog')) {
                SyL_Logger::debug($mail->getCommandLog());
            }
        } catch (Exception $e) {
            echo 'uncatchable error in ' . get_class($this) . ': ' . $e->getMessage();
            SyL_Logger::error('uncatchable error in ' . get_class($this) . ': ' . $e->getMessage());
        }
    }
}
