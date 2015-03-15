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

/** 汎用プロパティクラス */
require_once dirname(__FILE__) . '/../SyL_Property.php';

/** 
 * テンプレートクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Template
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_TemplateAbstract
{
    /**
     * テンプレートファイル
     *
     * @var string
     */
    protected $template_file = '';
    /**
     * サブテンプレート配列
     *
     * @var array
     */
    protected $sub_templates = array();
    /**
     * パラメータ配列
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * コンストラクタ
     *
     * @param string テンプレートファイル
     */
    public function __construct($template_file)
    {
        $this->template_file = $template_file;
    }

    /**
     * サブテンプレートをセットする
     *
     * @param string サブテンプレート名
     * @param SyL_TemplateAbstract テンプレートオブジェクト
     */
    public function setSubTemplate($name, SyL_TemplateAbstract $template)
    {
        $this->sub_templates[$name] = $template;
    }

    /**
     * パラメータをセットする
     *
     * @param string パラメータ名
     * @param string パラメータ値
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * 全パラメータを取得する
     *
     * @return array パラメータ配列
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * テンプレートを適用する
     *
     * @return string テンプレート適用後データ
     */
    public abstract function apply();

    /**
     * サブテンプレートを適用する
     *
     * @return array テンプレート適用後データ配列
     */
    protected function applyChild()
    {
        $templates = array();
        foreach ($this->sub_templates as $template_name => &$sub_template) {
            foreach (($sub_template->getParameters() + $this->parameters) as $name => $value) {
                $sub_template->setParameter($name, $value);
            }
            $templates[$template_name] = $sub_template->apply();
        }
        return $templates;
    }
}
