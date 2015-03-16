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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** AtomPubルート要素インターフェイス */
require_once 'SyL_AtomElementRootInterface.php';
/** AtomPub要素クラス */
require_once 'SyL_AtomElementAbstract.php';
/**  AtomPubワークスペース要素クラス */
require_once 'SyL_AtomElementWorkspace.php';

/**
 * AtomPubサービス要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementService extends SyL_AtomElementAbstract implements SyL_AtomElementRootInterface
{
    /**
     * AtomPubのサービス文書のエンコーディング
     *
     * @var string
     */
    private $encoding = 'UTF-8';
    /**
     * ワークスペースオブジェクト
     *
     * @var array
     */
    private $workspaces = array();

    /**
     * RSSエンコーディングを取得する
     *
     * @return string RSSエンコーディング
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
    /**
     * RSSエンコーディングをセットする
     *
     * @param string RSSエンコーディング
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * ワークスペースオブジェクトの配列を取得する
     *
     * @return SyL_RssElementChannel ワークスペースオブジェクトの配列
     */
    public function getWorkspaces()
    {
        return $this->channel;
    }
    /**
     * ワークスペースオブジェクトの配列をセットする
     *
     * @param array ワークスペースオブジェクトの配列
     */
    public function setWorkspaces(array $workspaces)
    {
        $this->workspaces = $workspaces;
    }
    /**
     * ワークスペースオブジェクトをセットする
     *
     * @param SyL_AtomElementWorkspace ワークスペースオブジェクト
     */
    public function addWorkspace(SyL_AtomElementWorkspace $workspace)
    {
        $this->workspaces[] = $workspace;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        if ($this->enable_enclosure) {
            $xml->startElement('service');
            $xml->writeAttribute('xmlns', 'http://www.w3.org/2007/app');
            $xml->writeAttributeNS('xmlns', 'atom', null, 'http://www.w3.org/2005/Atom');
            foreach (self::$namespaces as $name => $uri) {
                $xml->writeAttributeNS('xmlns', $name, null, $uri);
            }
        }

        foreach ($this->workspaces as &$workspace) {
            $workspace->apply($xml);
        }

        if ($this->enable_enclosure) {
            $xml->endElement();
        }
    }
}
