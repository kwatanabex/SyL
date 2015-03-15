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
 * @subpackage SyL.Lib.Xml.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** サイトマップURLクラス */
require_once 'SyL_XmlSitemapUrl.php';
/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../SyL_XmlParserAbstract.php';

/**
 * サイトマップリーダクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Xml.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_XmlSitemapReader extends SyL_XmlParserAbstract
{
    /**
     * URL配列
     * 
     * @var array
     */
    private $urls = array();

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        $i = count($this->urls) - 1;
        switch ($current_path) {
        case '/urlset/url':
            $this->urls[$i+1] = new SyL_XmlSitemapUrl();
            break;
        case '/urlset/url/loc':        $this->urls[$i]->loc        = $text; break;
        case '/urlset/url/lastmod':    $this->urls[$i]->lastmod    = $text; break;
        case '/urlset/url/changefreq': $this->urls[$i]->changefreq = $text; break;
        case '/urlset/url/priority':   $this->urls[$i]->priority   = $text; break;
        }
    }

    /**
     * URL配列を全て取得する
     *
     * @return array URL配列
     */
    public function getUrls()
    {
        return $this->urls;
    }
}
