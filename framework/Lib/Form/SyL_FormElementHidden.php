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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 隠しフォーム要素クラス
 *
 * 名前 : 要素部品名 : 要素名(name) : 値(value)
 *   1  :      -     :    1         :   1
 *
 * ○インスタンス作成方法
 * // 基底クラスからスタティックメソッドで取得
 * $SyL_FormElementHidden = SyL_FormElement::createElement( 'hidden', 'name', '名前' );
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementHidden extends SyL_FormElementAbstract
{
    /**
     * フォーム要素表示タイプ
     *
     * 0 - 入力
     * 1 - 表示のみ
     * 2 - hidden
     *
     * @var int
     */
    private $view_type = 2;

    /**
     * フォーム要素が表示要素か判定する
     *
     * @param bool フォーム要素表示フラグ
     * @return bool true: 表示要素、false: 表示要素以外
     */
    public function isReadOnly($read_only=null)
    {
        return false;
    }

    /**
     * フォーム要素が隠し要素か判定する
     *
     * @param bool フォーム要素隠しフラグ
     * @return bool true: 隠し要素、false: 隠し要素以外
     */
    public function isHidden($hidden=null)
    {
        return true;
    }

    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        return $this->getHtmlHidden();
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        return $this->getHtmlHidden();
    }
}
