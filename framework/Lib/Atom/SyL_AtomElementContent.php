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

/**
 * AtomPubコンテンツ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementContent extends SyL_AtomElementAbstract
{
    /**
     * メディアタイプ
     *
     * @var string
     */
    private $type = null;
    /**
     * リソース先
     *
     * @var string
     */
    private $src = null;
    /**
     * コンテンツ
     *
     * @var string
     */
    private $content = null;

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
     * リソース先を取得する
     *
     * @return string リソース先
     */
    public function getSrc()
    {
        return $this->src;
    }
    /**
     * リソース先をセットする
     *
     * @param string リソース先
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * リソース先を取得する
     *
     * @return string リソース先
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * リソース先をセットする
     *
     * @param string リソース先
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('content');

        if ($this->type !== null) {
            $xml->writeAttribute('type', $this->type);
        }

        if ($this->src !== null) {
            $xml->writeAttribute('src', $this->src);
        } else {
            $xml->text($this->content);
        }

        $xml->endElement();
    }
}