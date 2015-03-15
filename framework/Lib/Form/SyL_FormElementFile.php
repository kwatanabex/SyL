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
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ファイル入力フォーム要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementFile extends SyL_FormElementAbstract
{
    /**
     * 要素間の区切り文字
     *
     * @access private
     * @var string
     */
    var $separator = "&nbsp;/&nbsp;";

    /**
     * 要素名（複数）を取得する
     *
     * @access public
     * @return string 要素名
     */
    function getNames()
    {
        return $this->getName() . '[]';
    }

    /**
     * 要素値をセットする
     *
     * @access public
     * @param mixed 要素値
     */
    function setValue($value)
    {
        $this->value = isset($_FILES[$this->name]) ? $_FILES[$this->name] : null;
    }

    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @access public
     * @return string フォーム要素のHTML
     */
    function getHtmlTag()
    {
        $this->setAttribute($this->getNames(), 'name');
        $this->setAttribute($this->type, 'type');

        return '<input ' . $this->getAttributes() . ' />';
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @access public
     * @return string フォーム要素のHTML
     */
    function getHtmlView()
    {
        if (isset($this->value['name'])) {
            return implode($this->separator, array_map(array(&$this, 'encode'), $this->value['name']));
        } else {
            return '&nbsp;';
        }
    }

    /**
     * JavaScript入力チェックタグを取得
     *
     * @access public
     * @param string フォーム名
     * @return string JavaScript入力チェックタグ
     */
    function getJs($formname)
    {
        if (is_object($this->validators)) {
            $element = $this->getNames();
            $func    = "check_{$formname}_" . $this->getName() . "(form, errors)";
            $js      = $this->validators->getJs($this->getDisplayName());
            $js = <<< JAVASCRIPT_CODE
function {$func} {
  var validation    = new SyL.Validation.Validation(form);
  var name  = "{$element}";
  var message = "";

{$js}

  if (message) {
    errors.setErrorMessage(name, message);
  }
}

JAVASCRIPT_CODE;
            return array($func, $js);
        } else {
            return array();
        }
    }
}
