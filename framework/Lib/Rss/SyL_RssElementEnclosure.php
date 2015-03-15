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

/**
 * RSSエンクロージャ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementEnclosure extends SyL_RssElementAbstract
{
    /**
     * URL
     *
     * @var string
     */
     private $url = null;
    /**
     * サイズ
     *
     * @var string
     */
     private $length = null;
    /**
     * MIMEタイプ
     *
     * @var string
     */
     private $type = null;

    /**
     * URLを取得する
     *
     * @return string URL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * URLをセットする
     *
     * @param string URL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * サイズを取得する
     *
     * @return string サイズ
     */
    public function getLength()
    {
        return $this->length;
    }
    /**
     * サイズをセットする
     *
     * @param string サイズ
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * MIMEタイプを取得する
     *
     * @return string MIMEタイプ
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * MIMEタイプをセットする
     *
     * @param string MIMEタイプ
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('enclosure');
        if ($this->url !== null) {
            $xml->writeAttribute('url', $this->url);
        }
        if ($this->length !== null) {
            $xml->writeAttribute('length', $this->length);
        }
        if ($this->type !== null) {
            $xml->writeAttribute('type', $this->type);
        }
        $xml->endElement();
    }
}

