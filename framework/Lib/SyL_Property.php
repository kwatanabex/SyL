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
 * 汎用プロパティクラス
 *
 * @package    SyL.Lib
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_Property implements IteratorAggregate
{
    /**
     * プロパティ配列
     * 
     * @var array
     */
    private $properties = array();
    /**
     * 固定化配列フラグ
     * 
     * @var bool
     */
    private $fixed = false;

    /**
     * コンストラクタ
     * 
     * @param array プロパティ配列
     * @param bool 固定化配列フラグ
     */
    public function __construct(array $properties=array(), $fixed=false)
    {
        $this->sets($properties);
        $this->fixed = $fixed;
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function __set($name, $value) 
    {
        $this->set($name, $value);
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        return $this->get($name);
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function set($name, $value)
    {
        if ($this->fixed && !$this->is($name)) {
            throw new SyL_InvalidOperationException("property object fixed. property not found ({$name})");
        }
        $this->properties[$name] = $value;
    }

    /**
     * 複数プロパティをセットする
     *
     * @param array プロパティ配列
     */
    public function sets(array $values)
    {
        if ($this->fixed) {
            foreach ($values as $name => $value) {
                if (!$this->is($name)) {
                    throw new SyL_InvalidOperationException("property object fixed ({$name})");
                }
            }
        }
        $this->properties = array_merge($this->properties, $values);
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function get($name)
    {
        if ($this->is($name)) {
            return $this->properties[$name];
        } else {
            if ($this->fixed) {
                throw new SyL_InvalidOperationException("property object fixed ({$name})");
            }
            return null;
        }
    }

    /**
     * 全プロパティを取得する
     * 
     * @return array 全プロパティ値
     */
    public function gets()
    {
        return $this->properties;
    }

    /**
     * プロパティを削除する
     * 
     * @param string プロパティ名
     */
    public function delete($name)
    {
        if ($this->fixed) {
            throw new SyL_InvalidOperationException("property object fixed ({$name})");
        }
        if ($this->is($name)) {
            unset($this->properties[$name]);
        }
    }

    /**
     * 全プロパティを削除する
     */
    public function deletes()
    {
        if ($this->fixed) {
            throw new SyL_InvalidOperationException("property object fixed ({$name})");
        }
        $this->properties = array();
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

    /**
     * イテレータを取得する
     * 
     * @return ArrayIterator イテレータ
     */
    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }
}
