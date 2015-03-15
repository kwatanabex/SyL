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
 * RSS 1.0パーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssParser1_0 extends SyL_RssParserAbstract
{
    /**
     * RSSバージョン
     * 
     * @var string
     */
    protected $version = '1.0';
    /**
     * アイテム要素のインデックス
     * 
     * @var int
     */
    protected $item_index = 0;
    /**
     * rdfネームスペース
     * 
     * @var string
     */
    protected $rdf_ns = 'rdf';
    /**
     * dcネームスペース
     * 
     * @var string
     */
    protected $dc_ns = 'dc';

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
        case "/{$this->rdf_ns}:RDF/channel/description": $this->channel->setDescription($text); break;
        case "/{$this->rdf_ns}:RDF/channel/link":        $this->channel->setLink($text); break;
        case "/{$this->rdf_ns}:RDF/channel/title":       $this->channel->setTitle($text); break;
        case "/{$this->rdf_ns}:RDF/channel/{$this->dc_ns}:rights":      $this->channel->setCopyright($text); break;
        case "/{$this->rdf_ns}:RDF/channel/{$this->dc_ns}:language":    $this->channel->setLanguage($text); break;
        case "/{$this->rdf_ns}:RDF/channel/{$this->dc_ns}:date":        $this->channel->setPubDate($text ? new DateTime($text) : ''); break;
        case "/{$this->rdf_ns}:RDF/channel/{$this->dc_ns}:creator":     $this->channel->setWebMaster($text); break;

        // 画像
        case "/{$this->rdf_ns}:RDF/image":       $this->channel->setImage(new SyL_RssElementImage()); break;
        case "/{$this->rdf_ns}:RDF/image/url":   $this->channel->getImage()->setUrl($text); break;
        case "/{$this->rdf_ns}:RDF/image/title": $this->channel->getImage()->setTitle($text); break;
        case "/{$this->rdf_ns}:RDF/image/link":  $this->channel->getImage()->setLink($text); break;
        // 入力要素
        case "/{$this->rdf_ns}:RDF/textinput":             $this->channel->setTextInput(new SyL_RssTextInput()); break;
        case "/{$this->rdf_ns}:RDF/textinput/title":       $this->channel->getTextInput()->setTitle($text); break;
        case "/{$this->rdf_ns}:RDF/textinput/name":        $this->channel->getTextInput()->setName($text); break;
        case "/{$this->rdf_ns}:RDF/textinput/description": $this->channel->getTextInput()->setDescription($text); break;
        case "/{$this->rdf_ns}:RDF/textinput/link":        $this->channel->getTextInput()->setLink($text); break;

        // RSS情報
        case "/{$this->rdf_ns}:RDF/item":
            $items = $this->channel->getItems();
            $this->item_index = count($items);
            $items[$this->item_index] = new SyL_RssElementItem();
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/title":
            $items = $this->channel->getItems();
            $items[$this->item_index]->setTitle($text);
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/description":
            $items = $this->channel->getItems();
            $items[$this->item_index]->setDescription($text);
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/link":
            $items = $this->channel->getItems();
            $items[$this->item_index]->setLink($text);
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/{$this->dc_ns}:identifier":
            $guid = new SyL_RssElementGuid();
            $guid->setGuid($text);
            if (isset($attributes['isPermaLink'])) $guid->setIsPermaLink($attributes['isPermaLink']);
            $items = $this->channel->getItems();
            $items[$this->item_index]->setGuid($guid);
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/{$this->dc_ns}:date":
            $items = $this->channel->getItems();
            $items[$this->item_index]->setPubDate($text ? new DateTime($text) : '');
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/{$this->dc_ns}:source":
            $source = new SyL_RssElementSource();
            $source->setSource($text);
            if (isset($attributes['url'])) $source->setUrl($attributes['url']);
            $items = $this->channel->getItems();
            $items[$this->item_index]->setSource($source);
            $this->channel->setItems($items);
            break;
        case "/{$this->rdf_ns}:RDF/item/{$this->dc_ns}:rights":
            $items = $this->channel->getItems();
            $items[$this->item_index]->setAuthor($text);
            $this->channel->setItems($items);
            break;
        }

    }
}
