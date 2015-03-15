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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';
/** RSS RSS要素クラス */
require_once 'SyL_RssElementRss.php';
/** RSS関連の例外クラス */
require_once 'SyL_RssException.php';

/**
 * RSSパーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_RssParserAbstract extends SyL_XmlParserAbstract
{
    /**
     * RSS要素オブジェクト
     * 
     * @var SyL_RssElementRss
     */
    protected $rss = null;
    /**
     * RSSバージョン
     * 
     * @var string
     */
    protected $version = null;
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
     * RSSインスタンスを取得
     *
     * @param string RSS XMLデータ
     * @param string RSS バージョン
     * @return SyL_RssParser RSSリーダオブジェクト
     */
    public static function createInstance($data, $version='')
    {
        $tmp = substr(ltrim($data), 0, 100); // about
        if ($version) {
            switch ($version) {
            case '0.91':
            case '1.0':
            case '2.0':
                break;
            default:
                throw new SyL_RssInvalidVersionException("invalid rss version ({$version})");
            }
        } else {
            // バージョンが指定されない場合は自動判定
            if (preg_match('/<rss[^>]+version=\"([0-9\.]+)\"[^>]*>/i', $tmp, $matches)) {
                $version = $matches[1];
            } else if (preg_match('/<([\w\-]*:)?RDF/', $tmp)) {
                $version = '1.0';
            } else {
                throw new SyL_RssInvalidVersionException('rss version not found');
            }
        }

        // 先頭から100バイト目までにエンコーディングの定義があったら取得
        $original_encode = null;
        if (preg_match ('/<\?xml .*encoding=\"(.+)\".*\?>/i', $tmp, $matches)) {
            $original_encode = $matches[1];
        }

        $classname = 'SyL_RssParser' . str_replace('.', '_', $version);
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
        $this->rss = new SyL_RssElementRss();
        $this->rss->setVersion($this->version);
        $this->channel = new SyL_RssElementChannel();
        parent::parse();
        $this->rss->setChannel($this->channel);
    }

    /**
     * RSS要素オブジェクトを取得する
     *
     * @return SyL_RssElementRss RSS要素オブジェクト
     */
    public function getRss()
    {
        return $this->rss;
    }
}
