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
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';

/**
 * 検証設定XMLファイル読み込みクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationConfigXmlReader extends SyL_XmlParserAbstract
{
    /**
     * 設定ファイル配列
     * 
     * @var array
     */
    private $config = array();
    /**
     * 検証設定配列（名前）
     *
     * @var array
     */
    private $config_name = array();

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        static $i = -1;
        static $validation = '';

        // XML解析結果を取得
        switch ($current_path) {
        case '/syl-validation/validation':
            $validation = $attributes['name'];
            $this->config[$validation]      = array();
            $this->config_name[$validation] = isset($attributes['display']) ? $attributes['display'] : $validation;
            $i = -1;
            break;
        case '/syl-validation/validation/validator':
            $i++;
            $this->config[$validation][$i]['validator'] = $attributes['name'];
            $this->config[$validation][$i]['message']   = $attributes['message'];
            break;
        case '/syl-validation/validation/validator/option':
            $this->config[$validation][$i]['options'][$attributes['name']] = $text;
            break;
        }
    }

    /**
     * XML設定値を取得する
     * 
     * @return array XML設定値
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * XML設定値（名前）を取得する
     * 
     * @return array XML設定値（名前）
     */
    public function getDisplayName()
    {
        return $this->config_name;
    }
}
