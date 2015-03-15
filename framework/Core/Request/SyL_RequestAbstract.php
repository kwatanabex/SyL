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
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/SyL_Property.php';

/**
 * リクエスト抽象クラス
 *
 * インスタンス作成時に、実行環境ごとの外部パラメータを取得し保持する。
 * 外部パラメータで取得した値を、直接ビューで使用することはできない。
 * ビューで使用するには、事前に明示的にセットする。
 *
 * 外部パラメータとして配列が指定された場合、意図しない型であるのを防ぐため、getaメソッドで明示的に取得する。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_RequestAbstract implements SyL_ContainerComponentInterface
{
    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * リクエストオブジェクトを取得する
     *
     * SyLフレームワーク実行環境毎のリクエストオブジェクトを作成する。
     * シングルトンとして取得。
     *
     * @return SyL_RequestAbstract リクエストオブジェクト
     */
    public static function getInstance()
    {
        static $singleton = null;
        if ($singleton == null) {
            $name = SyL_CustomClass::getRequestClass();
            if ($name) {
                $classname = SyL_Loader::userLib($name);
            } else {
                $classname = 'SyL_Request' . SYL_APP_TYPE;
                include_once $classname . '.php';
            }
            if (!is_subclass_of($classname, __CLASS__)) {
                throw new SyL_InvalidClassException("invalid component class `{$classname}'. not extends `" . __CLASS__ . "' class");
            }
            $singleton = new $classname();
        }
        return $singleton;
    }

    /**
     * 入力パラメータを取得する
     *
     * @return SyL_Property 入力パラメータ
     */
    public function getParameters()
    {
        $parameters = $this->getInputs();
        self::applyCheckEncoding($parameters, SYL_ENCODE_INTERNAL);

        $property = new SyL_Property();
        $property->sets($parameters);
        if (get_magic_quotes_gpc()) {
            $property->apply('stripslashes');
        }
        SyL_Logger::trace("request parameters: " . print_r($property->gets(), true));
        return $property;
    }

    /**
     * 外部パラメータを取得する
     *
     * @return array 外部パラメータ
     */
    protected abstract function getInputs();

    /**
     * 全パラメータに文字コードチェック関数を適用する
     * 
     * @param array パラメータ
     * @param string 文字コード
     * @throws SyL_InvalidParameterException 指定した文字コードと一致しない場合
     */
    private static function applyCheckEncoding(&$parameters, $encoding)
    {
        if (is_array($parameters)) {
            foreach($parameters as $name => $value) {
                self::applyCheckEncoding($parameters[$name], $encoding);
            }
        } else {
            if (is_scalar($parameters)) {
                if (!mb_check_encoding($parameters, $encoding)) {
                    throw new SyL_InvalidParameterException("invalid parameter encoding: valid encoding {$encoding} ({$parameters})");
                }
            }
        }
    }

    /**
     * サーバ環境変数値を取得
     *
     * @param string サーバ環境変数名
     * @return string サーバ環境変数値
     */
    public function getServerVar($name)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    /**
     * 環境変数値を取得
     *
     * @param string 環境変数名
     * @return string 環境変数値
     */
    public function getEnvVar($name)
    {
        return isset($_ENV[$name]) ? $_ENV[$name] : null;
    }
}
