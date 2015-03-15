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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** AtomPubルート要素インターフェイス */
require_once 'SyL_AtomElementRootInterface.php';
/** AtomPub要素クラス */
require_once 'SyL_AtomElementAbstract.php';
/** AtomPubコンテンツ要素クラス */
require_once 'SyL_AtomElementContent.php';
/** AtomPub貢献人要素クラス */
require_once 'SyL_AtomElementContributor.php';

/**
 * AtomPubエントリ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementEntry extends SyL_AtomElementAbstract implements SyL_AtomElementRootInterface
{
    /**
     * ルート要素フラグ
     *
     * @var bool
     */
    private $root = false;
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
     * コンテンツ
     *
     * @var SyL_AtomElementContent
     */
    private $content = null;
    /**
     * 貢献人
     *
     * @var array
     */
    private $contributors = array();
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
     * 投稿日時
     *
     * @var DateTime
     */
    private $published = null;
    /**
     * 権利に関する情報
     *
     * @var string
     */
    private $rights = null;
    /**
     * 参照元メタデータ
     *
     * @var SyL_AtomElementFeed
     */
    private $source = null;
    /**
     * 要約
     *
     * @var string
     */
    private $summary = null;
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
     * コンストラクタ
     *
     * @param bool ルート要素判定
     */
    public function __construct($root=true)
    {
        $this->root = $root;
    }

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
     * コンテンツを取得する
     *
     * @return SyL_AtomElementContent コンテンツ
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * コンテンツをセットする
     *
     * @param SyL_AtomElementContent コンテンツ
     */
    public function setContent(SyL_AtomElementContent $content)
    {
        $this->content = $content;
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
     * 投稿日時を取得する
     *
     * @return DateTime 投稿日時
     */
    public function getPublished()
    {
        return $this->published;
    }
    /**
     * 投稿日時をセットする
     *
     * @param DateTime 投稿日時
     */
    public function setPublished(DateTime $published)
    {
        $this->published = $published;
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
     * 参照元メタデータを取得する
     *
     * @return SyL_AtomElementFeed 参照元メタデータ
     */
    public function getSource()
    {
        return $this->source;
    }
    /**
     * 参照元メタデータをセットする
     *
     * @param SyL_AtomElementFeed 参照元メタデータ
     */
    public function setSource(SyL_AtomElementFeed $source)
    {
        throw new SyL_NotImplementedException('source element not implemented');
        $this->source = $source;
    }

    /**
     * 要約を取得する
     *
     * @return string 要約
     */
    public function getSummary()
    {
        return $this->summary;
    }
    /**
     * 要約をセットする
     *
     * @param string 要約
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
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
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        if ($this->enable_enclosure) {
            $xml->startElement('entry');
            if ($this->root) {
                $xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
                foreach (self::$namespaces as $name => $uri) {
                    $xml->writeAttributeNS('xmlns', $name, null, $uri);
                }
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

        if ($this->content !== null) {
            $this->content->apply($xml);
        }

        foreach ($this->contributors as &$contributor) {
            $contributor->apply($xml);
        }

        foreach ($this->links as &$link) {
            $link->apply($xml);
        }

        if ($this->published !== null) {
            $xml->writeElement('published', $this->published->format(Datetime::ATOM));
        }

        if ($this->rights !== null) {
            $xml->writeElement('rights', $this->rights);
        }

        if ($this->source !== null) {
            // not implemented
        }

        if ($this->summary !== null) {
            $xml->writeElement('summary', $this->summary);
        }

        if ($this->enable_enclosure) {
            $xml->endElement();
        }
    }
}
