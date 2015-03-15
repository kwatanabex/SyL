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
 * RSSカテゴリ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementCategory extends SyL_RssElementAbstract
{
    /**
     * カテゴリ
     *
     * @var string
     */
     private $category = null;
    /**
     * ドメイン
     *
     * @var string
     */
     private $domain = null;

    /**
     * カテゴリを取得する
     *
     * @return string カテゴリ
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * カテゴリをセットする
     *
     * @param string カテゴリ
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * ドメインを取得する
     *
     * @return string ドメイン
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * ドメインをセットする
     *
     * @param string ドメイン
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * XMLWriterオブジェクトにRSS2.0要素を適用する
     *
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('category');
        if ($this->domain !== null) {
            $xml->writeAttribute('domain', $this->domain);
        }
        $xml->text($this->category);
        $xml->endElement();
    }
}

