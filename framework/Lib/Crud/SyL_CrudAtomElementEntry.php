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
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * CRUD用 AtomPubエントリ要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CrudAtomElementEntry extends SyL_AtomElementEntry
{
    /**
     * 囲い要素が有効か
     *
     * @var bool
     */
    protected $enable_enclosure = false;
    /**
     * 接頭辞
     * 
     * @var string
     */
    private $prefix = '';
    /**
     * 要素名
     * 
     * @var array
     */
    private $names = array();
    /**
     * 要素値
     * 
     * @var array
     */
    private $texts = array();

    /**
     * コンストラクタ
     *
     * @param string 接頭辞
     * @param array 要素名
     */
    public function __construct($prefix, array $names)
    {
        parent::__construct(false);

        $this->prefix = $prefix;
        foreach ($names as $name) {
            $this->names[] = $prefix . ':' . $name;
        }
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function __set($name, $value) 
    {
        if (array_search($name, $this->names) !== false) {
            $this->texts[] = array($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        $xml->startElement('entry');
        $xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        foreach (self::$namespaces as $name => $uri) {
            $xml->writeAttributeNS('xmlns', $name, null, $uri);
        }

        parent::apply($xml);

        foreach ($this->texts as $text) {
            if ($text[1] instanceof SyL_AtomElementAbstract) {
                $xml->startElement($text[0]);
                $text[1]->apply($xml);
                $xml->endElement();
            } else {
                $xml->writeElement($text[0], $text[1]);
            }
        }

        $xml->endElement();
    }
}
