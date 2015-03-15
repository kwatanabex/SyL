<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ビュークラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ViewAbstract implements SyL_ContainerComponentInterface
{
    /**
     * コンテンツタイプ
     * 
     * @var string
     */
    private $content_type = '';
    /**
     * 表示内容のデータ
     * 
     * @var string
     */
    private $render = '';
    /**
     * フレームワーク情報管理クラス
     * 
     * @var SyL_ContextAbstract
     */
    protected $context = null;
    /**
     * データオブジェクト
     * 
     * @var SyL_Data
     */
    private $data = null;
    /**
     * デフォルトHTMLエンコードフラグ
     * 
     * @var bool
     */
    private static $default_html_encode = false;

    /**
     * コンストラクタ
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $this->context = $context;
        $this->data    = $data->outputData();
    }

    /**
     * ビューオブジェクトを取得
     * 
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     * @return SyL_ViewAbstract ビューオブジェクト
     */
    public static function createInstance(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $name = '';
        if ($context->getRouter()->enableTemplate()) {
            $name = $context->getRouter()->getViewClass();
        } else {
            $name = 'core:View.Null@SyL';
        }

        $classname = '';
        try {
            $classname = SyL_Loader::userLib($name);
        } catch (SyL_FileNotFoundException $e) {
            $classname = SyL_Loader::core($name);
        }
        SyL_Logger::debug('view info: class="' . $classname . '"');

        return new $classname($context, $data);
    }

    /**
     * デフォルトHTMLエンコードフラグをセット
     *
     * @param bool デフォルトHTMLエンコードフラグ
     */
    public static function setDefaultHtmlEncode($default_html_encode)
    {
        self::$default_html_encode = $default_html_encode;
    }

    /**
     * テンプレートディレクトリルートを取得
     *
     * @return string テンプレートディレクトリルート
     */
    public function getTemplateDir()
    {
        return $this->context->getRouter()->getTemplateDir();
    }

    /**
     * テンプレートファイルを取得
     *
     * @return string テンプレートファイル
     */
    public function getTemplateFile()
    {
        return $this->context->getRouter()->getTemplateFile();
    }

    /**
     * コンテンツタイプをセット
     * 
     * @param string コンテンツタイプ
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    /**
     * コンテンツタイプを取得
     * 
     * @return string コンテンツタイプ
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * 表示内容をセット
     * 
     * @param string 表示内容
     */
    public function setRender($render)
    {
        $this->render = $render;
    }

    /**
     * 表示内容を取得
     * 
     * @return string 表示内容
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     * パラメータを取得
     *
     * 主にアクションから渡されたパラメータをテンプレート内で使用する際に使用
     * 
     * @param string パラメータ名
     * @param mixed デフォルト値
     * @return mixed パラメータ値
     */
    public function get($name, $default=null)
    {
        return $this->data->get($name, $default);
    }

    /**
     * パラメータを出力する
     * 
     * @param string パラメータ名
     * @param bool HTMLエンコードフラグ
     * @param mixed デフォルト値
     */
    public function out($name, $encode=null, $default=null)
    {
        if (!is_bool($encode)) {
            $encode = self::$default_html_encode;
        }
        $value = $this->data->get($name, $default);
        echo $encode ? htmlentities($value, ENT_QUOTES, SYL_ENCODE_INTERNAL) : $value;
    }

    /**
     * 条件付きでパラメータを出力する
     * 
     * @param string パラメータ名
     * @param string 出力フォーマット
     * @param bool HTMLエンコードフラグ
     * @param mixed デフォルト値
     */
    public function outIfExist($name, $format, $encode=null, $default=null)
    {
        $value = $this->data->get($name, $default);
        if (($value === null) || ($value === '')) {
            return;
        }

        if (!is_bool($encode)) {
            $encode = self::$default_html_encode;
        }
        if ($encode) {
            $value = htmlentities($value, ENT_QUOTES, SYL_ENCODE_INTERNAL);
        }
        echo sprintf($format, $value);
    }

    /**
     * 条件付きで値を出力する
     * 
     * @param bool 出力条件
     * @param string 出力条件がtrueの場合に、出力する文字列
     * @param string 出力条件がfalseの場合に、出力する文字列
     */
    public function echoIf($condition, $true_value, $false_value=null, $encode=null)
    {
        if (!is_bool($encode)) {
            $encode = self::$default_html_encode;
        }

        $value = $condition ? $true_value : $false_value;
        echo $encode ? htmlentities($value, ENT_QUOTES, SYL_ENCODE_INTERNAL) : $value;
    }

    /**
     * 条件付きで値を出力する
     * 
     * @param string 出力文字列
     * @param string 出力フォーマット
     * @param bool HTMLエンコードフラグ
     */
    public function echoIfExist($value, $format, $encode=null)
    {
        if (($value === null) || ($value === '')) {
            return;
        }

        if (!is_bool($encode)) {
            $encode = self::$default_html_encode;
        }
        if ($encode) {
            $value = htmlentities($value, ENT_QUOTES, SYL_ENCODE_INTERNAL);
        }

        echo sprintf($format, $value);
    }

    /**
     * 文字列をフォーマットして出力する
     * 
     * @param string 文字列フォーマット
     * @param array フォーマットパラメータ
     * @param bool HTMLエンコードフラグ
     */
    public function echoFormat($format, array $values, $encode=null)
    {
        if (!is_bool($encode)) {
            $encode = self::$default_html_encode;
        }
        
        if ($encode) {
            foreach ($values as &$value) {
                $value = htmlentities($value, ENT_QUOTES, SYL_ENCODE_INTERNAL);
            }
        }
        echo vsprintf($format, $values);
    }

    /**
     * 表示レンダリング
     *
     * テンプレートを出力バッファリングして内部で保持。
     * 以後、SyL_Response クラスで出力される。
     */
    public function render()
    {
        SyL_Logger::trace("view render parameters: " . print_r($this->data->gets(), true));

        ob_start();
        try {
            $this->renderDisplay();
        } catch (SyL_RouterNotFoundException $e) {
            // テンプレートファイルが見つからない場合
            // この段階でテンプレートファイルが見つからない場合は、router の enableAction を off にしたとき
            throw new SyL_ResponseNotFoundException(get_class($e) . ': ' . $e->getMessage());
        }
        $this->render = ob_get_clean();
    }

    /**
     * 表示レンダリング実行
     */
    protected abstract function renderDisplay();

}
