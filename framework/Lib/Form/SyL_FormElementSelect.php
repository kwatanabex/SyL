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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * プルダウンフォーム要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementSelect extends SyL_FormElementAbstract
{
    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        $this->setAttribute($this->prefix_id . $this->name, 'id');
        $this->setAttribute($this->name, 'name');

        $options = array();
        foreach ($this->options as $display_name => $value) {
            $selected = ((string)$value === $this->value) ? ' selected' : '';
            $options[] = sprintf('<option value="%s"%s>%s</option>', self::encode($value), $selected, self::encode($display_name));
        }

        return sprintf('<select %s>%s</select>', $this->getAttributeString(), implode("\r\n", $options));
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        $label = '';
        foreach ($this->options as $display_name => $value) {
            if ((string)$value === $this->value) {
                $label = $display_name;
                break;
            }
        }

        return $this->getHtmlSpan($this->name, self::encode($label)) . $this->getHtmlHidden();
    }

    /**
     * JavaScript入力チェックタグを取得
     *
     * @access public
     * @param string フォーム名
     * @return string JavaScript入力チェックタグ
     */
/*
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
*/

}
