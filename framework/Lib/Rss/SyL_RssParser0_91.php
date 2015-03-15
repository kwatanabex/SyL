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
 * RSS 0.91パーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssParser0_91 extends SyL_RssParserAbstract
{
    /**
     * RSSバージョン
     * 
     * @var string
     */
    protected $version = '0.91';
    /**
     * アイテム要素のインデックス
     * 
     * @var int
     */
    protected $item_index = 0;

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        switch ($current_path) {
        case '/rss/channel/copyright':      $this->channel->setCopyright($text); break;
        case '/rss/channel/description':    $this->channel->setDescription($text); break;
        case '/rss/channel/docs':           $this->channel->setDocs($text); break;
        case '/rss/channel/language':       $this->channel->setLanguage($text); break;
        case '/rss/channel/lastBuildDate':  $this->channel->setLastBuildDate($text ? new DateTime($text) : ''); break;
        case '/rss/channel/link':           $this->channel->setLink($text); break;
        case '/rss/channel/managingEditor': $this->channel->setManagingEditor($text); break;
        case '/rss/channel/pubDate':        $this->channel->setPubDate($text ? new DateTime($text) : ''); break;
        case '/rss/channel/rating':         $this->channel->setRating($text); break;
        case '/rss/channel/skipHours/hour':
            $skip_hours = $this->channel->getSkipHours();
            $skip_hours[] = $text;
            $this->channel->setSkipHours($skip_hours);
            break;
        case '/rss/channel/skipDays/day':
            $skip_days = $this->channel->getSkipDays();
            $skip_days[] = $text;
            $this->channel->setSkipDays($skip_days);
            break;
        case '/rss/channel/title':     $this->channel->setTitle($text); break;
        case '/rss/channel/webMaster': $this->channel->setWebMaster($text); break;

        // 画像
        case '/rss/channel/image':             $this->channel->setImage(new SyL_RssElementImage()); break;
        case '/rss/channel/image/url':         $this->channel->getImage()->setUrl($text); break;
        case '/rss/channel/image/title':       $this->channel->getImage()->setTitle($text); break;
        case '/rss/channel/image/link':        $this->channel->getImage()->setLink($text); break;
        case '/rss/channel/image/width':       $this->channel->getImage()->setWidth($text); break;
        case '/rss/channel/image/height':      $this->channel->getImage()->setHeight($text); break;
        case '/rss/channel/image/description': $this->channel->getImage()->setDescription($text); break;

        // 入力要素
        case '/rss/channel/textInput':             $this->channel->setTextInput(new SyL_RssTextInput()); break;
        case '/rss/channel/textInput/title':       $this->channel->getTextInput()->setTitle($text); break;
        case '/rss/channel/textInput/name':        $this->channel->getTextInput()->setName($text); break;
        case '/rss/channel/textInput/description': $this->channel->getTextInput()->setDescription($text); break;
        case '/rss/channel/textInput/link':        $this->channel->getTextInput()->setLink($text); break;

        // RSS情報
        case '/rss/channel/item':
            $items = $this->channel->getItems();
            $this->item_index = count($items);
            $items[$this->item_index] = new SyL_RssElementItem();
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/title':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setTitle($text);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/link':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setLink($text);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/description':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setDescription($text);
            $this->channel->setItems($items);
            break;
        }
    }
}
