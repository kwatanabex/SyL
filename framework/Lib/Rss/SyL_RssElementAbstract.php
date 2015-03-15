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
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** RSS関連の例外クラス */
require_once 'SyL_RssException.php';

/**
 * RSS要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_RssElementAbstract
{
    /**
     * RSSバージョン
     *
     * @var string
     */
    protected $version = '2.0';

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
     * RSSバージョンを取得する
     *
     * @return string RSSバージョン
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * RSSバージョンをセットする
     *
     * @param string RSSバージョン
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public final function apply(XMLWriter $xml)
    {
        switch ($this->version) {
        case '0.91':
            $this->apply0_91($xml);
            break;
        case '1.0':
            $this->apply1_0($xml);
            break;
        case '2.0':
            $this->apply2_0($xml);
            break;
        default:
            throw new SyL_RssInvalidVersionException("invalid rss version ({$version})");
        }
    }

    /**
     * XMLWriterオブジェクトにRSS0.91要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply0_91(XMLWriter $xml)
    {
    }

    /**
     * XMLWriterオブジェクトにRSS1.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply1_0(XMLWriter $xml)
    {
    }

    /**
     * XMLWriterオブジェクトにRSS2.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
    }

}
