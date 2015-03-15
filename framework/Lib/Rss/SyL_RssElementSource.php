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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * RSS引用元要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementSource extends SyL_RssElementAbstract
{
    /**
     * 引用元
     *
     * @var string
     */
     private $source = null;
    /**
     * 引用元URL
     *
     * @var bool
     */
     private $url = null;

    /**
     * 引用元を取得する
     *
     * @return string 引用元
     */
    public function getSource()
    {
        return $this->source;
    }
    /**
     * 引用元をセットする
     *
     * @param string 引用元
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * 引用元URLを取得する
     *
     * @return bool 引用元URL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 引用元URLをセットする
     *
     * @param bool 引用元URL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * XMLWriterオブジェクトにRSS1.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply1_0(XMLWriter $xml)
    {
        $xml->writeElement('dc:source', $this->source);
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('source');
        if ($this->url !== null) {
            $xml->writeAttribute('url', $url);
        }
        $xml->text($this->source);
        $xml->endElement();
    }
}
