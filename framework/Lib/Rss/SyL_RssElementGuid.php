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

/**
 * RSS一意に特定要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementGuid extends SyL_RssElementAbstract
{
    /**
     * 一意に識別する文字列
     *
     * @var string
     */
     private $guid = null;
    /**
     * 永久リンク
     *
     * @var bool
     */
     private $is_perma_link = null;

    /**
     * 一意に識別する文字列を取得する
     *
     * @return string 一意に識別する文字列
     */
    public function getGuid()
    {
        return $this->guid;
    }
    /**
     * 一意に識別する文字列を取得する（dc:identifier用エイリアス）
     *
     * @return string 一意に識別する文字列
     */
    public function getIdentifier()
    {
        return $this->getGuid();
    }
    /**
     * 一意に識別する文字列をセットする
     *
     * @param string 一意に識別する文字列
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * 永久リンクを取得する
     *
     * @return bool 永久リンク
     */
    public function getIsPermaLink()
    {
        return $this->is_perma_link;
    }
    /**
     * 永久リンクをセットする
     *
     * @param bool 永久リンク
     */
    public function setIsPermaLink($is_perma_link)
    {
        $this->is_perma_link = $is_perma_link;
    }

    /**
     * XMLWriterオブジェクトにRSS1.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply1_0(XMLWriter $xml)
    {
        $xml->writeElement('dc:identifier', $this->guid);
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('guid');
        if ($this->is_perma_link !== null) {
            if ($this->is_perma_link) {
                $xml->writeAttribute('isPermaLink', 'true');
            } else {
                $xml->writeAttribute('isPermaLink', 'false');
            }
        }
        $xml->text($this->guid);
        $xml->endElement();
    }
}

