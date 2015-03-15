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
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス */
require_once dirname(__FILE__) . '/../SyL_Property.php';

/**
 * 結果セットレコードクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbRecord extends SyL_Property
{
    /**
     * 値セット判定フラグ
     * 
     * @var array
     */
    private $setting_start = false;
    /**
     * 値がセットされたカラムの判定フラグ
     * 
     * @var array
     */
    private $setting_values = array();

    /**
     * 更新用に値のセットを開始するフラグ
     */
    public function startUpdateSetting()
    {
        $this->setting_start = true;
    }

    /**
     * 更新用に値のセットを開始しているか判定
     */
    public function isUpdateSetting()
    {
        return $this->setting_start;
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function set($name, $value)
    {
        $name = strtoupper($name);
        if ($this->setting_start) {
            $this->setting_values[$name] = true;
        }
        parent::set($name, $value);
    }

    /**
     * 複数プロパティをセットする
     *
     * @param array プロパティ配列
     */
    public function sets(array $values)
    {
        $values = array_change_key_case($values, CASE_UPPER);
        if ($this->setting_start) {
            foreach (array_keys($values) as $name) {
                $this->setting_values[$name] = true;
            }
        }
        parent::sets($values);
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
            return parent::get(strtoupper($name));
        } else {
            throw new SyL_KeyNotFoundException("property not found ({$name})");
        }
    }

    /**
     * プロパティの存在を確認する
     * 
     * @param string プロパティ名
     * @return bool true: プロパティあり、false: プロパティ無し
     */
    public function is($name)
    {
        $name = strtoupper($name);
        if ($this->setting_start) {
            return isset($this->setting_values[$name]);
        } else {
            return parent::is($name);
        }
    }
}
