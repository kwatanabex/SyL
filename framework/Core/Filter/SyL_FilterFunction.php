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
 * 汎用関数適用フィルタクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Filter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_FilterFunction extends SyL_FilterAbstract
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
        call_user_func_array(array($data, 'applyArray'), $paremeters);
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
        $this->preActionProcess($data, $context, $paremeters);
    }

    /**
     * 出力前フィルタメソッド
     *
     * @param SyL_ViewAbstract 表示オブジェクト
     * @param array フィルタパラメータ
     */
    protected function preRenderProcess(SyL_ViewAbstract $view, array $paremeters)
    {
        $funcname = array_shift($paremeters);
        $render = $view->getRender();
        array_unshift($paremeters, $render);
        $render = call_user_func_array($funcname, $paremeters);
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
