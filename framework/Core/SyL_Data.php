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
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/SyL_Property.php';

/**
 * データクラス
 *
 * フィルタ、アクションではメソッドの引数で指定され、随時パラメータを取得／セット可能。
 * ビューのパラメータ取得メソッドでは、このオブジェクトのパラメータを取得する。
 *
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
class SyL_Data implements SyL_ContainerComponentInterface
{
    /**
     * 入力パラメータ
     * 
     * @var SyL_Property
     */
    private $inputs = null;
    /**
     * 出力パラメータ
     * 
     * @var SyL_Property
     */
    private $outputs = null;
    /**
     * 配列パラメータをgetメソッドで取得できるフラグ
     *
     * @var bool
     */
    protected $input_get_array = false;
    /**
     * パラメータを入力から出力に透過的に渡すフラグ
     *
     * @var bool
     */
    protected $transparent = false;

    /**
     * コンストラクタ
     *
     * @param SyL_Property 汎用プロパティオブジェクト
     */
    protected function __construct(SyL_Property $inputs)
    {
        $this->inputs  = $inputs;
        $this->outputs = new SyL_Property();
    }

    /**
     * データ保持クラスのインスタンス取得する
     *
     * @param SyL_RequestAbstract リクエストオブジェクト
     * @return SyL_Data データ保持オブジェクト
     */
    public static function createInstance(SyL_RequestAbstract $request)
    {
        $classname = '';
        $name = SyL_CustomClass::getDataClass();
        if ($name) {
            $classname = SyL_Loader::userLib($name);
            if (!is_subclass_of($classname, __CLASS__)) {
                throw new SyL_InvalidClassException("invalid component class `{$classname}'. not extends `" . __CLASS__ . "' class");
            }
        } else {
            $classname = __CLASS__;
            include_once $classname . '.php';
        }

        return new $classname($request->getParameters());
    }

    /**
     * パラメータを取得する
     *
     * パラメータが配列の場合は、nullを取得する。
     * 配列のすべての要素を取得するには、geta メソッドを使用する。
     * 
     * @param string パラメータ名
     * @param mixed デフォルト値
     * @param string 有効値正規表現
     * @return string パラメータ値
     * @throws SyL_InvalidParameterException 有効値正規表現が一致しない場合
     */
    public function get($name, $default=null, $valid=null)
    {
        $value = null;
        if ($this->outputs->is($name)) {
            $value = $this->outputs->get($name);
        } else {
            $value = $this->inputs->get($name);
            // 入力パラメータの配列は、getaメソッドで明示的に取得
            if (is_array($value) && !$this->input_get_array) {
                $value = null;
            }
        }
        if ($valid !== null) {
            if (!preg_match($valid, $value)) {
                throw new SyL_InvalidParameterException("invalid parameter, no match `preg_match' ({$value})");
            }
        }
        return ($value !== null) ? $value : $default;
    }

    /**
     * パラメータを取得する（配列用）
     * 
     * 配列以外のパラメータは、null となる。
     *
     * @param string パラメータ名
     * @param string 配列インデックス
     * @return array パラメータ値
     */
    public function geta($name, $index=null)
    {
        $value = null;
        if ($this->outputs->is($name)) {
            $value = $this->outputs->get($name);
        } else {
            $value = $this->inputs->get($name);
        }

        if ($index !== null) {
            if (is_array($value)) {
                if (isset($value[$index])) {
                    return $value[$index];
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            if (is_array($value)) {
                return $value;
            } else {
                return array();
            }
        }
    }

    /**
     * ビュー出力パラメータをセットする
     *
     * @param string パラメータ名
     * @param mixed パラメータ値
     */
    public function set($name, $value)
    {
        $this->outputs->set($name, $value);
    }

    /**
     * パラメータを確認する
     *
     * @param string パラメータ名
     * @return string パラメータ値
     */
    public function is($name)
    {
        return $this->inputs->is($name) || $this->outputs->is($name);
    }

    /**
     * セッションパラメータを取得する
     *
     * @param string パラメータ名
     * @return string パラメータ値
     */
    public function getSession($name)
    {
        if (class_exists('SyL_SessionAbstract')) {
            return SyL_SessionAbstract::getInstance()->get($name);
        } else {
            throw new SyL_ClassNotFoundException('SyL_SessionAbstract class not loaded');
        }
    }

    /**
     * セッションパラメータを取得する
     *
     * @param string パラメータ名
     * @param mixed パラメータ値
     */
    public function setSession($name, $value)
    {
        if (class_exists('SyL_SessionAbstract')) {
            return SyL_SessionAbstract::getInstance()->set($name, $value);
        } else {
            throw new SyL_ClassNotFoundException('SyL_SessionAbstract class not loaded');
        }
    }

    /**
     * セッションパラメータを確認する
     *
     * @param string パラメータ名
     * @return bool セッションパラメータの確認
     */
    public function isSession($name)
    {
        if (class_exists('SyL_SessionAbstract')) {
            return SyL_SessionAbstract::getInstance()->is($name);
        } else {
            throw new SyL_ClassNotFoundException('SyL_SessionAbstract class not loaded');
        }
    }

    /**
     * パラメータの数を取得する
     * 
     * @return int パラメータの数
     */
    public function getLength()
    {
        return $this->inputs->getLength();
    }

    /**
     * 全パラメータに指定関数を適用する
     * 
     * @param string 関数名
     * @param mixed パラメータ
     * @param mixed ...
     */
    public function apply($func)
    {
        $func_args = func_get_args();
        $func = array_shift($func_args);
        $this->applyArray($func, $func_args);
    }

    /**
     * 全パラメータに指定関数を適用する（パラメータ配列ver.）
     * 
     * @param string 関数名
     * @param array パラメータ配列
     */
    public function applyArray($func, $func_args=array())
    {
        $this->inputs->applyArray($func, $func_args);
        $this->outputs->applyArray($func, $func_args);
    }

    /**
     * 出力プロパティオブジェクトを取得する
     *
     * @return SyL_Property 出力プロパティオブジェクト
     */
    public function outputData()
    {
        if ($this->transparent) {
            foreach ($this->inputs->gets() as $name => $value) {
                if (!$this->outputs->is($name)) {
                    $this->outputs->set($name, $value);
                }
            }
        }
        return $this->outputs;
    }
}
