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

/** 検証クラス */
require_once dirname(__FILE__) . '/../Validation/SyL_ValidationAbstract.php';

/**
 * フォーム要素クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_FormElementAbstract
{
    /**
     * フォーム要素名
     *
     * @var string
     */
    protected $name = '';
    /**
     * フォーム要素表示名
     *
     * @var string
     */
    protected $display_name = '';
    /**
     * フォーム要素の値
     *
     * @var mixed
     */
    protected $value = null;
    /**
     * フォーム要素HTMLタグの追加属性
     *
     * @var string
     */
    private $attributes = array();
    /**
     * フォーム要素部品配列
     * ※radio, select, checkboxのみ
     *
     * @var array
     */
    protected $options = array();
    /**
     * フォーム要素表示タイプ
     *
     * 0 - 入力
     * 1 - 表示のみ
     * 2 - hidden
     *
     * @var int
     */
    private $view_type = 0;
    /**
     * エラーメッセージ
     * ※エラーメッセージがnullの場合は、エラーが無い
     *
     * @var string
     */
    private $error_message = null;
    /**
     * 検証オブジェクト
     *
     * @var SyL_ValidationAbstract
     */
    private $validation = null;

    /**
     * フォーム要素IDの接頭辞
     *
     * @var string
     */
    protected $prefix_id = '';

    /**
     * カスタム検証クラス配置ディレクトリ
     *
     * @var array
     */
    private static $search_dir = array();
    /**
     * hidden要素のテンプレート
     *
     * @var string
     */
    const HIDDEN_TEMPLATE = '<input type="hidden" name="%s" value="%s" />';
    /**
     * span要素のテンプレート
     *
     * @var string
     */
    const SPAN_TEMPLATE = '<span id="%s">%s</span>';

    /**
     * コンストラクタ
     *
     * @param string フォーム要素HTMLタグ名
     * @param string フォーム要素表示名
     * @param array フォーム要素属性配列
     */
    public function __construct($name, $display_name, array $attributes=array())
    {
        $this->name         = $name;
        $this->display_name = $display_name;
        $this->attributes   = $attributes;
    }

    /**
     * 個別フォーム要素オブジェクトの取得
     *
     * @param string フォーム要素タイプ
     * @param string フォーム要素表示名
     * @param mixed リクエスト初期値
     * @param mixed フォーム要素の値（checkboxの場合は配列、それ以外はstring）
     * @param array フォーム要素の追加属性
     */
    public static function createInstance($type, $name, $display_name, $value=null, array $attributes=array())
    {
        // - は select-multiple も含むため
        $type = implode('', array_map('ucfirst', explode('-', $type)));
        $classname = 'SyL_FormElement' . $type;

        $load = false;
        foreach (self::$search_dir as $search_dir) {
            if (self::$search_dir && is_file($search_dir . "/{$classname}.php")) {
                include_once $search_dir . "/{$classname}.php";
                $load = true;
                break;
            }
        }
        if (!$load) {
            include_once $classname . '.php';
        }
        $element = new $classname($name, $display_name, $attributes);
        $element->setValue($value);
        return $element;
    }

    /**
     * カスタム検証クラス配置ディレクトリをセットする
     *
     * @param string カスタム検証クラス配置ディレクトリ
     */
    public static function addSearchDir($search_dir)
    {
        if (!in_array($search_dir, self::$search_dir)) {
            self::$search_dir[] = $search_dir;
        }
    }

    /**
     * 追加オプションの設定
     *
     * @param string フォーム要素部品名
     * @param array フォーム要素の部品配列（radio, select, checkboxの場合のみ）
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * 要素名を取得する
     *
     * @return string 要素名
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * フォーム要素表示名を取得する
     *
     * @return string 要素名
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * 要素値をセットする
     *
     * @param mixed 要素値
     * @throws SyL_InvalidParameterException パラメータが規定外の型の場合
     */
    public function setValue($value)
    {
        if (($value === null) || is_scalar($value)) {
            $this->value = $value;
        } else {
            throw new SyL_InvalidParameterException('invalid parameter format (' . gettype($value) . ')');
        }
    }

    /**
     * 要素値を取得する
     *
     * @return mixed 要素値
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 要素属性をセットする
     *
     * @param string 要素属性値
     * @param string 要素属性名
     */
    public function setAttribute($value, $name=null)
    {
        if ($name) {
            $name = strtolower($name);
            $this->attributes[$name] = $value;
        } else {
            $this->attributes[] = $value;
        }
    }

    /**
     * 要素属性を取得する
     *
     * @param string 要素属性名
     * @return string 要素属性値
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * 要素属性のHTML用文字列を取得する
     *
     * @return string 属性のHTML用文字列
     */
    public function getAttributeString()
    {
        $attributes = array();
        foreach ($this->attributes as $name => $value) {
            $attributes[] = is_int($name) ? self::encode($value) : ($name . '="' . self::encode($value) . '"');
        }
        return implode(' ', $attributes);
    }

    /**
     * 必須チェック存在判定
     * ※検証グループオブジェクトをセットしてから実行する
     *
     * @return bool true: 必須チェックあり、false: 必須チェック無し
     */
    public function isRequire()
    {
        return ($this->validation != null) ? $this->validation->isRequire() : false;
    }

    /**
     * フォーム要素が表示要素か判定する
     *
     * @param bool フォーム要素表示フラグ
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
     * フォーム要素が隠し要素か判定する
     *
     * @param bool フォーム要素隠しフラグ
     * @return bool true: 隠し要素、false: 隠し要素以外
     */
    public function isHidden($hidden=null)
    {
        if (is_bool($hidden)) {
            $this->view_type = $hidden ? '2' : '0';
        }
        return ($this->view_type == '2');
    }

    /**
     * フォーム要素HTML出力
     * 読み取り専用になっているかで、子クラスのメソッドを呼び分ける
     *
     * @param string フォーム名
     * @return string フォーム要素のHTML
     */
    public function getHtml($form_name='')
    {
        if ($form_name) {
            $this->prefix_id = $form_name . '_';
        }
        switch ($this->view_type) {
        case '1': return $this->getHtmlView();
        case '2': return $this->getHtmlHidden();
        default:  return $this->getHtmlTag();
        }
    }

    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @param string フォーム名
     * @return string フォーム要素のHTML
     */
    protected abstract function getHtmlTag();

    /**
     * フォーム要素HTML出力（表示）
     *
     * @param string フォーム名
     * @return string フォーム要素のHTML
     */
    protected abstract function getHtmlView();

    /**
     * フォーム要素をhiddenタグで取得する
     *
     * @return string フォーム要素のhiddenタグ
     */
    protected function getHtmlHidden()
    {
        return sprintf(self::HIDDEN_TEMPLATE, self::encode($this->name), self::encode($this->value));
    }

    /**
     * SPANタグを取得する
     *
     * @param string ID属性
     * @param string 表示テキスト
     * @return string SPANタグ
     */
    protected function getHtmlSpan($name, $text)
    {
        $id = $this->prefix_id . $name . '_s';
        return sprintf(self::SPAN_TEMPLATE, $id, $text);
    }

    /**
     * HTMLエンコードを行う
     *
     * @param string HTMLエンコード前文字列
     * @return string HTMLエンコード後文字列
     */
    protected static function encode($value)
    {
        return htmlspecialchars($value);
    }

    /**
     * 検証オブジェクトをセット
     *
     * @param SyL_ValidationAbstract 検証オブジェクト
     */
    public function setValidation(SyL_ValidationAbstract $validation)
    {
        $this->validation = clone $validation;
    }

    /**
     * 検証を実行する
     * 検証グループオブジェクトがない場合は、常にtrue
     *
     * @return bool true: エラー無し、false: エラーあり
     */
    public function validate()
    {
        if ($this->validation == null) {
            return true;
        }

        try {
            $this->validation->execute($this->value, $this->display_name);
            $this->error_message = null;
            return true;
        } catch (SyL_ValidationValidatorException $e) {
            $this->error_message = $e->getMessage();
            return false;
        }
    }

    /**
     * エラーを判定する
     *
     * @return bool true: エラーあり、false; エラー無し
     */
    public function isError()
    {
        return ($this->error_message != null);
    }

    /**
     * エラーメッセージを取得する
     *
     * @return string エラーメッセージ
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * エラーメッセージをセットする
     *
     * @param string エラーメッセージ
     */
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;
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
            $element = $this->getName();
            $func    = "check_{$formname}_{$element}(form, errors)";
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
