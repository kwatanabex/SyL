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
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** AtomPubルート要素インターフェイス */
require_once 'SyL_AtomElementRootInterface.php';
/** AtomPub要素クラス */
require_once 'SyL_AtomElementAbstract.php';
/** AtomPub著者要素クラス */
require_once 'SyL_AtomElementAuthor.php';
/** AtomPubカテゴリ要素クラス */
require_once 'SyL_AtomElementCategory.php';
/** AtomPub貢献人要素クラス */
require_once 'SyL_AtomElementContributor.php';
/** AtomPubフィード生成媒体要素クラス */
require_once 'SyL_AtomElementGenerator.php';
/** AtomPubフィードリンク要素クラス */
require_once 'SyL_AtomElementLink.php';
/** AtomPubエントリ要素クラス */
require_once 'SyL_AtomElementEntry.php';

/**
 * AtomPubフィード要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementFeed extends SyL_AtomElementAbstract implements SyL_AtomElementRootInterface
{
    /**
     * AtomPubのサービス文書のエンコーディング
     *
     * @var string
     */
    private $encoding = 'UTF-8';
    /**
     * 著者
     *
     * @var array
     */
    private $authors = array();
    /**
     * カテゴリ
     *
     * @var array
     */
    private $categories = array();
    /**
     * 貢献人
     *
     * @var array
     */
    private $contributors = array();
    /**
     * フィード生成媒体
     *
     * @var SyL_AtomElementGenerator
     */
    private $generator = null;
    /**
     * アイコン
     *
     * @var string
     */
    private $icon = null;
    /**
     * ID
     *
     * @var string
     */
    private $id = null;
    /**
     * リンク
     *
     * @var array
     */
    private $links = array();
    /**
     * ロゴ
     *
     * @var string
     */
    private $logo = null;
    /**
     * 権利に関する情報
     *
     * @var string
     */
    private $rights = null;
    /**
     * サブタイトル
     *
     * @var string
     */
    private $subtitle = null;
    /**
     * タイトル
     *
     * @var string
     */
    private $title = null;
    /**
     * 直近の時間
     *
     * @var DateTime
     */
    private $updated = null;
    /**
     * エントリ
     *
     * @var array
     */
    private $entries = array();

    /**
     * RSSエンコーディングを取得する
     *
     * @return string RSSエンコーディング
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
    /**
     * RSSエンコーディングをセットする
     *
     * @param string RSSエンコーディング
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * 著者を取得する
     *
     * @return array 著者
     */
    public function getAuthors()
    {
        return $this->authors;
    }
    /**
     * 著者をセットする
     *
     * @param array 著者
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
    }
    /**
     * 著者を追加する
     *
     * @param SyL_AtomElementAuthor 著者
     */
    public function addAuthor(SyL_AtomElementAuthor $author)
    {
        $this->authors[] = $author;
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
     * @param bool カテゴリ固定化
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
    }
    /**
     * カテゴリを追加する
     *
     * @param SyL_AtomElementCategory カテゴリ
     */
    public function addCategory(SyL_AtomElementCategory $category)
    {
        $this->categories[] = $category;
    }

    /**
     * 貢献人を取得する
     *
     * @return array 貢献人
     */
    public function getContributors()
    {
        return $this->contributors;
    }
    /**
     * 貢献人をセットする
     *
     * @param array 貢献人
     */
    public function setContributors(array $contributors)
    {
        $this->contributors = $contributors;
    }
    /**
     * 貢献人を追加する
     *
     * @param SyL_AtomElementContributor 貢献人
     */
    public function addContributor(SyL_AtomElementContributor $contributor)
    {
        $this->contributors[] = $contributor;
    }

    /**
     * フィード生成媒体を取得する
     *
     * @return SyL_AtomElementGenerator フィード生成媒体
     */
    public function getGenerator()
    {
        return $this->generator;
    }
    /**
     * フィード生成媒体をセットする
     *
     * @param SyL_AtomElementGenerator フィード生成媒体
     */
    public function setGenerator(SyL_AtomElementGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * アイコンを取得する
     *
     * @return string アイコン
     */
    public function getIcon()
    {
        return $this->icon;
    }
    /**
     * アイコンをセットする
     *
     * @param string アイコン
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * IDを取得する
     *
     * @return string ID
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * IDをセットする
     *
     * @param string ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * リンクを取得する
     *
     * @return array リンク
     */
    public function getLinks()
    {
        return $this->links;
    }
    /**
     * リンクをセットする
     *
     * @param array リンク
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }
    /**
     * エリンクを追加する
     *
     * @param SyL_AtomElementLink リンク
     */
    public function addLink(SyL_AtomElementLink $link)
    {
        $this->links[] = $link;
    }

    /**
     * ロゴを取得する
     *
     * @return string ロゴ
     */
    public function getLogo()
    {
        return $this->logo;
    }
    /**
     * ロゴをセットする
     *
     * @param string ロゴ
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * 権利に関する情報を取得する
     *
     * @return string 権利に関する情報
     */
    public function getRights()
    {
        return $this->rights;
    }
    /**
     * 権利に関する情報をセットする
     *
     * @param string 権利に関する情報
     */
    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    /**
     * サブタイトルを取得する
     *
     * @return string サブタイトル
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }
    /**
     * サブタイトルをセットする
     *
     * @param string サブタイトル
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
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
     * 直近の時間を取得する
     *
     * @return DateTime 直近の時間
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * 直近の時間をセットする
     *
     * @param DateTime 直近の時間
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
    }

    /**
     * エントリを取得する
     *
     * @return array エントリ
     */
    public function getEntries()
    {
        return $this->entries;
    }
    /**
     * エントリをセットする
     *
     * @param array エントリ
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
    }
    /**
     * エントリをセットする
     *
     * @param SyL_AtomElementEntry エントリ
     */
    public function addEntry(SyL_AtomElementEntry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        if ($this->enable_enclosure) {
            $xml->startElement('feed');
            $xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
            foreach (self::$namespaces as $name => $uri) {
                $xml->writeAttributeNS('xmlns', $name, null, $uri);
            }
        }

        $xml->writeElement('id', $this->id);
        $xml->writeElement('title', $this->title);
        $xml->writeElement('updated', $this->updated->format(Datetime::ATOM));

        foreach ($this->authors as &$author) {
            $author->apply($xml);
        }

        foreach ($this->categories as &$category) {
            $category->apply($xml);
        }

        foreach ($this->contributors as &$contributor) {
            $contributor->apply($xml);
        }

        if ($this->generator !== null) {
            $this->generator->apply($xml);
        }

        if ($this->icon !== null) {
            $xml->writeElement('icon', $this->icon);
        }

        foreach ($this->links as &$link) {
            $link->apply($xml);
        }

        if ($this->logo !== null) {
            $xml->writeElement('logo', $this->logo);
        }

        if ($this->rights !== null) {
            $xml->writeElement('rights', $this->rights);
        }

        if ($this->subtitle !== null) {
            $xml->writeElement('subtitle', $this->subtitle);
        }

        foreach ($this->entries as &$entry) {
            $entry->apply($xml);
        }

        if ($this->enable_enclosure) {
            $xml->endElement();
        }
    }
}
