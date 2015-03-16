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
 * AtomPubカテゴリ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomElementCategory extends SyL_AtomElementAbstract
{
    /**
     * Term
     *
     * @var string
     */
    private $term = null;
    /**
     * scheme
     *
     * @var string
     */
    private $scheme = null;
    /**
     * label
     *
     * @var string
     */
    private $label = null;

    /**
     * Termを取得する
     *
     * @return string Term
     */
    public function getTerm()
    {
        return $this->term;
    }
    /**
     * Termをセットする
     *
     * @param string Term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * Schemeを取得する
     *
     * @return string Scheme
     */
    public function getScheme()
    {
        return $this->scheme;
    }
    /**
     * Schemeをセットする
     *
     * @param string Scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Labelを取得する
     *
     * @return string Label
     */
    public function getLabel()
    {
        return $this->label;
    }
    /**
     * Labelをセットする
     *
     * @param string Label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('atom:category');
        $xml->writeAttribute('term', $this->term);
        if ($this->scheme !== null) {
            $xml->writeAttribute('scheme', $this->scheme);
        }
        if ($this->label !== null) {
            $xml->writeAttribute('label', $this->label);
        }
        $xml->endElement();
    }
}