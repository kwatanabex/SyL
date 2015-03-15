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
 * @subpackage SyL.Lib.Template
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** テンプレートクラス */
require_once 'SyL_TemplateAbstract.php';
/** XSLT 適用クラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlXslt.php';

/** 
 * XSLT テンプレートクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Template
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_TemplateXslt extends SyL_TemplateAbstract
{
    /**
     * XMLファイル
     *
     * @var string
     */
    private $xml_file = '';

    /**
     * コンストラクタ
     *
     * @param string XSLテンプレートファイル
     * @param string XMLファイル
     */
    public function __construct($template_file, $xml_file='')
    {
        parent::__construct($template_file);
        $this->xml_file = $xml_file;
    }

    /**
     * テンプレートを適用する
     *
     * @return string テンプレート適用後データ
     */
    public function apply()
    {
        $xsl = new DOMDocument();
        $xsl->load($this->template_file);

        $xml = new DOMDocument();
        if ($this->xml_file) {
            $xml->load($this->xml_file);
        } else {
            $xml->loadXML('<?xml version="1.0" encoding="UTF-8"?><root></root>');
        }

        $parameters = $this->parameters;
        // サブテンプレート適用
        foreach ($this->applyChild() as $name => $value) {
            $parameters[$name] = $value;
        }

        return SyL_XmlXslt::transform($xml, $xsl, $parameters);
    }
}
