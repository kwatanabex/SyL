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
 * RSS画像要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementImage extends SyL_RssElementAbstract
{
    /**
     * 画像のURL
     *
     * @var string
     */
     private $url = null;
    /**
     * タイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * リンクURL
     *
     * @var string
     */
     private $link = null;
    /**
     * 画像の横幅
     *
     * @var string
     */
     private $width = null;
    /**
     * 画像の縦幅
     *
     * @var string
     */
     private $height = null;
    /**
     * 画像の説明
     *
     * @var string
     */
     private $description = null;

    /**
     * rdf:about属性 (RSS 1.0)
     *
     * @var string
     */
    private $about = null;

    /**
     * 画像のURLを取得する
     *
     * @return string 画像のURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 画像のURLをセットする
     *
     * @param string 画像のURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * タイトルを取得する
     *
     * @return string タイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * タイトルをセットする
     *
     * @param string タイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * リンクURLを取得する
     *
     * @return string リンクURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * リンクURLをセットする
     *
     * @param string リンクURL
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * 画像の横幅を取得する
     *
     * @return string 画像の横幅
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * 画像の横幅をセットする
     *
     * @param string 画像の横幅
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * 画像の縦幅を取得する
     *
     * @return string 画像の縦幅
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * 画像の縦幅をセットする
     *
     * @param string 画像の縦幅
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * 画像の説明を取得する
     *
     * @return string 画像の説明
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * 画像の説明をセットする
     *
     * @param string 画像の説明
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * rdf:about属性を取得する
     *
     * @return string rdf:about属性
     */
    public function getAbout()
    {
        return $this->about;
    }
    /**
     * rdf:about属性をセットする
     *
     * @param string rdf:about属性
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * XMLWriterオブジェクトにRSS0.91要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply0_91(XMLWriter $xml)
    {
        $this->apply2_0($xml);
    }

    /**
     * XMLWriterオブジェクトにRSS1.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply1_0(XMLWriter $xml)
    {
        $xml->startElement('image');
        if ($this->about) {
            $xml->writeAttribute('rdf:about', $this->about);
        } else {
            $xml->writeAttribute('rdf:about', $this->url);
        }

        $xml->writeElement('url', $this->url);
        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);

        $xml->endElement();
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('image');

        $xml->writeElement('url', $this->url);
        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);
        if ($this->width !== null) {
            $xml->writeElement('width', $this->width);
        }
        if ($this->height !== null) {
            $xml->writeElement('height', $this->height);
        }
        if ($this->description !== null) {
            $xml->writeElement('description', $this->description);
        }

        $xml->endElement();
    }
}

