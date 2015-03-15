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
 * フォーム要素表示クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormView
{
    /**
     * フォームの属性
     *
     * @var array
     */
    private $form_attributes = array();
    /**
     * フォーム要素の配列
     *
     * @var array
     */
    private $elements = array();
    /**
     * 遷移する際のサブミット要素オブジェクト
     *
     * @var array
     */
    private $buttons = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * フォームオブジェクトをセットする
     *
     * @param SyL_Form フォームオブジェクト
     */
    public function addForm(SyL_Form $form)
    {
        $this->form_attributes = $form->getAttributes();
        $this->elements = array_merge($this->elements, $form->getElements());
    }

    /**
     * フォームの属性をHTML文字列として取得する
     *
     * @return string フォームの属性文字列
     */
    public function getFormAttributeString()
    {
        $html = '';
        foreach ($this->form_attributes as $name => $value) {
            if ($html) {
                $html .= ' ';
            }
            $html .= sprintf('%s="%s"', htmlspecialchars($name), htmlspecialchars($value));
        }
        return $html;
    }

    /**
     * 次へ遷移する際のサブミット要素オブジェクトをセットする
     *
     * @param SyL_FormElementSubmit サブミット要素オブジェクト
     */
    public function addButtonElement(SyL_FormElementAbstract $button)
    {
        $this->buttons[] = $button;
    }

    /**
     * フォーム内の各要素名を配列で取得する
     *
     * @param bool hidden要素名取得フラグ
     * @return array フォーム内の各要素名
     */
    public function getElementNames($hidden=false)
    {
        $names = array();
        foreach ($this->elements as &$element) {
            if ($hidden && $element->isHidden()) {
                $names[] = $element->getName();
            } else if (!$hidden && !$element->isHidden()) {
                $names[] = $element->getName();
            }
        }
        return $names;
    }

    /**
     * 遷移する際のサブミットボタンHTMLを取得する
     *
     * @return array サブミットボタンHTML
     */
    public function getHtmlSubmits()
    {
        $buttons = array();
        foreach ($this->buttons as &$button) {
            $buttons[] = $button->getHtml();
        }
        return $buttons;
    }

    /**
     * hidden要素のHTMLをすべて取得する
     *
     * @return array hidden要素のHTML
     */
    public function getHtmlHiddens()
    {
        $hiddens = array();
        foreach ($this->elements as &$element) {
            if ($element->isHidden()) {
                $hiddens[] = $element->getHtml();
            }
        }
        return $hiddens;
    }

    /**
     * フォーム要素表示名を取得する
     *
     * @param string フォーム要素名
     * @return string フォーム要素表示名
     */
    public function getDisplayName($name)
    {
        return $this->elements[$name]->getDisplayName();
    }

    /**
     * フォーム要素の値を取得
     *
     * @param string フォーム要素名
     * @return mixed フォーム要素の値
     */
    public function getValue($name)
    {
        return $this->elements[$name]->getValue();
    }

    /**
     * フォーム要素のHTMLを取得
     *
     * @param string フォーム要素名
     * @return string フォーム要素のHTML
     */
    public function getHtml($name)
    {
        return $this->elements[$name]->getHtml($this->form_attributes['name']);
    }

    /**
     * フォーム要素が必須か判定する
     *
     * @param string フォーム要素名
     * @return bool フォーム要素の必須判定
     */
    public function isRequire($name)
    {
        return $this->elements[$name]->isRequire();
    }

    /**
     * フォーム要素が表示のみか判定する
     *
     * @param string フォーム要素名
     * @return bool フォーム要素の表示判定
     */
    public function isReadOnly($name)
    {
        return $this->elements[$name]->isReadOnly();
    }

    /**
     * フォーム要素のエラーを判定する
     *
     * @param string フォーム要素名
     * @return bool true: エラーあり、false: エラー無し
     */
    public function isError($name)
    {
        return $this->elements[$name]->isError();
    }

    /**
     * 全フォーム要素のエラーを判定する
     *
     * @return bool true: エラーあり、false: エラー無し
     */
    public function isErrors()
    {
        foreach ($this->elements as &$element) {
            if ($element->isError()) {
                return true;
            }
        }
        return false;
    }

    /**
     * フォーム要素のエラーメッセージをセットする
     *
     * @param string フォーム要素名
     * @param string エラーメッセージ
     */
    public function setErrorMessage($name, $error_message)
    {
        $this->elements[$name]->setErrorMessage($error_message);
    }

    /**
     * フォーム要素のエラーメッセージを取得する
     *
     * @param string フォーム要素名
     * @return string エラーメッセージ
     */
    public function getErrorMessage($name)
    {
        return $this->elements[$name]->getErrorMessage();
    }

    /**
     * フォーム要素の全エラーメッセージを取得する
     *
     * @param string フォーム要素名
     * @return array エラーメッセージ配列
     */
    public function getErrorMessages()
    {
        $error_messages = array();
        foreach ($this->elements as &$element) {
            $error_message = $element->getErrorMessage();
            if ($error_message) {
                $error_messages[] = $error_message;
            }
        }
        return $error_messages;
    }
}

