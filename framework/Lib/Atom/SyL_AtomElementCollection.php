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

/** AtomPubカテゴリ要素クラス */
require_once 'SyL_AtomElementCategory.php';

/**
 * AtomPubコレクション要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementCollection extends SyL_AtomElementAbstract
{
    /**
     * コレクションURI
     *
     * @var string
     */
    private $href = null;
    /**
     * タイトル
     *
     * @var string
     */
    private $title = null;
    /**
     * メディアタイプ
     *
     * @var array
     */
    private $accepts = array();
    /**
     * カテゴリ
     *
     * @var array
     */
    private $categories = array();
    /**
     * カテゴリが固定化
     *
     * @var bool
     */
    private $categories_fixed = true;

    /**
     * コレクションURIを取得する
     *
     * @return string コレクションURI
     */
    public function getHref()
    {
        return $this->href;
    }
    /**
     * コレクションURIをセットする
     *
     * @param string コレクションURI
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

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
     * メディアタイプを取得する
     *
     * @return array メディアタイプ
     */
    public function getAccepts()
    {
        return $this->accepts;
    }
    /**
     * メディアタイプをセットする
     *
     * @param array メディアタイプ
     */
    public function setAccepts(array $accepts)
    {
        $this->accepts = $accepts;
    }
    /**
     * メディアタイプをセットする
     *
     * @param string メディアタイプ
     */
    public function addAccept($accept)
    {
        $this->accepts[] = $accept;
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
    public function setCategories(array $categories, $fixed=true)
    {
        $this->categories = $categories;
        $this->categories_fixed = $fixed;
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
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('collection');

        if ($this->title !== null) {
            $xml->writeElement('atom:title', $this->title);
        }

        foreach ($this->accepts as $accept) {
            $xml->writeElement('accept', $accept);
        }

        if (count($this->categories) > 0) {
            $xml->startElement('categories');
            $xml->writeAttribute('fixed', ($this->categories_fixed ? 'yes' : 'no'));
            foreach ($this->categories as &$category) {
                $category->apply($xml);
            }
        }

        $xml->endElement();
    }
}