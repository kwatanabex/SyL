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

/**
 * AtomPub Personコンストラクト要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Atom
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_AtomElementPersonAbstract extends SyL_AtomElementAbstract
{
    /**
     * 要素名
     *
     * @var string
     */
    protected $element_name = null;
    /**
     * 名前
     *
     * @var string
     */
    private $name = null;
    /**
     * URI
     *
     * @var string
     */
    private $uri = null;
    /**
     * メールアドレス
     *
     * @var string
     */
    private $email = null;

    /**
     * 名前を取得する
     *
     * @return string 名前
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * 名前をセットする
     *
     * @param string 名前
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * メールアドレスを取得する
     *
     * @return string メールアドレス
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * メールアドレスをセットする
     *
     * @param string メールアドレス
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * XMLWriterオブジェクトにAtomPub要素を適用する
     * 
     * @param XMLWriter XMLWriterオブジェクト
     */
    public function apply(XMLWriter $xml)
    {
        if (!$this->element_name) {
            throw new SyL_InvalidParameterException('element_name not setting');
        }

        $xml->startElement($this->element_name);
        $xml->writeElement('name', $this->name);
        if ($this->uri !== null) {
            $xml->writeElement('uri', $this->uri);
        }
        if ($this->email !== null) {
            $xml->writeElement('email', $this->email);
        }
        $xml->endElement();
    }
}