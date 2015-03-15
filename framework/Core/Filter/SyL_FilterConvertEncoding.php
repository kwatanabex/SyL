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
 * @subpackage SyL.Core.Filter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * SyL環境情報表示フィルタクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Filter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_FilterConvertEncoding extends SyL_FilterAbstract
{
    /**
     * アクション実行前フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param array フィルタパラメータ
     */
    protected function preActionProcess(SyL_ContextAbstract $context, SyL_Data $data, array $paremeters)
    {
        if (isset($paremeters[1])) {
            $data->apply('mb_convert_encoding', $paremeters[0], $paremeters[1]);
        } else {
            $data->apply('mb_convert_encoding', $paremeters[0]);
        }
    }

    /**
     * アクション実行後フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param array フィルタパラメータ
     */
    protected function postActionProcess(SyL_ContextAbstract $context, SyL_Data $data, array $paremeters)
    {
        $this->preAction($data, $context, $paremeters);
    }

    /**
     * 出力前フィルタメソッド
     *
     * @param SyL_ViewAbstract 表示オブジェクト
     * @param array フィルタパラメータ
     */
    protected function preRenderProcess(SyL_ViewAbstract $view, array $paremeters)
    {
        $render = $view->getRender();
        $content_type = trim($view->getContentType());
        // content-type 変換
        if ($content_type) {
            if (preg_match('/^(.+;[ ]*charset=)(.+)$/i', $content_type, $matches)) {
                $content_type = $matches[1] . $to_encoding;
            } else {
                if (substr($content_type, -1) != ';') {
                    $content_type .= ';';
                }
                $content_type .= " charset={$to_encoding}";
            }
            $view->setContentType($content_type);
        }
        if (isset($paremeters[1])) {
            $render = mb_convert_encoding($render, $paremeters[0], $paremeters[1]);
        } else {
            $render = mb_convert_encoding($render, $paremeters[0]);
        }
        $view->setRender($render);
    }

    /**
     * 出力後フィルタメソッド
     *
     * @param array フィルタパラメータ
     */
    protected function postRenderProcess(array $paremeters)
    {
    }

}
