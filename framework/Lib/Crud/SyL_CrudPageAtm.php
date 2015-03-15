<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
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
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** AtomPubサービス要素クラス */
require_once dirname(__FILE__) . '/../Atom/SyL_AtomElementService.php';
/** AtomPubフィード要素クラス */
require_once dirname(__FILE__) . '/../Atom/SyL_AtomElementFeed.php';
/** CRUD用 AtomPubエントリ要素クラス */
require_once dirname(__FILE__) . '/SyL_CrudAtomElementEntry.php';

/**
 * CRUD AtomPubクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudPageAtm extends SyL_CrudPageAbstract
{
    /**
     * Atomエントリでサポートされるエントリ要素
     *
     * @var array
     */
    private static $item_support_elements = array('title', 'link', 'summary', 'author', 'published', 'updated');

    /**
     * AtomPubのサービス文書を取得する
     *
     * @param array カテゴリリスト
     * @param array メディアタイプリスト
     * @return SyL_AtomElementService AtomPubのサービス文書
     */
    public function getAtomService(array $accepts=array('application/atom+xml'))
    {
        $workspace = new SyL_AtomElementWorkspace();
        $workspace->title = $this->getName();

        $collection = new SyL_AtomElementCollection();
        $collection->href = $this->config->getUrlAtmFeed();
        $collection->title = $this->getName();
        $collection->accepts = $accepts;

        $workspace->addCollection($collection);

        $service = new SyL_AtomElementService();
        $service->addWorkspace($workspace);

        return $service;
    }

    /**
     * Atomフィードを取得する
     *
     * @param array カテゴリリスト
     * @param array メディアタイプリスト
     * @return SyL_AtomElementFeed Atomフィード
     */
    public function getAtomFeed($page_count=1, $sorts=array(), $row_count=0)
    {
        // ソート
        if (count($sorts) == 0) {
            $sorts = $this->config->getAtomDefaultSort();
        }
        // 件数
        if ($row_count == 0) {
            $row_count = $this->config->getAtomRowCount();
        }
        // フォーマット設定
        if (!$this->config->getAtomItemFormat()) {
            throw new SyL_CrudNotFoundException('atom config item_format not setting');
        }

        list($headers, $dbrows, $pager) = $this->config->getAccess()->getList($page_count, $sorts, array(), $row_count, false);

        $feed = new SyL_AtomElementFeed();
        $feed->title = $this->getName();
        $feed->id = $this->config->getUrlLst();
        $feed->updated = new DateTime();

        $link = new SyL_AtomElementLink();
        $link->href = $this->config->getUrlLst();
        $link->rel = 'alternate';
        $link->type = 'text/html';
        $feed->addLink($link);

        $feed_uri = $this->config->getUrlAtmFeed();
        $current_page = $pager->getCurrentPage();
        $total_page = $pager->getTotalPage();

        $link = new SyL_AtomElementLink();
        $link->href = $feed_uri . '?__page=' . $current_page;
        $link->rel  = 'self';
        $link->type = 'application/atom+xml';
        $feed->addLink($link);

        $link = new SyL_AtomElementLink();
        $link->href = $feed_uri . '?__page=1';
        $link->rel  = 'first';
        $link->type = 'application/atom+xml';
        $feed->addLink($link);

        if ($current_page > 1) {
            $link = new SyL_AtomElementLink();
            $link->href = $feed_uri . '?__page=' . ($current_page - 1);
            $link->rel  = 'previous';
            $link->type = 'application/atom+xml';
            $feed->addLink($link);
        }

        if ($total_page > $current_page) {
            $link = new SyL_AtomElementLink();
            $link->href = $feed_uri . '?__page=' . ($current_page + 1);
            $link->rel  = 'next';
            $link->type = 'application/atom+xml';
            $feed->addLink($link);
        }

        $link = new SyL_AtomElementLink();
        $link->href = $feed_uri . '?__page=' . $total_page;
        $link->rel  = 'last';
        $link->type = 'application/atom+xml';
        $feed->addLink($link);

        $feed->subtitle = $this->getDescription();

        $generator = new SyL_AtomElementGenerator();
        $generator->uri = 'http://syl.jp/';
        $generator->text = 'SyL Framework CRUD Library';
        $feed->generator = $generator;

        foreach ($dbrows as &$record) {
            $primary = array();
            foreach ($record as $name => $value) {
                if ($headers[$name]['primary']) {
                    $primary[$name] = $value;
                }
            }
            $id = self::encodeId($primary);
            $feed->addEntry($this->createEntry($record, $id, false));
        }

        return $feed;
    }

    /**
     * Atomフィードに投稿する
     *
     * @return SyL_AtomElementFeed Atomフィード
     */
    public function postAtomFeed()
    {
        SyL_AtomElementAbstract::registerNamespace('crud', 'http://localhost/');
        
    }

    /**
     * Atomエントリを取得する
     *
     * @return SyL_AtomElementEntry Atomエントリ
     */
    public function getAtomEntry()
    {
        $record = $this->config->getAccess()->getRecord($this->getId());
        $id = self::encodeId($this->getId());
        return $this->createEntry($record, $id, true);
    }

    /**
     * Atomエントリを作成する
     *
     * @param SyL_DbRecord DBレコード
     * @param array 主キー
     * @param bool ルート要素フラグ
     * @return SyL_AtomElementEntry Atomエントリ
     */
    private function createEntry(SyL_DbRecord $record, $id, $root)
    {
        $item_format = $this->config->getAtomItemFormat();

        $entry = new SyL_AtomElementEntry($root);
        foreach (self::$item_support_elements as $name) {
            if (!empty($item_format[$name])) {
                $value = self::replaceItem($record, $item_format[$name]);
                switch ($name) {
                case 'link':
                    $link = new SyL_AtomElementLink();
                    $link->href = $value;
                    $link->rel = 'alternate';
                    $value = $link;
                case 'author':
                    $method_name = 'add' . ucfirst($name);
                    $entry->{$method_name}($value);
                    break;
                case 'published':
                case 'updated':
                    $entry->{$name} = new DateTime($value);
                    break;
                default:
                    $entry->{$name} = $value;
                }
            }
        }

        $uri = $this->config->getUrlAtmEntry() . '?__id=' . $id;

        $entry->id = $uri;

        $link = new SyL_AtomElementLink();
        $link->href = $uri;
        $link->rel = 'edit';
        $link->type = 'application/atom+xml; type=entry';
        $entry->addLink($link);

        $link = new SyL_AtomElementLink();
        $link->href = $this->config->getUrlVew() . '?__id=' . $id;
        $link->rel = 'alternate';
        $link->type = 'text/html';
        $entry->addLink($link);

        return $entry;
    }

    /**
     * ITEM直下の各要素を内容を変換する
     *
     * @param SyL_DbRecord 結果セットレコードオブジェクト
     * @param string フォーマット
     * @return string フォーマット変換後文字列
     */
    private static function replaceItem(&$record, $format)
    {
        $value = '';
        if (preg_match_all('/\{\$(\w+)\}/', $format, $matches, PREG_SET_ORDER)) {
            $value = $format;
            foreach ($matches as $match) {
                if ($record->is($match[1])) {
                    $value = str_replace($match[0], $record->{$match[1]}, $value);
                }
            }
        } else {
            if ($record->is($format)) {
                $value = $record->{$format};
            }
        }
        return $value;
    }
}

