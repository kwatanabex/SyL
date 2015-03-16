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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** AtomPub関連の例外クラス */
require_once 'SyL_AtomException.php';

/**
 * AtomPub要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_AtomElementAbstract
{
    /**
     * 名前空間
     *
     * @var array
     */
    protected static $namespaces = array();
    /**
     * 囲い要素が有効か
     *
     * @var bool
     */
    protected $enable_enclosure = true;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        $method_name = 'get' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            return $this->{$method_name}($name);
        } else {
            throw new SyL_InvalidParameterException("invalid property. getter method not found ({$name})");
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
        $method_name = 'set' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            return $this->{$method_name}($value);
        } else {
            throw new SyL_InvalidParameterException("invalid property. setter method not found ({$name})");
        }
    }

    /**
     * 使用する名前空間を登録する
     * 
     * @param string 名前
     * @param string URI
     */
    public static function registerNamespace($name, $uri)
    {
        self::$namespaces[$name] = $uri;
    }

    /**
     * 名前空間を削除する
     * 
     * @param string 名前
     */
    public static function removeNamespace($name)
    {
        unset(self::$namespaces[$name]);
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public abstract function apply(XMLWriter $xml);
}
