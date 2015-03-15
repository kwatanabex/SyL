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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** テンプレートクラス */
require_once 'SyL_TemplateAbstract.php';

/** 
 * テキストテンプレートクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Template
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_TemplateText extends SyL_TemplateAbstract
{
    /**
     * パラメータ変換接頭／接尾辞
     *
     * @var array
     */
    private $convert_string = array('{$', '}');

    /**
     * コンストラクタ
     *
     * @param string テンプレートファイル
     * @param array パラメータ変換接頭／接尾辞
     */
    public function __construct($template_file, array $convert_string=array('{$', '}'))
    {
        parent::__construct($template_file);
        $this->convert_string = $convert_string;
    }

    /**
     * テンプレートを適用する
     *
     * @return string テンプレート適用後データ
     */
    public function apply()
    {
        $parameters = array();
        foreach ($this->parameters as $name => &$value) {
            $name = $this->convert_string[0] . $name . $this->convert_string[1];
            $parameters[$name] = $value;
        }

        // サブテンプレート適用
        foreach ($this->applyChild() as $name => $value) {
            $name = $this->convert_string[0] . $name . $this->convert_string[1];
            $parameters[$name] = $value;
        }

        return strtr(file_get_contents($this->template_file), $parameters);
    }
}
