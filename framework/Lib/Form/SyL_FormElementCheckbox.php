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
 * チェックボックスフォーム要素クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementCheckbox extends SyL_FormElementAbstract
{
    /**
     * フォーム要素の値
     *
     * @var mixed
     */
    protected $value = array();
    /**
     * 要素間の区切り文字
     *
     * @var string
     */
    private $separator = "<br />\r\n";

    /**
     * 要素名（複数）を取得する
     *
     * @return string 要素名
     */
    private function getNames()
    {
        return $this->name . '[]';
    }

    /**
     * 要素値をセットする
     *
     * @param mixed 要素値
     * @throws SyL_InvalidParameterException パラメータが規定外の型の場合
     */
    public function setValue($value)
    {
        if (($value === '') || ($value === null)) {
            $this->value = array();
        } else if (is_array($value)) {
            $this->value = $value;
        } else if (is_scalar($value)) {
            $this->value = array($value);
        } else {
            throw new SyL_InvalidParameterException('invalid parameter format (' . gettype($value) . ')');
        }
    }

    /**
     * 要素間の区切り文字をセット
     *
     * @param string 要素間の区切り文字
     */
    public function setSeparator($separetor)
    {
        $this->separator = $separetor;
    }

    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        $this->setAttribute($this->getNames(), 'name');
        $this->setAttribute('checkbox', 'type');

        $options = array();
        $i = 1;
        foreach ($this->options as $display_name => $value) {
            $id = $this->prefix_id . $this->getNames() . '_' . $i++;
            $this->setAttribute($id, 'id');
            $this->setAttribute($value, 'value');
            $checked = in_array((string)$value, $this->value, true) ? ' checked' : '';
            $options[] = sprintf('<input %s /><label for="%s">%s</label>', $this->getAttributeString() . $checked, self::encode($id), self::encode($display_name));
        }

        return implode($this->separator, $options);
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        $options = array();
        foreach ($this->options as $display_name => $value) {
            if (in_array((string)$value, $this->value, true)) {
                $options[] = self::encode($display_name);
            }
        }
        return $this->getHtmlSpan($this->getNames(), implode($this->separator, $options)) . $this->getHtmlHidden();
    }

    /**
     * フォーム要素をhiddenタグで出力
     *
     * @param string 要素名
     * @param string 要素値
     * @return string フォーム要素のhiddenタグ
     */
    public function getHtmlHidden()
    {
        $hiddens = '';
        foreach ($this->options as $display_name => $value) {
            if (in_array((string)$value, $this->value, true)) {
                $hiddens .= sprintf(self::HIDDEN_TEMPLATE, $this->getNames(), self::encode($value));
            }
        }
        return $hiddens;
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
  var message = '';

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
