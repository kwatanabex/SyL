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

/** RSSカテゴリ要素クラス */
require_once 'SyL_RssElementCategory.php';
/** RSSエンクロージャ要素クラス */
require_once 'SyL_RssElementEnclosure.php';
/** RSS一意に特定要素クラス */
require_once 'SyL_RssElementGuid.php';
/** RSS引用元要素クラス */
require_once 'SyL_RssElementSource.php';

/**
 * RSSアイテム要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementItem extends SyL_RssElementAbstract
{
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
     * 説明
     *
     * @var string
     */
    private $description = null;
    /**
     * 著者
     *
     * @var string
     */
    private $author = null;
    /**
     * カテゴリ配列
     *
     * @var array
     */
    private $categories = array();
    /**
     * コメントのページURL
     *
     * @var string
     */
    private $comments = null;
    /**
     * メディアオブジェクト
     *
     * @var SyL_RssElementEnclosure
     */
    private $enclosure = null;
    /**
     * 一意に特定できる文字列
     *
     * @var SyL_RssElementGuid
     */
    private $guid = null;
    /**
     * 発行日時
     *
     * @var DateTime
     */
    private $pub_date = null;
    /**
     * 引用元
     *
     * @var SyL_RssElementSource
     */
    private $source = null;

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
     * 著者を取得する
     *
     * @return string 著者
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * 著者を取得する（dc:rights用エイリアス）
     *
     * @return string 技術担当者
     */
    public function getRights()
    {
        return $this->getAuthor();
    }
    /**
     * 著者をセットする
     *
     * @param string 著者
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * カテゴリ配列を取得する
     *
     * @return array カテゴリ配列
     */
    public function getCategories()
    {
        return $this->categories;
    }
    /**
     * カテゴリ配列をセットする
     *
     * @param array カテゴリ配列
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
    }
    /**
     * カテゴリをセットする
     *
     * @param SyL_RssElementCategory カテゴリ
     */
    public function addCategory(SyL_RssElementCategory $category)
    {
        $this->categories[] = $category;
    }

    /**
     * コメントのページURLを取得する
     *
     * @return string コメントのページURL
     */
    public function getComments()
    {
        return $this->comments;
    }
    /**
     * コメントのページURLをセットする
     *
     * @param string コメントのページURL
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * メディアオブジェクトを取得する
     *
     * @return SyL_RssElementEnclosure メディアオブジェクト
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }
    /**
     * メディアオブジェクトをセットする
     *
     * @param SyL_RssElementEnclosure メディアオブジェクト
     */
    public function setEnclosure(SyL_RssElementEnclosure $enclosure)
    {
        $this->enclosure = $enclosure;
    }

    /**
     * 一意に特定できる文字列を取得する
     *
     * @return SyL_RssElementGuid 一意に特定できる文字列
     */
    public function getGuid()
    {
        return $this->guid;
    }
    /**
     * 一意に特定できる文字列を取得する（dc:identifier用エイリアス）
     *
     * @return SyL_RssElementGuid 一意に特定できる文字列
     */
    public function getIdentifier()
    {
        return $this->getGuid();
    }
    /**
     * 一意に特定できる文字列をセットする
     *
     * @param SyL_RssElementGuid 一意に特定できる文字列
     */
    public function setGuid(SyL_RssElementGuid $guid)
    {
        $this->guid = $guid;
    }

    /**
     * 発行日時を取得する
     *
     * @return DateTime 発行日時
     */
    public function getPubDate()
    {
        return $this->pub_date;
    }
    /**
     * 発行日時を取得する（dc:date用エイリアス）
     *
     * @return DateTime 発行日時
     */
    public function getDate()
    {
        return $this->getPubDate();
    }
    /**
     * 発行日時をセットする
     *
     * @param DateTime 発行日時
     */
    public function setPubDate(DateTime $pub_date)
    {
        $this->pub_date = $pub_date;
    }

    /**
     * 引用元を取得する
     *
     * @return SyL_RssElementSource 引用元
     */
    public function getSource()
    {
        return $this->source;
    }
    /**
     * 引用元をセットする
     *
     * @param SyL_RssElementSource 引用元
     */
    public function setSource(SyL_RssElementSource $source)
    {
        $this->source = $source;
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
        $xml->startElement('item');

        if ($this->title !== null) {
            $xml->writeElement('title', $this->title);
        }
        if ($this->link !== null) {
            $xml->writeElement('link', $this->link);
        }
        if ($this->description !== null) {
            $xml->writeElement('description', $this->description);
        }

        $xml->endElement();
    }

    /**
     * XMLWriterオブジェクトにRSS1.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply1_0(XMLWriter $xml)
    {
        $xml->startElement('item');
        $about = $this->getAbout();
        if ($about) {
            $xml->writeAttribute('rdf:about', $about);
        } else {
            $xml->writeAttribute('rdf:about', $this->getLink());
        }

        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);
        if ($this->description !== null) {
            $xml->writeElement('description', $this->description);
        }

        if ($this->author !== null) {
            $xml->writeElement('dc:rights', $this->author);
        }
        if ($this->guid !== null) {
            $this->guid->setVersion($this->version);
            $this->guid->apply($xml);
        }
        if ($this->pub_date !== null) {
            $xml->writeElement('dc:date', $this->pub_date->format(Datetime::RSS));
        }
        if ($this->source !== null) {
            $this->source->setVersion($this->version);
            $this->source->apply($xml);
        }

        $xml->endElement();
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('item');

        if ($this->title !== null) {
            $xml->writeElement('title', $this->title);
        }
        if ($this->link !== null) {
            $xml->writeElement('link', $this->link);
        }
        if ($this->description !== null) {
            $xml->writeElement('description', $this->description);
        }
        if ($this->author !== null) {
            $xml->writeElement('author', $this->author);
        }
        foreach ($this->categories as &$category) {
            $category->setVersion($this->version);
            $category->apply($xml);
        }
        if ($this->comments !== null) {
            $xml->writeElement('comments', $this->comments);
        }
        if ($this->enclosure !== null) {
            $this->enclosure->setVersion($this->version);
            $this->enclosure->apply($xml);
        }
        if ($this->guid !== null) {
            $this->guid->setVersion($this->version);
            $this->guid->apply($xml);
        }
        if ($this->pub_date !== null) {
            $xml->writeElement('pubDate', $this->pub_date->format(Datetime::RSS));
        }
        if ($this->source !== null) {
            $this->source->setVersion($this->version);
            $this->source->apply($xml);
        }

        $xml->endElement();
    }
}
