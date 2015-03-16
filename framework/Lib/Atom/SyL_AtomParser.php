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
 * Atom パーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomParser extends SyL_AtomParserAbstract
{
    /**
     * 著者要素のインデックス
     * 
     * @var int
     */
    protected $author_index = 0;
    /**
     * 貢献人要素のインデックス
     * 
     * @var int
     */
    protected $contributor_index = 0;
    /**
     * エントリ要素のインデックス
     * 
     * @var int
     */
    protected $entry_index = 0;
    
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
        case '/feed/icon':       $this->feed->setIcon($text); break;
        case '/feed/id':         $this->feed->setId($text); break;
        case '/feed/title':      $this->feed->setTitle($text); break;
        case '/feed/subtitle':   $this->feed->setSubtitle($text); break;
        case '/feed/updated':    $this->feed->setUpdated($text ? new DateTime($text) : ''); break;
        case '/feed/author':
            $authors = $this->feed->getAuthors();
            $this->author_index = count($authors);
            $authors[$this->author_index] = new SyL_AtomElementAuthor();
            $this->feed->setAuthors($authors);
            break;
        case '/feed/author/name':
            $authors = $this->feed->getAuthors();
            $authors[$this->author_index]->setTitle($text);
            $this->feed->setAuthors($authors);
            break;
        case '/feed/author/uri':
            $authors = $this->feed->getAuthors();
            $authors[$this->author_index]->setUri($text);
            $this->feed->setAuthors($authors);
            break;
        case '/feed/author/email':
            $authors = $this->feed->getAuthors();
            $authors[$this->author_index]->setEmail($text);
            $this->feed->setAuthors($authors);
            break;

        case '/feed/generator':  
            $generator = new SyL_AtomElementGenerator();
            if (isset($attributes['uri'])) {
                $generator->setUri($attributes['uri']);
            }
            if (isset($attributes['version'])) {
                $generator->setVersion($attributes['version']);
            }
            $generator->setText($text);
            $this->feed->setGenerator($generator);
            break;
        case '/feed/category':
            $category = new SyL_AtomElementCategory();
            if (isset($attributes['term'])) {
                $category->setTerm($attributes['term']);
            }
            if (isset($attributes['scheme'])) {
                $category->setScheme($attributes['scheme']);
            }
            if (isset($attributes['label'])) {
                $category->setLabel($attributes['label']);
            }
            $categories = $this->feed->getCategories();
            $categories[] = category;
            $this->feed->setCategories($categories);
            break;
            
        case '/feed/contributor':
            $contributors = $this->feed->getContributors();
            $this->contributor_index = count($contributors);
            $contributors[$this->contributor_index] = new SyL_AtomElementContributor();
            $this->feed->setContributors($contributors);
            break;
        case '/feed/contributor/name':
            $contributors = $this->feed->getContributors();
            $contributors[$this->contributor_index]->setTitle($text);
            $this->feed->setContributors($authors);
            break;
        case '/feed/contributor/uri':
            $contributors = $this->feed->getContributors();
            $contributors[$this->contributor_index]->setUri($text);
            $this->feed->setContributors($authors);
            break;
        case '/feed/contributor/email':
            $contributors = $this->feed->getContributors();
            $contributors[$this->contributor_index]->setEmail($text);
            $this->feed->setContributors($authors);
            break;
        case '/feed/link':
            $link = new SyL_AtomElementLink();
            if (isset($attributes['href'])) {
                $link->setHref($attributes['href']);
            }
            if (isset($attributes['rel'])) {
                $link->setRel($attributes['rel']);
            }
            if (isset($attributes['type'])) {
                $link->setType($attributes['type']);
            }
            if (isset($attributes['hreflang'])) {
                $link->setHreflang($attributes['hreflang']);
            }
            if (isset($attributes['title'])) {
                $link->setTitle($attributes['title']);
            }
            if (isset($attributes['length'])) {
                $link->setLength($attributes['length']);
            }
            $links = $this->feed->getLinks();
            $links[] = link;
            $this->feed->setLinks($links);
            break;
        case '/feed/logo': $this->feed->setLogo($text); break;
        case '/feed/rights':
            $rights = $this->feed->getRights();
            $rights[] = text;
            $this->feed->setRights($rights);
            break;

        case '/feed/entry':
            $entries = $this->feed->getEntries();
            $this->entry_index = count($entries);
            $entries[$this->entry_index] = new SyL_AtomElementEntry();
            $this->feed->setEntries($entries);
            break;




        case '/feed/image/url':         $this->feed->getImage()->setUrl($text); break;
        case '/feed/image/title':       $this->feed->getImage()->setTitle($text); break;
        case '/feed/image/link':        $this->feed->getImage()->setLink($text); break;
        case '/feed/image/width':       $this->feed->getImage()->setWidth($text); break;
        case '/feed/image/height':      $this->feed->getImage()->setHeight($text); break;
        case '/feed/image/description': $this->feed->getImage()->setDescription($text); break;

        // 入力要素
        case '/feed/textInput':             $this->feed->setTextInput(new SyL_RssTextInput()); break;
        case '/feed/textInput/title':       $this->feed->getTextInput()->setTitle($text); break;
        case '/feed/textInput/name':        $this->feed->getTextInput()->setName($text); break;
        case '/feed/textInput/description': $this->feed->getTextInput()->setDescription($text); break;
        case '/feed/textInput/link':        $this->feed->getTextInput()->setLink($text); break;

        // RSS情報
        case '/feed/item':
            $items = $this->feed->getItems();
            $this->item_index = count($items);
            $items[$this->item_index] = new SyL_RssElementItem();
            $this->feed->setItems($items);
            break;
        case '/feed/item/title':
            $items = $this->feed->getItems();
            $items[$this->item_index]->setTitle($text);
            $this->feed->setItems($items);
            break;
        case '/feed/item/link':
            $items = $this->feed->getItems();
            $items[$this->item_index]->setLink($text);
            $this->feed->setItems($items);
            break;
        case '/feed/item/description':
            $items = $this->feed->getItems();
            $items[$this->item_index]->setDescription($text);
            $this->feed->setItems($items);
            break;
        }
    }
}
