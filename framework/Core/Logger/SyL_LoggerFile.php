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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ファイル追記出力クラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/File/SyL_FileAppender.php';

/**
 * ログをファイルに出力するクラス
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Logger
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_LoggerFile extends SyL_FileAppender implements SyL_LoggerInterface
{
    /**
     * ログに出力する日付フォーマット
     * 
     * 指定する値は、date 関数フォーマット
     * 
     * @var string
     */
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * ログファイルオープンする
     *
     * コンストラクタで指定されたファイルをオープンする。
     * その際、ファイル名に {YYYY}, {MM}, {DD} がある場合は、
     * それぞれカレント年月日に変換される
     */
    public function open()
    {
        // ファイル名日付変換
        $search   = array('{YYYY}', '{MM}', '{DD}');
        $replace  = explode('-', date('Y-m-d'), 3);
        $this->resource_name = str_replace($search, $replace, $this->resource_name);

        parent::open();
        //$this->changePermission(0666);
        $this->setWriteBuffer(0);
    }

    /**
     * ログを出力する
     *
     * 文字列型に変換され出力される。
     *
     * @param string ログレベル名
     * @param string ログ出力メッセージ
     */
    public function log($level, $message)
    {
        $message = self::toString($message);
        $message = sprintf('%s [%s] %s {%s} %s', date(self::DATETIME_FORMAT), $level, SYL_APP_NAME, self::getLoggingClass(), rtrim($message));
        $this->writeln($message);
    }

    /**
     * ログ情報を型によって内容を変更する
     *
     * @param mixed 変更前出力メッセージ
     * @return string 変更後出力メッセージ
     */
    private static function toString($message)
    {
        if (is_scalar($message)) {
            if (is_bool($message)) {
                return $message ? 'true' : 'false';
            } else {
                return (string)$message;
            }
        } else {
            return print_r($message, true);
        }
    }

    /**
     * ロギングしているクラス／メソッドを取得する
     *
     * @return string ロギングしているクラス／メソッド
     */
    private static function getLoggingClass()
    {
        $funcname = '<main>';
        $match    = false;
        foreach (debug_backtrace() as $debug) {
            if (isset($debug['class']) && isset($debug['function'])) {
                if ($debug['class'] == 'SyL_Logger') {
                    $match = true;
                } else {
                    if ($match) {
                        $type = isset($debug['type']) ? $debug['type'] : '.';
                        $funcname = $debug['class'] . $type . $debug['function'] . '()';
                        break;
                    }
                }
            }
        }
        return $funcname;
    }
}
