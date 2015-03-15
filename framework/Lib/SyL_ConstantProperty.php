<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
 *
 * PHP version 5 (>= 5.2.10)
 *
 * Copyright (C) 2006-2014 k.watanabe
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
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2014 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** SyL 汎用例外クラス */
require_once dirname(__FILE__) . '/Exception/SyL_Exception.php';

/**
 * 汎用プロパティクラス（定数）
 *
 * @package    SyL.Lib
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2014 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ConstantProperty
{
    /**
     * プロパティ一覧を取得する
     * 
     * @return array プロパティ一覧
     */
    public static function getList()
    {
        static $constants = null;
        if ($constants === null) {
            $ref = new ReflectionClass (get_called_class());
            $constants = $ref->getConstants();
        }
        return $constants;
    }

    /**
     * 最初に検索されたプロパティ値のプロパティ名を取得する
     * 
     * @param mixed プロパティ値
     * @return string プロパティ名
     */
    public static function getName($value)
    {
        foreach (self::getList() as $name => $value1) {
            if ($value == $value1) {
                return $name;
            }
        }

        throw new SyL_InvalidParameterException("property name not found ({$value})");
    }
}
