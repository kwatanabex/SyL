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
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * AtomPubフィード生成媒体要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementGenerator extends SyL_AtomElementAbstract
{
    /**
     * フィード生成媒体
     *
     * @var string
     */
    private $text = null;
    /**
     * URI
     *
     * @var string
     */
    private $uri = null;
    /**
     * バージョン
     *
     * @var string
     */
    private $version = null;

    /**
     * フィード生成媒体を取得する
     *
     * @return string フィード生成媒体
     */
    public function getText()
    {
        return $this->text;
    }
    /**
     * フィード生成媒体をセットする
     *
     * @param string フィード生成媒体
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * URIを取得する
     *
     * @return string URI
     */
    public function getUri()
    {
        return $this->uri;
    }
    /**
     * URIをセットする
     *
     * @param string URI
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * バージョンを取得する
     *
     * @return string バージョン
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * バージョンをセットする
     *
     * @param string バージョン
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('generator');
        if ($this->uri !== null) {
            $xml->writeAttribute('uri', $this->uri);
        }
        if ($this->version !== null) {
            $xml->writeAttribute('version', $this->version);
        }

        $xml->text($this->text);

        $xml->endElement();
    }
}
