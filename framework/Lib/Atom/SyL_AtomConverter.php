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

/** RSSパーサークラス */
//require_once 'SyL_RssParserAbstract.php';

/**
 * AtomPub要素オブジェクト変換クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomConverter
{
    /**
     * AtomPub要素オブジェクトを XML に変換する
     * 
     * @param SyL_AtomElementAbstract AtomPub要素オブジェクト
     * @param string エンコーディング
     * @return string XML文字列
     */
    public static function toXml(SyL_AtomElementAbstract $atom, $encoding=null)
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        self::apply($atom, $xml, $encoding);
        return $xml->outputMemory(true);
    }

    /**
     * AtomPub要素オブジェクトを XMLWriter オブジェクトに適用する
     * 
     * @param SyL_AtomElementAbstract AtomPub要素オブジェクト
     * @param XMLWriter XMLWriterオブジェクト
     * @param string エンコーディング
     */
    public static function apply(SyL_AtomElementAbstract $atom, XMLWriter $xml, $encoding=null)
    {
        if (($atom instanceof SyL_AtomElementService) ||
            ($atom instanceof SyL_AtomElementFeed)    ||
            ($atom instanceof SyL_AtomElementEntry)) {
            // OK
        } else {
            throw new SyL_InvalidClassException('not supported class (' . get_class($atom) . ')');
        }

        if (!$encoding) {
            $encoding = $atom->getEncoding();
        }

        $xml->startDocument('1.0', $encoding);
        $atom->apply($xml);
        $xml->endDocument();
    }

    /**
     * XML を RSS要素オブジェクトに変換する
     * 
     * @param string XML
     * @param string RSSバージョン
     * @param string エンコーディング
     * @return SyL_RssElementRss RSS要素オブジェクト
     */
    public static function toObject($data, $rss_version='2.0', $encoding=null)
    {
        $parser = SyL_RssParserAbstract::createInstance($data, $rss_version);
        if ($encoding) {
            $parser->setOutputEncoding($encoding);
        }
        $parser->parse();
        return $parser->getRss();
    }
}
