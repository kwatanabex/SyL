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
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * コンポーネントに対する操作定義クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ContainerEventComponentOperation
{
    /**
     * 操作タイプ
     * 
     * @var string
     */
    private $type = '';
    /**
     * メソッド名
     * 
     * @var string
     */
    private $name = '';
    /**
     * 静的メソッドフラグ
     * 
     * @var bool
     */
    private $static = false;
    /**
     * メソッドパラメータ配列
     * 
     * @var array
     */
    private $parameters = array();

    /**
     * コンストラクタ
     *
     * @param string 操作タイプ
     * @param string メソッド名
     * @param bool 静的メソッドフラグ
     */
    public function __construct($type, $name=null, $static=false)
    {
        $this->type = $type;
        $this->name = $name;
        $this->static = $static;
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        switch ($name) {
        case 'type':   return $this->type;
        case 'name':   return $this->name;
        case 'static': return $this->static;
        default: throw new SyL_InvalidParameterException("invalid property name ({$name})");
        }
    }

    /**
     * メソッドパラメータを追加する
     * 
     * @param string パラメータタイプ
     * @return string パラメータ値
     */
    public function addParameter($type, $value)
    {
        $this->parameters[] = array($type, $value);
    }

    /**
     * メソッドパラメータを取得する
     * 
     * @return array メソッドパラメータ
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
