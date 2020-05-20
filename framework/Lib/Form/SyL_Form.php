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

/** フォーム関連の例外クラス */
require_once 'SyL_FormException.php';
/** フォーム要素クラス */
require_once 'SyL_FormElementAbstract.php';
/** フォーム設定クラス */
require_once 'SyL_FormConfigAbstract.php';
/** フォーム表示クラス */
require_once 'SyL_FormView.php';
/** 検証クラス */
require_once dirname(__FILE__) . '/../Validation/SyL_ValidationAbstract.php';

/**
 * フォームクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_Form
{
    /**
     * フォーム属性配列
     *
     * @var array
     */
    private $attributes = array();
    /**
     * フォーム要素の基本オブジェクト格納配列
     *
     * @var array
     */
    private $elements = array();
    /**
     * パラメータ取得関数
     *
     * @var callback
     */
    private static $auto_input_parameter_callback = null;
    /**
     * フォーム表示タイプ
     *
     * 0 - 入力
     * 1 - 表示のみ
     * 2 - hidden
     *
     * @var int
     */
    private $view_type = 0;
    /**
     * 追加バリデーションメソッド
     *
     * @var callback
     */
    private $custom_validation_callback = null;

    /**
     * JavaScriptパラメータ
     *
     * @access private
     * @var array
     */
     /*
    var $js_parameters = array(
      'func_name'   => '',
      'all_message' => true,
      'header'      => '',
      'footer'      => '',
      'error_callback' => null
    );
    */

    /**
     * コンストラクタ
     *
     * @param string フォーム名
     * @param string フォームサブミット時のアクション先
     */
    public function __construct($name='syl_form', $action='')
    {
        $this->setAttribute('name', $name);
        $this->setAttribute('id',   $name);
        $this->setAttribute('method', 'POST');
        $this->setAttribute('action', $action ? $action : $_SERVER['PHP_SELF']);
    }

    /**
     * フォーム要素オブジェクトの作成する
     *
     * @param string フォーム要素タイプ
     * @param string フォーム要素名
     * @param string フォーム要素表示名
     * @param array フォーム要素の追加属性
     * @param mixed フォーム要素の値
     * @param array フォーム要素の部品配列（radio, select, checkbox用）
     * @param SyL_ValidationAbstract 検証オブジェクト
     */
    public function createElement($type, $element_name, $display_name, array $attributes=array(), $value=null, array $options=array(), SyL_ValidationAbstract $validation=null)
    {
        if (($value === null) || (is_array($value) && (count($value) == 0))) {
            $value = self::getParameter($element_name);
        }
        $element = SyL_FormElementAbstract::createInstance($type, $element_name, $display_name, $value, $attributes);
        foreach ($options as $k => $v) {
            $element->setOption($k, $v);
        }
        $this->addElement($element, $validation);
    }

    /**
     * グループ要素オブジェクトの作成する
     *
     * @param string フォーム要素名
     * @param string フォーム要素表示名
     * @param array フォーム要素配列
     */
    public function createElementGroup($element_name, $display_name, array $elements, $format=null)
    {
        foreach ($elements as &$element) {
            $value = $element->getValue();
            if (($value === null) || (is_array($value) && (count($value) == 0))) {
                $element->setValue(self::getParameter($element->getName()));
            }
        }

        $group = SyL_FormElementAbstract::createInstance('group', $element_name, $display_name);
        $group->setFormat($format);
        $group->setValue($elements);
        $this->addElement($group);
    }

    /**
     * フォーム要素設定オブジェクトから要素を作成する
     *
     * @param SyL_FormConfigAbstract フォーム要素設定オブジェクト
     * @param bool デフォルト値設定フラグ
     */
    public function createElementFromConfig(SyL_FormConfigAbstract $config, $default=false)
    {
        foreach ($config->getElements($default) as $element) {
            if (!$default) {
                $value = $element->getValue();
                if ($element instanceof SyL_FormElementGroup) {
                    foreach ($value as &$element1) {
                        $value1 = $element1->getValue();
                        if (($value1 === null) || (is_array($value1) && (count($value1) == 0))) {
                            $element1->setValue(self::getParameter($element1->getName()));
                        }
                    }
                    $element->setValue($value);
                } else {
                    if (($value === null) || (is_array($value) && (count($value) == 0))) {
                        $element->setValue(self::getParameter($element->getName()));
                    }
                }
            }
            $this->addElement($element);
        }
    }

    /**
     * フォーム要素オブジェクトを追加する
     *
     * @param SyL_FormElementAbstract フォーム要素オブジェクト
     * @param SyL_ValidationAbstract 検証オブジェクト
     */
    public function addElement(SyL_FormElementAbstract $element, SyL_ValidationAbstract $validation=null)
    {
        if ($validation != null) {
            $element->setValidation($validation);
        }
        if ($element instanceof SyL_FormElementFile) {
            $this->setAttribute('enctype', 'multipart/form-data');
        } else if ($element instanceof SyL_FormElementGroup) {
            foreach ($element->getValue() as $element2) {
                if ($element2 instanceof SyL_FormElementFile) {
                    $this->setAttribute('enctype', 'multipart/form-data');
                    break;
                }
            }
        }
        $element_name = $element->getName();
        $this->elements[$element_name] = $element;
    }

    /**
     * 全フォーム要素オブジェクトを取得する
     *
     * @param string フォーム要素名
     * @return array 全フォーム要素オブジェクト
     */
    public function getElements()
    {
        foreach ($this->elements as &$element) {
            switch ($this->view_type) {
            case '1': $element->isReadOnly(true); break;
            case '2': $element->isHidden(true);   break;
            }
        }
        return $this->elements;
    }

    /**
     * フォーム要素オブジェクトを取得する
     *
     * @param string フォーム要素名
     * @return SyL_FormElementAbstract フォーム要素オブジェクト
     */
    public function getElement($element_name)
    {
        $element = null;
        if (isset($this->elements[$element_name])) {
            $element = $this->elements[$element_name];
            switch ($this->view_type) {
            case '1': $element->isReadOnly(true); break;
            case '2': $element->isHidden(true);   break;
            }
        }
        return $element;
    }

    /**
     * 追加バリデーション関数をセットする
     *
     * @param mixed パラメータ取得関数
     * @throws SyL_InvalidParameterException パラメータが関数形式でない場合
     */
    public function registerCustomValidationCallback($callback)
    {
        if (is_callable($callback)) {
            $this->custom_validation_callback = $callback;
        } else {
            throw new SyL_InvalidParameterException("Invalid callback parameter. no function or method (" . var_export($callback, true) . ")");
        }
    }

    /**
     * パラメータ取得関数をセットする
     *
     * @param mixed パラメータ取得関数
     * @throws SyL_InvalidParameterException パラメータが関数形式でない場合
     */
    public static function registerInputCallback($callback)
    {
        if (is_callable($callback)) {
            self::$auto_input_parameter_callback = $callback;
        } else {
            throw new SyL_InvalidParameterException("Invalid callback parameter. no function or method (" . var_export($callback, true) . ")");
        }
    }

    /**
     * リクエストから値を取得する
     *
     * @param string パラメータ名
     * @return mixed パラメータ値
     */
    public static function getParameter($name)
    {
        if (self::$auto_input_parameter_callback) {
            return call_user_func(self::$auto_input_parameter_callback, $name);
        } else {
            $parameter = isset($_POST[$name]) ? $_POST[$name] : null;
            //if (get_magic_quotes_gpc()) {
            //    if (is_array($parameter)) {
            //        $parameter = array_map('stripslashes', $parameter);
            //    } else {
            //        $parameter = stripslashes($parameter);
            //    }
            //}
            return $parameter;
        }
    }

    /**
     * フォームの属性をセットする
     *
     * @param string フォームの属性
     * @param string フォームの属性値
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * フォームの属性を取得する
     *
     * @param string フォームの属性
     * @return string フォームの属性値
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * フォームの全属性を取得する
     *
     * @return array フォームの全属性値
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * フォーム属性をHTMLの文字列として取得する
     *
     * @return string フォーム属性をHTMLの文字列
     */
    public function getAttributesHtmlString()
    {
        $attribute_String = '';
        foreach ($this->attributes as $name => $value) {
            if ($attribute_String) {
                $attribute_String .= ' ';
            }
            $attribute_String .= $name . '="' . htmlentities($value) . '"';
        }
        return $attribute_String;
    }

    /**
     * フォームが表示要素か判定する
     *
     * @param bool フォーム表示フラグ
     * @return bool true: 表示要素、false: 表示要素以外
     */
    public function isReadOnly($read_only=null)
    {
        if (is_bool($read_only)) {
            $this->view_type = $read_only ? '1' : '0';
        }
        return ($this->view_type == '1');
    }

    /**
     * フォームが隠し要素か判定する
     *
     * @param bool フォーム隠しフラグ
     * @return bool true: 隠し要素、false: 隠し要素以外
     */
    public function isHidden($hidden)
    {
        if (is_bool($hidden)) {
            $this->view_type = $hidden ? '2' : '0';
        }
        return ($this->view_type == '2');
    }

    /**
     * 全ての要素の検証を実行する
     *
     * @return true: エラーなし、false: エラーあり
     */
    public function validate()
    {
        $result = true;
        foreach ($this->elements as &$element) {
            if (!$element->validate()) {
                $result = false;
            }
        }
        if ($this->custom_validation_callback) {
            if (!call_user_func_array($this->custom_validation_callback, array(&$this->elements))) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * フォーム表示オブジェクトを取得する
     *
     * @return SyL_FormView フォーム表示オブジェクト
     */
    public function getView()
    {
        $view = new SyL_FormView();
        $view->addForm($this);
        return $view;
    }

    /**
     * JavaScript使用
     *
     * @access public
     * @param string JavaScriptバリデーション実行関数名
     * @param bool 全エラーメッセージ一括表示フラグ
     */
     /*
    function useJs($func_name='sylFormCheck', $all_message=true)
    {
        $this->js_parameters['func_name'] = $func_name;
        $this->js_parameters['all_message'] = $all_message;
    }
    */

    /**
     * JavaScriptエラー時のコールバック関数をセット
     *
     * @access public
     * @param string JavaScriptエラー時のコールバック関数
     */
    /*
    function setJsCustomErrorCallback($error_callback)
    {
        $this->js_parameters['error_callback'] = $error_callback;
    }
    */

    /**
     * JavaScriptエラーメッセージヘッダ、フッタの設定
     *
     * @access public
     * @param string JavaScriptエラーメッセージヘッダ
     * @param string JavaScriptエラーメッセージフッタ
     */
     /*
    function setJsHeaderFooter($header='', $footer='')
    {
        $this->js_parameters['header'] = $header;
        $this->js_parameters['footer'] = $footer;
    }
    */

    /**
     * JavaScript入力チェックタグを取得
     *
     * @access public
     * @return string JavaScript入力チェックタグ
     */
     /*
    function getJs()
    {
        $func_name = $this->js_parameters['func_name'];
        if (!$func_name) {
            return '';
        }

        $all_message = $this->js_parameters['all_message'] ? 'true' : 'false';

        // 各チェックメソッド
        $funcs   = '';
        $checker = '';
        foreach (array_keys($this->elements) as $key) {
            $js = $this->elements[$key]->getJs($this->getAttribute('name'));
            if (count($js) == 2) {
              $funcs   .= '/* ' . $this->elements[$key]->getDisplayName() . ' check * /' . "\n" . $js[0] . ';' . "\n";
              $checker .= $js[1] . "\n";
            }
        }

        // 不要な部分（コメントなど）削除
        //$validation = preg_replace('/\/\*[^\/]*\*\//', '', $validation);
        //$validation = preg_replace('/^(\s*)\/\/(.*)$/m', '', $validation);
        //$validation = preg_replace('/^(\s+)(.*)$/m', '$2', $validation);

        $header = str_replace('"', '\\"', $this->js_parameters['header']);
        $footer = str_replace('"', '\\"', $this->js_parameters['footer']);

        return <<< JAVASCRIPT_CODE
<script type="text/javascript">
{$checker}
</script>
<script type="text/javascript">
var {$func_name}_submit = false;
function {$func_name}(form)
{
  if ({$func_name}_submit) {
    return false;
  }

  var errors = new SyL.Validation.Errors();
  errors.setCustomErrorCallback({$this->js_parameters['error_callback']});

{$funcs}

  if (errors.isError()) {
    errors.errorMessageHeader = "{$header}";
    errors.errorMessageHeader = "{$footer}";
    errors.setDisplayAllMessage({$all_message});
    errors.raiseErrorMessage();
    errors.focusElement(form);
    return false;
  } else {
    {$func_name}_submit = true;
    return true;
  }
}
</script>
JAVASCRIPT_CODE;
    }
    */

}
