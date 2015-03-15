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
 * @subpackage SyL.Lib.Util
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 特定の文字列を定数値に変換するクラス
 *
 * 特定の文字列とは、{$FOO} のような文字列
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Util
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_UtilReplaceConstant
{

    /**
     * 特定の文字列を定数値に変換する
     *
     * @param string 変換対象文字列
     * @return array フォーム設定ファイル名の配列
     */
    public static function replace($value)
    {
        return preg_replace_callback('/\{\$(\w+)\}/', array(__CLASS__, 'replaceConstants'), $value);
    }

    /**
     * 定数置き換えコールバックメソッド
     *
     * @param array マッチした配列
     * @return mixed マッチした定数値
     */
    private static function replaceConstants($matches)
    {
        if (defined($matches[1])) {
            return constant($matches[1]);
        } else {
            $value = SyL_Config::get($matches[1]);
            if ($value !== null) {
                return $value;
            } else {
                return '{' . $matches[1] . '}';
            }
        }
    }
}