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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** フィルタ例外クラス */
require_once 'SyL_FilterException.php';

/**
 * フィルタクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Filter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 **/
abstract class SyL_FilterAbstract implements SyL_ContainerComponentInterface
{
    /**
     * アクション実行前フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param mixed その他パラメータ
     * @param ...
     */
    public final function preAction(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $func_args = func_get_args();
        array_shift($func_args);
        array_shift($func_args);

        SyL_Logger::trace('filter.preActionProcess start');
        $this->preActionProcess($context, $data, $func_args);
    }

    /**
     * アクション実行前フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param array フィルタパラメータ
     */
    protected function preActionProcess(SyL_ContextAbstract $context, SyL_Data $data, array $paremeters) {}

    /**
     * アクション実行後フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param mixed その他パラメータ
     * @param ...
     */
    public final function postAction(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $func_args = func_get_args();
        array_shift($func_args);
        array_shift($func_args);

        SyL_Logger::trace('filter.preActionProcess start');
        $this->preActionProcess($context, $data, $func_args);
    }

    /**
     * アクション実行後フィルタメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @param array フィルタパラメータ
     */
    protected function postActionProcess(SyL_ContextAbstract $context, SyL_Data $data, array $paremeters) {}

    /**
     * 出力前フィルタメソッド
     *
     * @param SyL_ViewAbstract 表示オブジェクト
     * @param mixed その他パラメータ
     * @param ...
     */
    public final function preRender(SyL_ViewAbstract $view)
    {
        $func_args = func_get_args();
        array_shift($func_args);

        SyL_Logger::trace('filter.preRenderProcess start');
        $this->preRenderProcess($view, $func_args);
    }

    /**
     * 出力前フィルタメソッド
     *
     * @param SyL_ViewAbstract 表示オブジェクト
     * @param array フィルタパラメータ
     */
    protected function preRenderProcess(SyL_ViewAbstract $view, array $paremeters) {}

    /**
     * 出力後フィルタメソッド
     *
     * @param mixed その他パラメータ
     * @param ...
     */
    public final function postRender(array $paremeters)
    {
        SyL_Logger::trace('filter.postRenderProcess start');
        $this->postRenderProcess($paremeters);
    }

    /**
     * 出力後フィルタメソッド
     *
     * @param array フィルタパラメータ
     */
    protected function postRenderProcess(array $paremeters) {}
}
