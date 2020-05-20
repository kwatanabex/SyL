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
 * WEB用例外ハンドラクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.ErrorHandler
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ErrorHandlerWeb extends SyL_ErrorHandlerAbstract
{
    /**
     * 権限が無い場合の処理
     * 
     * @param Throwable スローオブジェクト
     */
    protected function handleForbiddenError(Throwable $e)
    {
        header('HTTP/1.0 403 Forbidden');
        header('Content-type: text/html; charset=' . SYL_ENCODE_INTERNAL);
        $this->display('403 Forbidden');
    }

    /**
     * リソースがない場合の処理
     * 
     * @param Throwable スローオブジェクト
     */
    protected function handleNotFoundError(Throwable $e)
    {
        header('HTTP/1.0 404 Not Found');
        header('Content-type: text/html; charset=' . SYL_ENCODE_INTERNAL);
        $this->display('404 Not Found');
    }

    /**
     * 通常エラー処理
     * 
     * @param Throwable スローオブジェクト
     */
    protected function handleError(Throwable $e)
    {
        $error_message = self::getErrorMessage($e);

        header('HTTP/1.0 500 Internal Server Error');
        header('Content-type: text/html; charset=' . SYL_ENCODE_INTERNAL);
        $this->display('Internal Server Error', $error_message);
    }

    /**
     * エラーHTMLを出力する
     * 
     * @param string タイトル
     * @param string エラーメッセージ
     */
    protected function display($title, $error_message=null)
    {
        if (!$error_message) {
            $error_message = $title;
        }

            echo <<<PRINT_HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>{$title}</title>
</head>
<body>
<div style="font-weight:bold;">*** internal error occurred ***</div>
<div>{$error_message}</div>
</body>
</html>
PRINT_HTML;
    }

    /**
     * その他ログを保存
     *
     * @return string その他ログ
     */
    protected function getExtraLog()
    {
        $error_message  = '';
        $error_message .= 'GET : '  . print_r($_GET, true);
        $error_message .= 'POST : ' . print_r($_POST, true);
        $error_message .= 'REMOTE_ADDR: ' .    (isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']     : '') . PHP_EOL;
        $error_message .= 'REQUEST_METHOD: ' . (isset($_SERVER['REQUEST_METHOD'])  ? $_SERVER['REQUEST_METHOD']  : '') . PHP_EOL;
        $error_message .= 'REQUEST_URI: ' .    (isset($_SERVER['REQUEST_URI'])     ? $_SERVER['REQUEST_URI']     : '') . PHP_EOL;
        $error_message .= 'USER_AGENT: ' .     (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . PHP_EOL;
        $error_message .= 'REFERRER: ' .       (isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER']    : '') . PHP_EOL;
        return $error_message;
    }
}
