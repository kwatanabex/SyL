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

/** RSS要素クラス */
require_once 'SyL_RssElementAbstract.php';
/** RSSチャネル要素クラス */
require_once 'SyL_RssElementChannel.php';

/**
 * RSS RSS要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementRss extends SyL_RssElementAbstract
{
    /**
     * エンコーディング
     *
     * @var string
     */
    private $encoding = 'UTF-8';
    /**
     * チャネルオブジェクト
     *
     * @var SyL_RssElementChannel
     */
    private $channel = null;

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
     * チャンネルオブジェクトを取得する
     *
     * @return SyL_RssElementChannel チャンネルオブジェクト
     */
    public function getChannel()
    {
        return $this->channel;
    }
    /**
     * チャンネルオブジェクトをセットする
     *
     * @param SyL_RssElementChannel チャンネルオブジェクト
     */
    public function setChannel(SyL_RssElementChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * XMLWriterオブジェクトにRSS0.91要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply0_91(XMLWriter $xml)
    {
        $xml->startElement('rss');
        $xml->writeAttribute('version', $this->version);

        if ($this->channel !== null) {
            $this->channel->setVersion($this->version);
            $this->channel->apply($xml);
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
        $xml->startElement('rdf:RDF');
        $xml->writeAttribute('xmlns', 'http://purl.org/rss/1.0/');
        $xml->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xml->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

        if ($this->channel !== null) {
            $this->channel->setVersion($this->version);
            $this->channel->apply($xml);
        }

        $image = $this->channel->getImage();
        if ($image) {
            $image->setVersion($this->version);
            $image->apply($xml);
        }

        $text_input = $this->channel->getTextInput();
        if ($text_input) {
            $text_input->setVersion($this->version);
            $text_input->apply($xml);
        }

        foreach ($this->channel->getItems() as $item) {
            $item->setVersion($this->version);
            $item->apply($xml);
        }

        $xml->endElement();
    }

    /**
     * XMLWriterオブジェクトにRSS2.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $this->apply0_91($xml);
    }
}
