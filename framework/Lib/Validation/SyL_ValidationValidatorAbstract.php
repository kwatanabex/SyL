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
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 個別検証クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_ValidationValidatorAbstract extends SyL_ValidationAbstract
{
    /**
     * 検証パラメータ
     *
     * @var array
     */
    protected $parameters = array();
    /**
     * カスタム検証クラス配置ディレクトリ
     *
     * @var array
     */
    private static $search_dir = array();
    /**
     * 検証直前に行う処理
     *
     * @var mixed
     */
    private static $pre_execute_callback = null;

    /**
     * コンストラクタ
     *
     * @param string エラーメッセージのフォーマット
     * @param array 検証パラメータ
     */
    public function __construct($error_message, array $parameters=array())
    {
        parent::__construct();

        $this->error_message = $error_message;
        $this->parameters    = array_merge($this->parameters, $parameters);

        if (!isset($this->parameters['min_valids'])) {
            $this->parameters['min_valids'] = 0; // 0 は配列内全検証数
        }
        if (!isset($this->parameters['max_valids'])) {
            $this->parameters['max_valids'] = 0; // 0 は配列内全検証数
        }
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
     * 検証直前に行う処理を登録する
     *
     * @param mixed コールバックメソッド
     * @param array コールバックメソッドのパラメータ
     */
    public static function registerPreExecuteCallback($callback, array $parameters=array())
    {
        self::$pre_execute_callback = array($callback, $parameters);
    }

    /**
     * 検証直前に処理を行う
     *
     * @param mixed 検証値
     * @return mixed 処理後の検証値
     */
    private static function preFilter($value)
    {
        if (self::$pre_execute_callback) {
            return call_user_func_array(self::$pre_execute_callback[0], array_merge((array)$value, self::$pre_execute_callback[1]));
        } else {
            return $value;
        }
    }

    /**
     * 個別検証オブジェクトの作成する
     *
     * @param string 検証タイプ
     * @param string エラーメッセージ
     * @param array 検証パラメータ
     * @return SyL_ValidationValidatorAbstract 個別検証オブジェクト
     */
    public static function createInstance($type, $error_message, array $parameters=array())
    {
        $classname = 'SyL_ValidationValidator' . ucfirst($type);

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
        return new $classname($error_message, $parameters);
    }

    /**
     * 即時検証処理を実行する
     *
     * @param string 検証タイプ
     * @param string 検証対象値
     * @param array 検証パラメータ
     * @return bool true: エラー無し, false: エラーあり
     */
    public static function executeImmediate($type, $value, array $parameters=array())
    {
        self::createInstance($type, '[' . __CLASS__ . '->executeImmediate() method error]', $parameters)->execute($value);
    }

    /**
     * 必須チェック存在判定
     *
     * @return bool true: 必須チェックあり、false: 必須チェック無し
     */
    public function isRequire()
    {
        return ($this instanceof SyL_ValidationValidatorRequire);
    }

    /**
     * 検証処理を実行する
     *
     * @param mixed 検証対象値
     * @param string 検証対象名
     * @throws SyL_ValidationValidatorException 検証エラーの場合
     * @throws SyL_ValidationValidatorMinValidsException 指定検証項目数（min_valids）に満たない場合
     * @throws SyL_ValidationValidatorMaxValidsException 指定検証項目数（max_valids）を超えた場合
     */
    public function execute($value, $name='')
    {
        if (is_array($value)) {
            if (count($value) == 0) {
                if ($this->isRequire()) {
                    throw new SyL_ValidationValidatorException($this->getErrorMessage($name));
                } else {
                    return;
                }
            } else {
                $ok = 0;
                foreach ($value as $tmp) {
                    try {
                        $this->execute($tmp, $name);
                        $ok++;
                    } catch (SyL_ValidationValidatorException $e) {
                    }
                }
                if ($this->parameters['max_valids'] == 0) {
                    $this->parameters['max_valids'] = count($value);
                    if ($this->parameters['min_valids'] == 0) {
                        $this->parameters['min_valids'] = count($value);
                    }
                }

                if ($this->parameters['min_valids'] > $ok) {
                    throw new SyL_ValidationValidatorMinValidsException($this->getErrorMessage($name));
                } else if ($this->parameters['max_valids'] < $ok) {
                    throw new SyL_ValidationValidatorMaxValidsException($this->getErrorMessage($name));
                }
            }
        } else {
            // プレフィルタ
            $value = self::preFilter($value);

            // 必須チェック or ファイルチェック以外で、検証値が空の場合はtrue
            if (!$this->isRequire()) {
                if (($value === null) || ($value === '')) {
                    return;
                }
            }

            if (!$this->validate($value)) {
                throw new SyL_ValidationValidatorException($this->getErrorMessage($name));
            }
        }
    }

    /**
     * 検証処理を実行する
     *
     * @param mixed 検証対象値
     * @return bool true: エラー無し, false: エラーあり
     */
    protected abstract function validate($value);

    /**
     * エラーメッセージを取得する
     *
     * @param string 要素名
     * @return string エラーメッセージ
     */
    protected function getErrorMessage($name='')
    {
        foreach ($this->parameters as $key => $value) {
            if (is_scalar($value)) {
                $this->error_message = str_replace('{$' . $key . '}', $value, $this->error_message);
            }
        }

        return parent::getErrorMessage($name);
    }

    /**
     * 検証処理のJavaScriptを取得する
     *
     * @param string フォーム要素表示名
     * @param array フォーム要素の部品配列（radio, select, checkboxの場合のみ）
     * @return string JavaScript処理ロジック
     */
     /*
    public function getJs($display_name)
    {
        $this->replaceErrorMessage($display_name);
        return $this->getJsCode();
    }
    */

    /**
     * 検証処理のJavaScriptを取得する
     *
     * @access public
     * @param array フォーム要素の部品配列（radio, select, checkboxの場合のみ）
     * @return string JavaScript処理ロジック
     */
     /*
    function getJsCode()
    {
        return '';
    }
    */
}

/**
 * エイリアス
 */
//class SyL_Validator extends SyL_ValidationValidator{}
