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
 * 表示要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementLabel extends SyL_FormElementAbstract
{
    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        $this->setValue($this->display_name);
        return $this->getHtmlSpan($this->name, $this->encode($this->display_name)) . $this->getHtmlHidden();
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        return $this->getHtmlTag();
    }
}
