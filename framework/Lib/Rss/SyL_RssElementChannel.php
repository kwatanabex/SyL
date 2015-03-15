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

/** RSSカテゴリ要素クラス */
require_once 'SyL_RssElementCategory.php';
/** RSSクラウド要素クラス */
require_once 'SyL_RssElementCloud.php';
/** RSS画像要素クラス */
require_once 'SyL_RssElementImage.php';
/** RSSテキストインプット要素クラス */
require_once 'SyL_RssElementTextInput.php';
/** RSSアイテム要素クラス */
require_once 'SyL_RssElementItem.php';

/**
 * RSSチャネル要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementChannel extends SyL_RssElementAbstract
{
    /**
     * チャネル名
     *
     * @var string
     */
    private $title = null;
    /**
     * チャネルURL
     *
     * @var string
     */
    private $link = null;
    /**
     * チャネルの説明文
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
     * 言語
     *
     * @var string
     */
    private $language = null;
    /**
     * 著作権表示
     *
     * @var string
     */
    private $copyright = null;
    /**
     * コンテンツ編集者
     *
     * @var string
     */
    private $managing_editor = null;
    /**
     * 技術担当者
     *
     * @var string
     */
    private $web_master = null;
    /**
     * 発行日時
     *
     * @var DateTime
     */
    private $pub_date = null;
    /**
     * 変更された最終日時
     *
     * @var DateTime
     */
    private $last_build_date = null;
    /**
     * カテゴリ配列
     *
     * @var array
     */
    private $categories = array();
    /**
     * 生成プログラム
     *
     * @var string
     */
    private $generator = null;
    /**
     * フォーマット文書を示すURL
     *
     * @var string
     */
    private $docs = null;
    /**
     * 更新通知用情報
     *
     * @var SyL_RssElementCloud
     */
    private $cloud = null;
    /**
     * 有効期間
     *
     * @var array
     */
    private $ttls = array();
    /**
     * 画像
     *
     * @var SyL_RssElementImage
     */
    private $image = null;
    /**
     * PICSレーティング
     *
     * @var string
     */
    private $rating = null;
    /**
     * テキスト入力ボックス
     *
     * @var SyL_RssElementTextInput
     */
    private $text_input = null;
    /**
     * スキップ時間
     *
     * @var array
     */
    private $skip_hours = array();
    /**
     * スキップ曜日
     *
     * @var array
     */
    private $skip_days = array();
    /**
     * アイテム要素
     *
     * @var array
     */
    private $items = array();

    /**
     * チャンネル名を取得する
     *
     * @return string チャンネル名
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * チャンネル名をセットする
     *
     * @param string チャンネル名
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * チャネルURLを取得する
     *
     * @return string チャネルURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * チャネルURLをセットする
     *
     * @param string チャネルURL
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
     * チャネルの説明文を取得する
     *
     * @return string チャネルの説明文
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * チャネルの説明文をセットする
     *
     * @param string チャネルの説明文
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 言語を取得する
     *
     * @return string 言語
     */
    public function getLanguage()
    {
        return $this->language;
    }
    /**
     * 言語をセットする
     *
     * @param string 言語
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * 著作権表示を取得する
     *
     * @return string 著作権表示
     */
    public function getCopyright()
    {
        return $this->copyright;
    }
    /**
     * 著作権表示を取得する（dc:rights用エイリアス）
     *
     * @return string 著作権表示
     */
    public function getRights()
    {
        return $this->getCopyright();
    }
    /**
     * 著作権表示をセットする
     *
     * @param string 著作権表示
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * コンテンツ編集者を取得する
     *
     * @return string コンテンツ編集者
     */
    public function getManagingEditor()
    {
        return $this->managing_editor;
    }
    /**
     * コンテンツ編集者をセットする
     *
     * @param string コンテンツ編集者
     */
    public function setManagingEditor($managing_editor)
    {
        $this->managing_editor = $managing_editor;
    }

    /**
     * 技術担当者を取得する
     *
     * @return string 技術担当者
     */
    public function getWebMaster()
    {
        return $this->web_master;
    }
    /**
     * 技術担当者を取得する（dc:creator用エイリアス）
     *
     * @return string 技術担当者
     */
    public function getCreator()
    {
        return $this->getWebMaster();
    }
    /**
     * 技術担当者をセットする
     *
     * @param string 技術担当者
     */
    public function setWebMaster($web_master)
    {
        $this->web_master = $web_master;
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
     * 変更された最終日時を取得する
     *
     * @return DateTime 変更された最終日時
     */
    public function getLastBuildDate()
    {
        return $this->last_build_date;
    }
    /**
     * 変更された最終日時をセットする
     *
     * @param DateTime 変更された最終日時
     */
    public function setLastBuildDate(DateTime $last_build_date)
    {
        $this->last_build_date = $last_build_date;
    }

    /**
     * カテゴリを取得する
     *
     * @return array カテゴリ
     */
    public function getCategories()
    {
        return $this->categories;
    }
    /**
     * カテゴリをセットする
     *
     * @param array カテゴリ
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
    }
    /**
     * カテゴリを追加する
     *
     * @param SyL_RssElementCategory カテゴリ
     */
    public function addCategory(SyL_RssElementCategory $category)
    {
        $this->categories[] = $category;
    }

    /**
     * 生成プログラムを取得する
     *
     * @return string 生成プログラム
     */
    public function getGenerator()
    {
        return $this->generator;
    }
    /**
     * 生成プログラムをセットする
     *
     * @param string 生成プログラム
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * フォーマット文書を示すURLを取得する
     *
     * @return string フォーマット文書を示すURL
     */
    public function getDocs()
    {
        return $this->docs;
    }
    /**
     * フォーマット文書を示すURLをセットする
     *
     * @param string フォーマット文書を示すURL
     */
    public function setDocs($docs)
    {
        $this->docs = $docs;
    }

    /**
     * 更新通知用情報を取得する
     *
     * @return SyL_RssElementCloud 更新通知用情報
     */
    public function getCloud()
    {
        return $this->cloud;
    }
    /**
     * 更新通知用情報をセットする
     *
     * @param SyL_RssElementCloud 更新通知用情報
     */
    public function setCloud(SyL_RssElementCloud $cloud)
    {
        $this->cloud = $cloud;
    }

    /**
     * 有効期間を取得する
     *
     * @return array 有効期間
     */
    public function getTtls()
    {
        return $this->ttls;
    }
    /**
     * 有効期間をセットする
     *
     * @param array 有効期間
     */
    public function setTtls(array $ttls)
    {
        $this->ttls = $ttls;
    }
    /**
     * 有効期間を追加する
     *
     * @param string 有効期間
     */
    public function addTtl($ttl)
    {
        $this->ttls[] = $ttl;
    }

    /**
     * 画像を取得する
     *
     * @return SyL_RssElementImage 画像
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * 画像をセットする
     *
     * @param SyL_RssElementImage 画像
     */
    public function setImage(SyL_RssElementImage $image)
    {
        $this->image = $image;
    }

    /**
     * PICSレーティングを取得する
     *
     * @return string PICSレーティング
     */
    public function getRating()
    {
        return $this->rating;
    }
    /**
     * PICSレーティングをセットする
     *
     * @param string PICSレーティング
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * テキスト入力ボックスを取得する
     *
     * @return SyL_RssElementTextInput テキスト入力ボックス
     */
    public function getTextInput()
    {
        return $this->text_input;
    }
    /**
     * テキスト入力ボックスをセットする
     *
     * @param SyL_RssElementTextInput テキスト入力ボックス
     */
    public function setTextInput(SyL_RssElementTextInput $text_input)
    {
        $this->text_input = $text_input;
    }

    /**
     * スキップ時間を取得する
     *
     * @return array スキップ時間
     */
    public function getSkipHours()
    {
        return $this->skip_hours;
    }
    /**
     * スキップ時間をセットする
     *
     * @param array スキップ時間
     */
    public function setSkipHours(array $skip_hours)
    {
        $this->skip_hours = $skip_hours;
    }
    /**
     * スキップ時間を追加する
     *
     * @param string スキップ時間
     */
    public function addSkipHour($skip_hour)
    {
        $this->skip_hours[] = $skip_hour;
    }

    /**
     * スキップ曜日を取得する
     *
     * @return array スキップ曜日
     */
    public function getSkipDays()
    {
        return $this->skip_days;
    }
    /**
     * スキップ曜日をセットする
     *
     * @param array スキップ曜日
     */
    public function setSkipDays(array $skip_days)
    {
        $this->skip_days = $skip_days;
    }
    /**
     * スキップ曜日を追加する
     *
     * @param string スキップ曜日
     */
    public function addSkipDay($skip_day)
    {
        $this->skip_days[] = $skip_day;
    }

    /**
     * アイテム要素を取得する
     *
     * @return array アイテム要素
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * アイテム要素をセットする
     *
     * @param array アイテム要素
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
    /**
     * アイテム要素を追加する
     *
     * @param SyL_RssElementItem アイテム要素
     */
    public function addItem(SyL_RssElementItem $item)
    {
        $this->items[] = $item;
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
        $xml->startElement('channel');
        if ($this->about) {
            $xml->writeAttribute('rdf:about', $this->about);
        } else {
            $xml->writeAttribute('rdf:about', $this->link);
        }

        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);
        $xml->writeElement('description', $this->description);

        if ($this->copyright !== null) {
            $xml->writeElement('dc:right', $this->copyright);
        }
        if ($this->language !== null) {
            $xml->writeElement('dc:language', $this->language);
        }
        if ($this->pub_date !== null) {
            $xml->writeElement('dc:date', $this->pub_date->format(Datetime::RSS));
        }
        if ($this->web_master !== null) {
            $xml->writeElement('dc:creator', $this->web_master);
        }

        if ($this->image !== null) {
            $xml->startElement('image');
            $about = $this->image->getAbout();
            if ($about) {
                $xml->writeAttribute('rdf:resource', $about);
            } else {
                $xml->writeAttribute('rdf:resource', $this->image->getUrl());
            }
            $xml->endElement();
        }

        if ($this->text_input !== null) {
            $xml->startElement('textinput');
            $about = $this->text_input->getAbout();
            if ($about) {
                $xml->writeAttribute('rdf:resource', $about);
            } else {
                $xml->writeAttribute('rdf:resource', $this->image->getLink());
            }
            $xml->endElement();
        }

        $xml->startElement('items');
        $xml->startElement('rdf:Seq');
        foreach ($this->items as &$item) {
            $xml->startElement('rdf:li');
            $about = $item->getAbout();
            if ($about) {
                $xml->writeAttribute('resource', $about);
            } else {
                $xml->writeAttribute('resource', $item->getLink());
            }
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endElement();

        $xml->endElement();
    }

    /**
     * XMLWriterオブジェクトにRSS2.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('channel');

        $xml->writeElement('title', $this->title);
        $xml->writeElement('link', $this->link);
        $xml->writeElement('description', $this->description);
        if ($this->language !== null) {
            $xml->writeElement('language', $this->language);
        }
        if ($this->copyright !== null) {
            $xml->writeElement('copyright', $this->copyright);
        }
        if ($this->managing_editor !== null) {
            $xml->writeElement('managingEditor', $this->managing_editor);
        }
        if ($this->web_master !== null) {
            $xml->writeElement('webMaster', $this->web_master);
        }
        if ($this->pub_date !== null) {
            $xml->writeElement('pubDate', $this->pub_date->format(Datetime::RSS));
        }
        if ($this->last_build_date !== null) {
            $xml->writeElement('lastBuildDate', $this->last_build_date->format(Datetime::RSS));
        }
        foreach ($this->categories as &$category) {
            $category->setVersion($this->version);
            $category->apply($xml);
        }
        if ($this->generator !== null) {
            $xml->writeElement('generator', $this->generator);
        }
        if ($this->docs !== null) {
            $xml->writeElement('docs', $this->docs);
        }
        if ($this->cloud !== null) {
            $this->cloud->setVersion($this->version);
            $this->cloud->apply($xml);
        }
        foreach ($this->ttls as &$ttl) {
            $xml->writeElement('ttl', $ttl);
        }
        if ($this->image !== null) {
            $this->image->setVersion($this->version);
            $this->image->apply($xml);
        }
        if ($this->rating !== null) {
            $xml->writeElement('rating', $this->rating);
        }
        if ($this->text_input !== null) {
            $this->text_input->setVersion($this->version);
            $this->text_input->apply($xml);
        }
        foreach ($this->skip_hours as &$skip_hour) {
            $xml->writeElement('skipHours', $skip_hour);
        }
        foreach ($this->skip_days as &$skip_day) {
            $xml->writeElement('skipDays', $skip_day);
        }
        foreach ($this->items as &$item) {
            $item->setVersion($this->version);
            $item->apply($xml);
        }

        $xml->endElement();
    }
}