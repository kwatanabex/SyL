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
 * @subpackage SyL.Core.Logger
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ログレベル */
define('SYL_LOG_NONE',   0);
define('SYL_LOG_ERROR',  1);
define('SYL_LOG_WARN',   2);
define('SYL_LOG_NOTICE', 4);
define('SYL_LOG_INFO',   8);
define('SYL_LOG_DEBUG', 16);
define('SYL_LOG_TRACE', 32);

/** ログ出力インターフェイス */
require_once 'SyL_LoggerInterface.php';
/** ログをファイルに出力するクラス */
require_once 'SyL_LoggerFile.php';

/**
 * ログを出力するクラス
 *
 * フレームワークの起動時に初期化される。
 * 静的クラスなので、SyL_Logger::foo としてコール。
 * 各ログレベルは、フレームワーク初期ファイル SyL.php 内に定数として定義されている。
 *
 * 出力ログレベルの設定は、defines.xml に SYL_LOG として定義されている。
 *
 * ログが出力されるディレクトリは、{SYL_PROJECT_DIR} /var/logs/ {SYL_APP_NAME} で、
 * 日付ごとに出力される。
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Logger
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_Logger
{
    /**
     * ログファイルオブジェクト
     * 
     * @var SyL_LoggerFile
     */
    private static $singleton = null;
    /**
     * ログレベル
     * 
     * @var int
     */
    private static $mode = SYL_LOG_WARN;

    /**
     * コンストラクタ
     */
    private function __construct()
    {
    }

    /**
     * ロガーの初期化
     *
     * @param int 出力ログレベル
     */
    public static function startup($mode = SYL_LOG_WARN)
    {
        if (self::$singleton != null) {
            throw new SyL_InvalidOperationException('already startup');
        }

        self::$mode = $mode;
        if ($mode > SYL_LOG_NONE) {
            $filename = SYL_APP_LOG_DIR . '/SyL_{YYYY}{MM}{DD}.log';
            self::$singleton = new SyL_LoggerFile($filename);
            self::$singleton->open();
        }
    }

    /**
     * ログレベルを取得する
     *
     * @return int ログレベル
     */
    public static function getMode()
    {
        return self::$mode;
    }

    /**
     * ロガーの終了
     */
    public static function shutdown()
    {
        if (self::$singleton != null) {
            self::$singleton->close();
            self::$singleton = null;
        }
    }

    /**
     * ログの出力（ERROR）
     *
     * @param mixed 出力メッセージ
     */
    public static function error($message)
    {
        if (self::$mode >= SYL_LOG_ERROR) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('error', $message);
        }
    }

    /**
     * ログの出力（WARN）
     *
     * @param string 出力メッセージ
     */
    public static function warn($message)
    {
        if (self::$mode >= SYL_LOG_WARN) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('warn', $message);
        }
    }

    /**
     * ログの出力（NOTICE）
     *
     * @param string 出力メッセージ
     */
    public static function notice($message)
    {
        if (self::$mode >= SYL_LOG_NOTICE) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('notice', $message);
        }
    }

    /**
     * ログの出力（INFO）
     *
     * @param string 出力メッセージ
     */
    public static function info($message)
    {
        if (self::$mode >= SYL_LOG_INFO) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('info', $message);
        }
    }

    /**
     * ログの出力（DEBUG）
     *
     * @param string 出力メッセージ
     */
    public static function debug($message)
    {
        if (self::$mode >= SYL_LOG_DEBUG) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('debug', $message);
        }
    }

    /**
     * ログの出力（DEBUG）
     *
     * @param string 出力メッセージ
     */
    public static function trace($message)
    {
        if (self::$mode >= SYL_LOG_TRACE) {
            if ($message instanceof Exception) {
                $message = self::getExceptionMessage($message);
            }
            self::$singleton->log('trace', $message);
        }
    }

    /**
     * 例外メッセージを取得する
     *
     * @param Exception 例外
     * @return string 例外メッセージ
     */
    private static function getExceptionMessage(Exception $e)
    {
        if (class_exists('SyL_ErrorHandlerAbstract')) {
            return SyL_ErrorHandlerAbstract::getExceptionMessage($e);
        } else {
            throw $e;
        }
    }
}
