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
 * フォーム要素グループクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementGroup extends SyL_FormElementAbstract
{
    /**
     * フォーム要素の値
     *
     * @var mixed
     */
    protected $value = array();
    /**
     * 表示フォーマット
     *
     * @var string
     */
    private $format = null;

    /**
     * 表示フォーマットをセットする
     *
     * @param string 表示フォーマット
     */
    public function setFormat($format)
    {
        $this->format = $format;
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
        } else if ($value instanceof SyL_FormElementAbstract) {
            $this->value = array($value);
        } else {
            throw new SyL_InvalidParameterException('invalid parameter format (' . gettype($value) . ')');
        }
    }

    /**
     * 必須チェック存在判定
     * ※検証グループオブジェクトをセットしてから実行する
     *
     * @return bool true: 必須チェックあり、false: 必須チェック無し
     */
    public function isRequire()
    {
        $is = false;
        foreach ($this->value as &$element) {
            if ($element->isRequire()) {
                $is = true;
                break;
            }
        }
        return $is;
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        $htmls = array();
        foreach ($this->value as &$element) {
            $htmls[] = $element->getHtmlTag();
        }

        if ($this->format) {
            return vsprintf($this->format, $htmls);
        } else {
            return implode('', $htmls);
        }
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        $htmls = array();
        foreach ($this->value as &$element) {
            $htmls[] = $element->getHtmlView();
        }

        if ($this->format) {
            return vsprintf($this->format, $htmls);
        } else {
            return implode('', $htmls);
        }
    }

    /**
     * フォーム要素をhiddenタグで出力
     *
     * @return string フォーム要素のhiddenタグ
     */
    public function getHtmlHidden()
    {
        $htmls = array();
        foreach ($this->value as &$element) {
            $htmls[] = $element->getHtmlHidden();
        }
        return implode('', $htmls);
    }

    /**
     * 検証オブジェクトをセット
     *
     * @param SyL_ValidationAbstract 検証オブジェクト
     * @throws SyL_NotImplementedException このメソッドは実装されていないので必ず発生する
     */
    public function setValidation(SyL_ValidationAbstract $validation)
    {
        throw new SyL_NotImplementedException('not supported method (' . __METHOD__ . ')');
    }

    /**
     * 検証を実行する
     * 検証グループオブジェクトがない場合は、常にtrue
     *
     * @return bool true: エラー無し、false: エラーあり
     */
    public function validate()
    {
        $valid = true;
        foreach ($this->value as &$element) {
            if (!$element->validate()) {
                $this->setErrorMessage($element->getErrorMessage());
                $valid = false;
                break;
            }
        }
        return $valid;
    }

    /**
     * JavaScript入力チェックタグを取得
     *
     * @access public
     * @return string JavaScript入力チェックタグ
     */
/*
    function getJs($formname)
    {
        $js     = '';
        $error_confirm = array();
        foreach (array_keys($this->value) as $name) {
            $tmp     = $this->value[$name]->getJs($formname);
            $element = $this->value[$name]->getNames();
            if (count($tmp) == 2) {
                if (count($error_confirm) > 0) {
                    $js .= 'if (' . implode(' && ', $error_confirm) . ') {' . "\n";
                    $js .= $tmp[1] . "\n";
                    $js .= $tmp[0] . ';' . "\n";
                    $js .= '}' . "\n";
                } else {
                    $js .= $tmp[1] . "\n";
                    $js .= $tmp[0] . ';' . "\n";
                }
            }
            $error_confirm[] = "!errors.isError('{$element}')";
        }

        if ($js) {
            $element = $this->getName();
            $func    = "check_{$formname}_{$element}(form, errors)";
            $js   = <<< JAVASCRIPT_CODE
function {$func}
{
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
