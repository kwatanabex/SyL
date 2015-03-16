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

/** AtomPubコレクション要素クラス */
require_once 'SyL_AtomElementCollection.php';

/**
 * AtomPubワークスペース要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementWorkspace extends SyL_AtomElementAbstract
{
    /**
     * タイトル
     *
     * @var string
     */
    private $title = null;
    /**
     * コレクション配列
     *
     * @var array
     */
    private $collections = array();

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
     * コレクション配列を取得する
     *
     * @return array コレクション配列
     */
    public function getCollections()
    {
        return $this->collections;
    }
    /**
     * コレクション配列をセットする
     *
     * @param array コレクション配列
     */
    public function setCollections(array $collections)
    {
        $this->collections = $collections;
    }
    /**
     * コレクションをセットする
     *
     * @param SyL_AtomElementCollection コレクション
     */
    public function addCollection(SyL_AtomElementCollection $collection)
    {
        $this->collections[] = $collection;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('workspace');

        if ($this->title !== null) {
            $xml->writeElement('atom:title', $this->title);
        }

        foreach ($this->collections as &$collection) {
            $collection->apply($xml);
        }

        $xml->endElement();
    }
}