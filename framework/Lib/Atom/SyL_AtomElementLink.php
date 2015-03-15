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

/**
 * AtomPubフィードリンク要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementLink extends SyL_AtomElementAbstract
{
    /**
     * リンクのIRI
     *
     * @var string
     */
    private $href = null;
    /**
     * リンク関連タイプ
     *
     * @var string
     */
    private $rel = null;
    /**
     * メディアタイプ
     *
     * @var string
     */
    private $type = null;
    /**
     * リソースの言語
     *
     * @var string
     */
    private $hreflang = null;
    /**
     * タイトル
     *
     * @var string
     */
    private $title = null;
    /**
     * リンクされたコンテンツのサイズ
     *
     * @var string
     */
    private $length = null;

    /**
     * リンクのIRIを取得する
     *
     * @return string リンクのIRI
     */
    public function getHref()
    {
        return $this->href;
    }
    /**
     * リンクのIRIをセットする
     *
     * @param string リンクのIRI
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * リンク関連タイプを取得する
     *
     * @return string リンク関連タイプ
     */
    public function getRel()
    {
        return $this->rel;
    }
    /**
     * リンク関連タイプをセットする
     *
     * @param string リンク関連タイプ
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
    }

    /**
     * メディアタイプを取得する
     *
     * @return string メディアタイプ
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * メディアタイプをセットする
     *
     * @param string メディアタイプ
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * リソースの言語を取得する
     *
     * @return string リソースの言語
     */
    public function getHreflang()
    {
        return $this->hreflang;
    }
    /**
     * リソースの言語をセットする
     *
     * @param string リソースの言語
     */
    public function setHreflang($hreflang)
    {
        $this->hreflang = $hreflang;
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
     * リンクされたコンテンツのサイズを取得する
     *
     * @return string リンクされたコンテンツのサイズ
     */
    public function getLength()
    {
        return $this->length;
    }
    /**
     * リンクされたコンテンツのサイズをセットする
     *
     * @param string リンクされたコンテンツのサイズ
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('link');
        $xml->writeAttribute('href', $this->href);
        if ($this->rel !== null) {
            $xml->writeAttribute('rel', $this->rel);
        }
        if ($this->type !== null) {
            $xml->writeAttribute('type', $this->type);
        }
        if ($this->hreflang !== null) {
            $xml->writeAttribute('hreflang', $this->hreflang);
        }
        if ($this->title !== null) {
            $xml->writeAttribute('title', $this->title);
        }
        if ($this->length !== null) {
            $xml->writeAttribute('length', $this->length);
        }

        $xml->endElement();
    }
}
