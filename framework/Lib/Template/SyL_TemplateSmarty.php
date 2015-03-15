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

/** 
 * Smartyテンプレートクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Template
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_TemplateSmarty extends SyL_TemplateAbstract
{
    /**
     * Smarty オブジェクト
     *
     * @var Smarty
     */
    private $smarty = null;

    /**
     * Smarty オブジェクトをセットする
     *
     * @param Smarty Smartyオブジェクト
     */
    public function setSmarty(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * サブテンプレートを適用する
     *
     * @return array テンプレート適用後データ配列
     */
    protected function applyChild()
    {
        foreach ($this->sub_templates as &$sub_template) {
            $sub_template->setSmarty($this->smarty);
        }
        return parent::applyChild();
    }

    /**
     * テンプレートを適用する
     *
     * @return string テンプレート適用後データ
     */
    public function apply()
    {
        foreach ($this->parameters as $name => &$value) {
            if (is_object($value)) {
                $this->smarty->assign_by_ref($name, $value);
            } else {
                $this->smarty->assign($name, $value);
            }
        }

        // サブテンプレート適用
        foreach ($this->applyChild() as $name => $value) {
            $this->smarty->assign($name, $value);
        }

        return $this->smarty->fetch($this->template_file);
    }
}
