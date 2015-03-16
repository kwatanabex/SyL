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

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';
/** AtomPubフィード要素クラス */
require_once 'SyL_AtomElementFeed.php';
/** AtomPub関連の例外クラス */
require_once 'SyL_AtomException.php';

/**
 * Atomパーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_AtomParserAbstract extends SyL_XmlParserAbstract
{
    /**
     * Atom要素オブジェクト
     * 
     * @var SyL_AtomElementFeed
     */
    protected $feed = null;
    /**
     * RSSチャネル要素オブジェクト
     * 
     * @var SyL_RssElementChannel
     */
    protected $channel = null;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * Atomインスタンスを取得
     *
     * @param string Atom XMLデータ
     * @return SyL_RssParser RSSリーダオブジェクト
     */
    public static function createInstance($data)
    {
        $tmp = substr(ltrim($data), 0, 100); // about

        // 先頭から100バイト目までにエンコーディングの定義があったら取得
        $original_encode = null;
        if (preg_match ('/<\?xml .*encoding=\"(.+)\".*\?>/i', $tmp, $matches)) {
            $original_encode = $matches[1];
        }

        $classname = 'SyL_AtomParser';
        include_once $classname . '.php';
        $parser = new $classname();
        if ($original_encode) {
            $parser->setInputEncoding($original_encode);
        }
        $parser->setData($data);
        return $parser;
    }

    /**
     * XMLファイルの解析処理
     *
     * 複数のXMLファイルを解析し、$this->config にセットする。
     * 複数のXMLファイルに、同じキー値が存在した場合、最初に読み込まれたほうが有効となる。
     */
    public function parse()
    {
        $this->feed = new SyL_AtomElementFeed();
        parent::parse();
    }

    /**
     * RSS要素オブジェクトを取得する
     *
     * @return SyL_RssElementRss RSS要素オブジェクト
     */
    public function getFeed()
    {
        return $this->feed;
    }
}
