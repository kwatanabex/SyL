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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';

/**
 * フォームページ遷移設定XMLファイル読み込みクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormManagerConfigXmlReader extends SyL_XmlParserAbstract
{
    /**
     * ページ配列
     * 
     * @var array
     */
    private $pages = array();
    /**
     * ページ状態遷移配列
     * 
     * @var array
     */
    private $forwards = array();

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        static $name = null;
        static $subname = null;
        static $validator_name = null;

        switch ($current_path) {
        case '/syl-form-manager/pages/page':
            $id = $attributes['id'];
            $this->pages[$id] = array();
            $this->pages[$id]['type'] = $attributes['type'];
            $this->pages[$id]['name'] = isset($attributes['name']) ? $attributes['name'] : null;
            $this->pages[$id]['file'] = isset($attributes['file']) ? $attributes['file'] : null;
            break;
        case '/syl-form-manager/forwards/forward':
            $name = $attributes['submit'];
            $this->forwards[$name] = array();
            $this->forwards[$name]['from'] = $attributes['from'];
            $this->forwards[$name]['to']   = $attributes['to'];
            break;
        }
    }

    /**
     * ページ配列を取得する
     * 
     * @return array ページ配列
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * ページ状態遷移配列を取得する
     * 
     * @return array ページ状態遷移配列
     */
    public function getForwards()
    {
        return $this->forwards;
    }
}
