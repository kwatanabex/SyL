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

/**
 * RSSクラウド要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssElementCloud extends SyL_RssElementAbstract
{
    /**
     * ドメイン
     *
     * @var string
     */
     private $domain = null;
    /**
     * ポート
     *
     * @var string
     */
     private $port = null;
    /**
     * パス
     *
     * @var string
     */
     private $path = null;
    /**
     * プロシージャ
     *
     * @var string
     */
     private $register_procedure = null;
    /**
     * プロトコル
     *
     * @var string
     */
     private $protocol = null;

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
     * ポートを取得する
     *
     * @return string ポート
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * ポートをセットする
     *
     * @param string ポート
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * パスを取得する
     *
     * @return string パス
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * パスをセットする
     *
     * @param string パス
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * プロシージャを取得する
     *
     * @return string プロシージャ
     */
    public function getRegisterProcedure()
    {
        return $this->register_procedure;
    }

    /**
     * プロシージャをセットする
     *
     * @param string プロシージャ
     */
    public function setRegisterProcedure($register_procedure)
    {
        $this->register_procedure = $register_procedure;
    }

    /**
     * プロトコルを取得する
     *
     * @return string プロトコル
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * プロトコルをセットする
     *
     * @param string プロトコル
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * XMLWriterオブジェクトに要素を適用する
     *
     * @return XMLWriter XMLWriterオブジェクト
     */
    public function apply2_0(XMLWriter $xml)
    {
        $xml->startElement('cloud');
        if ($this->domain !== null) {
            $xml->writeAttribute('domain', $this->domain);
        }
        if ($this->port !== null) {
            $xml->writeAttribute('port', $this->port);
        }
        if ($this->path !== null) {
            $xml->writeAttribute('path', $this->path);
        }
        if ($this->register_procedure !== null) {
            $xml->writeAttribute('registerProcedure', $this->register_procedure);
        }
        if ($this->protocol !== null) {
            $xml->writeAttribute('protocol', $this->protocol);
        }
        $xml->endElement();
    }
}

