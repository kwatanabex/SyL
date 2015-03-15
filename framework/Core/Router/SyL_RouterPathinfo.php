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
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * PATH_INFO環境変数からフレームワーク遷移情報を取得するクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RouterPathinfo extends SyL_RouterAbstract
{
    /**
     * PATHINFO 環境変数に指定されたファイル名の拡張子
     * 
     * @var string
     */
    protected $url_ext = '.html';

    /**
     * 遷移情報オブジェクトのプロパティを作成
     *
     * @param SyL_Data データオブジェクト
     * @return array array(アクションファイル, アクションメソッド, テンプレートファイル)
     */
    protected function createActionInfo(SyL_Data $data)
    {
        $pathinfo = SyL_RequestAbstract::getInstance()->getServerVar('PATH_INFO');

        $action_file   = '';
        if (($pathinfo === null) || ($pathinfo === '')) {
            $action_file = '/' . $this->action_default_file;
        } else if ($pathinfo[0] == '/') {
            if (substr($pathinfo, -1) == '/') {
                $action_file = implode('/', array_map('ucfirst', explode('/', $pathinfo))) . $this->action_default_file;
            } else {
                // 拡張子チェック
                if (!preg_match('/^(.+)(' . preg_quote($this->url_ext) . ')$/', $pathinfo, $matches)) {
                    throw new SyL_RouterNotFoundException("invalid extension ({$pathinfo})");
                }
                $action_file = implode('/', array_map('ucfirst', explode('/', $matches[1]))) . $this->action_file_ext;
            }
        } else {
            throw new SyL_RouterInvalidPathException("invalid pathinfo ({$pathinfo})");
        }

        $template_file = substr($action_file, 0, intval('-' . strlen($this->action_file_ext))) . $this->template_ext;

        return array($action_file, $template_file);
    }
}
