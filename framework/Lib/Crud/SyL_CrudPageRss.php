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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** RSS RSS要素クラス */
require_once dirname(__FILE__) . '/../Rss/SyL_RssElementRss.php';

/**
 * CRUD RSSクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudPageRss extends SyL_CrudPageAbstract
{
    /**
     * RSSエントリでサポートされるエントリ要素
     *
     * @var array
     */
    private static $item_support_elements = array('title', 'link', 'description', 'author', 'guid', 'pubDate');

    /**
     * RSS情報を取得する
     *
     * @param int ページ数
     * @param array ソートカラム
     * @param int 1ページの件数
     * @return array 一覧表示情報
     */
    public function getRss($page_count=1, $sorts=array(), $row_count=0)
    {
        // ソート
        if (count($sorts) == 0) {
            $sorts = $this->config->getRssDefaultSort();
        }
        // 件数
        if ($row_count == 0) {
            $row_count = $this->config->getRssRowCount();
        }

        $item_format = $this->config->getRssItemFormat();
        if (!$item_format) {
            throw new SyL_CrudNotFoundException('rss config item_format not setting (SyL_CrudConfigAbstract::$rss_config["item_format"])');
        }

        list($headers, $dbrows, $pager) = $this->config->getAccess()->getList($page_count, $sorts, array(), $row_count, false);

        $channel = new SyL_RssElementChannel();
        $channel->title = $this->getName();
        $channel->link = $this->config->getUrlLst();
        $channel->description = $this->getDescription();
        $channel->language = 'ja';
        //$channel->copyright = 'Copyright';
        $channel->pubDate = new DateTime();
        $channel->generator = 'SyL Framework CRUD Library';

        foreach ($dbrows as &$record) {
            $item = new SyL_RssElementItem();
            foreach (self::$item_support_elements as $name) {
                if (!empty($item_format[$name])) {
                    $value = self::replaceItem($record, $item_format[$name]);
                    if ($name == 'pubDate') {
                        $item->{$name} = new DateTime($value);
                    } else {
                        $item->{$name} = $value;
                    }
                }
            }
            $channel->addItem($item);
        }

        $rss = new SyL_RssElementRss();
        $rss->setChannel($channel);

        return $rss;
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

