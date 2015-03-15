<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - Web Application Framework for PHP
 *
 * PHP version 4 (>= 4.3.x) or 5
 *
 * Copyright (C) 2006-2009 k.watanabe
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

/** RSS 0.91パーサークラス */
require_once 'SyL_RssParser0_91.php';

/**
 * RSS 2.0パーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssParser2_0 extends SyL_RssParser0_91
{
    /**
     * RSSバージョン
     * 
     * @var string
     */
    protected $version = '2.0';

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        // XML解析結果を取得
        switch ($current_path) {
        case '/rss/channel/category':
            $categories = $this->channel->getCategories();
            $index = count($categories);
            $categories[$index] = new SyL_RssElementCategory();
            $categories[$index]->setCategory($text);
            if (isset($attributes['domain'])) {
                $categories[$index]->setDomain($attributes['domain']);
            }
            $this->channel->setCategories($categories);
            break;
        case '/rss/channel/generator': $this->channel->setGenerator($text); break;
        case '/rss/channel/cloud':
            $cloud = new SyL_RssElementCloud();
            if (isset($attributes['domain'])) $cloud->setDomain($attributes['domain']);
            if (isset($attributes['port']))   $cloud->setPort($attributes['port']);
            if (isset($attributes['path']))   $cloud->setPath($attributes['path']);
            if (isset($attributes['registerProcedure'])) $cloud->setRegisterProcedure($attributes['registerProcedure']);
            if (isset($attributes['protocol'])) $cloud->setProtocol($attributes['protocol']);
            $this->channel->setCloud($cloud);
            break;
        case '/rss/channel/ttl':
            $ttls = $this->channel->getTtls();
            $index = count($ttls);
            $ttls[$index] = $text;
            $this->channel->setTtls($ttls);
            break;

        // RSS情報
        case '/rss/channel/item/author':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setAuthor($text);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/category':
            $items = $this->channel->getItems();
            $categories = $items[$this->item_index]->getCategories();
            $index = count($categories);
            $categories[$index] = new SyL_RssElementCategory();
            $categories[$index]->setCategory($text);
            if (isset($attributes['domain'])) {
                $categories[$index]->setDomain($attributes['domain']);
            }
            $items[$this->item_index]->setCategories($categories);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/comments':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setComments($text);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/enclosure':
            $enclosure = new SyL_RssElementEnclosure();
            if (isset($attributes['url']))    $enclosure->setUrl($attributes['url']);
            if (isset($attributes['length'])) $enclosure->setLength($attributes['length']);
            if (isset($attributes['type']))   $enclosure->setType($attributes['type']);
            $items = $this->channel->getItems();
            $items[$this->item_index]->setEnclosure($enclosure);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/guid':
            $guid = new SyL_RssElementGuid();
            $guid->setGuid($text);
            if (isset($attributes['isPermaLink'])) $guid->setIsPermaLink($attributes['isPermaLink']);
            $items = $this->channel->getItems();
            $items[$this->item_index]->setGuid($guid);
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/pubDate':
            $items = $this->channel->getItems();
            $items[$this->item_index]->setPubDate($text ? new DateTime($text) : '');
            $this->channel->setItems($items);
            break;
        case '/rss/channel/item/source':
            $source = new SyL_RssElementSource();
            $source->setSource($text);
            if (isset($attributes['url'])) $source->setUrl($attributes['url']);
            $items = $this->channel->getItems();
            $items[$this->item_index]->setSource($source);
            $this->channel->setItems($items);
            break;

        default:
            // その他の要素は、ver.0.91を参照
            parent::doElement($current_path, $attributes, $text);
            break;
        }
    }
}
