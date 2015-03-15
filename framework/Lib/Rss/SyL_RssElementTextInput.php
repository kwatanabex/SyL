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
 * RSSテキストインプット要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementTextInput extends SyL_RssElementAbstract
{
    /**
     * タイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * 説明
     *
     * @var string
     */
     private $description = null;
    /**
     * 名前
     *
     * @var string
     */
     private $name = null;
    /**
     * リンクURL
     *
     * @var string
     */
     private $link = null;

    /**
     * rdf:about属性 (RSS 1.0)
     *
     * @var string
     */
    private $about = null;

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
     * 説明を取得する
     *
     * @return string 説明
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * 説明をセットする
     *
     * @param string 説明
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 名前を取得する
     *
     * @return string 名前
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * 名前をセットする
     *
     * @param string 名前
     */
    public function setName($name)
    {
        $this->name = $name;
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
        $xml->startElement('textInput');
        $about = $this->getAbout();
        if ($about) {
            $xml->writeAttribute('rdf:about', $about);
        } else {
            $xml->writeAttribute('rdf:about', $this->getLink());
        }

        $xml->writeElement('title', $this->title);
        $xml->writeElement('description', $this->description);
        $xml->writeElement('name', $this->name);
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
        $xml->startElement('textInput');

        $xml->writeElement('title', $this->title);
        $xml->writeElement('description', $this->description);
        $xml->writeElement('name', $this->name);
        $xml->writeElement('link', $this->link);

        $xml->endElement();
    }
}

