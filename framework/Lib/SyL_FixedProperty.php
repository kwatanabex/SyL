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
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** SyL 汎用例外クラス */
require_once dirname(__FILE__) . '/Exception/SyL_Exception.php';

/**
 * 汎用プロパティクラス（パラメータ固定）
 *
 * @package    SyL.Lib
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_FixedProperty
{
    /**
     * プロパティ配列
     * 
     * @var array
     */
    protected $properties = array();

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        if ($this->is($name)) {
            return $this->properties[$name];
        } else {
            throw new SyL_InvalidParameterException("invalid property ({$name})");
        }
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function __set($name, $value)
    {
        if ($this->is($name)) {
            $this->properties[$name] = $this->validate($name, $value);
        } else {
            throw new SyL_InvalidParameterException("invalid property ({$name})");
        }
    }

    /**
     * プロパティを検証する
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    protected function validate($name, $value)
    {
        return $value;
    }

    /**
     * プロパティの存在を確認する
     * 
     * @param string プロパティ名
     * @return bool true: プロパティあり、false: プロパティ無し
     */
    public function is($name)
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * プロパティの数を取得する
     * 
     * @return int プロパティの数
     */
    public function getLength()
    {
        return count($this->properties);
    }

    /**
     * 全プロパティに指定関数を適用する
     * 
     * @param string 関数名
     * @param mixed パラメータ
     * @param mixed ...
     */
    public function apply($func)
    {
        $func_args = func_get_args();
        $func = array_shift($func_args);
        self::applyRecursive($this->properties, $func, $func_args);
    }

    /**
     * 全プロパティに指定関数を適用する（パラメータ配列ver.）
     * 
     * @param string 関数名
     * @param array パラメータ配列
     */
    public function applyArray($func, $func_args=array())
    {
        self::applyRecursive($this->properties, $func, $func_args);
    }

    /**
     * 全プロパティに指定関数を再帰的に適用する
     * 
     * @param array 全プロパティ（参照渡し）
     * @param string 関数名
     * @param array パラメータ配列
     */
    protected static function applyRecursive(&$properties, $func, array $args)
    {
        if (is_array($properties)) {
            foreach($properties as $name => $value) {
                self::applyRecursive($properties[$name], $func, $args);
            }
        } else {
            if (is_scalar($properties)) {
                if (count($args) > 0) {
                    $properties = call_user_func_array($func, array_merge((array)$properties, $args));
                } else {
                    $properties = call_user_func_array($func, (array)$properties);
                }
            }
        }
    }
}
