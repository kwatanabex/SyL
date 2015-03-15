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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * コマンドライン用例外ハンドラクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.ErrorHandler
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ErrorHandlerCmd extends SyL_ErrorHandlerAbstract
{
    /**
     * 権限が無い場合の処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected function handleForbiddenError(Exception $e)
    {
        $this->display($e);
    }

    /**
     * リソースがない場合の処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected function handleNotFoundError(Exception $e)
    {
        $this->display($e);
    }

    /**
     * 通常エラー処理
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected function handleError(Exception $e)
    {
        $this->display($e);
    }

    /**
     * エラーメッセージを出力する
     * 
     * @param Exception スローされた例外オブジェクト
     */
    protected function display(Exception $e)
    {
        echo self::getErrorMessage($e) . PHP_EOL;
        echo $this->convertTrace(self::getTrace($e));
    }

    /**
     * エラートレース配列をコマンドライン表示用に変換する
     *
     * @param array エラートレース配列
     * @return string エラートレース文字列
     */
    protected function convertTrace($error_trace)
    {
        $error_trace_result = '';
        if (count($error_trace) > 0) {
            $error_trace_result = PHP_EOL;

            // エラートレース表示の作成
            $error_trace_result .= "------ start Stack Trace ------" . PHP_EOL;
            foreach ($error_trace as $value) {
                $error_trace_result .= "{$value['no']}. [file] {$value['file']} [line] {$value['line']} [function] {$value['function']}" . PHP_EOL;
            }
            $error_trace_result .= "------ end Stack Trace ------" . PHP_EOL;
        }

        return $error_trace_result;
    }
}
