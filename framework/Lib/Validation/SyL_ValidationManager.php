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

/** 検証クラス */
require_once 'SyL_ValidationAbstract.php';

/**
 * 検証プロセス管理クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationManager
{
    /**
     * 検証情報を含む配列
     *
     * @var array
     */
    private $validations = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * 検証オブジェクトを追加する
     *
     * @param SyL_ValidationAbstract 検証オブジェクト
     * @param string 検証項目値
     * @param string 検証項目名
     * @param string 検証項目表示名
     */
    public function add(SyL_ValidationAbstract $validator, $value, $name='', $display_name='')
    {
        if ($name == '') {
            $name = self::getDefaultElementName();
        }

        $this->validations[$name] = array(
            'display_name'  => $display_name,
            'value'         => $value,
            'error_massage' => null,
            'validator'     => clone $validator
        );
    }

    /**
     * 検証外エラー情報を追加する
     *
     * @param string エラーメッセージ
     * @param string 検証項目名
     */
    public function addErrorMessage($error_massage, $name='')
    {
        if ($name == '') {
            $name = self::getDefaultElementName();
        }
        $this->validations[$name] = array(
          'display_name'  => null,
          'value'         => null,
          'validator'     => null,
          'error_massage' => $error_massage
        );
    }

    /**
     * デフォルトの要素名を取得する
     *
     * @return string 検証項目名
     */
    private static function getDefaultElementName()
    {
        static $i = 1;
        return __CLASS__ . '.internal.' . $i++;
    }

    /**
     * 検証処理を実行する
     *
     * @return bool true: エラー無し, false: エラーあり
     */
    public function execute()
    {
        $valid = true;
        foreach ($this->validations as &$validation) {
            if ($validation['validator'] != null) {
                try {
                    $validation['validator']->execute($validation['value'], $validation['display_name']);
                } catch (SyL_ValidationValidatorException $e) {
                    $validation['error_massage'] = $e->getMessage();
                    $valid = false;
                }
            }
        }
        return $valid;
    }

    /**
     * 検証設定オブジェクトを元に検証を実行する
     *
     * @param SyL_ValidationConfigAbstract 検証設定オブジェクト
     * @param array 値取得元連想配列
     * @return bool true: エラー無し, false: エラーあり
     */
    public function executeConfig(SyL_ValidationConfigAbstract $config, array $resources)
    {
        foreach ($config->getNames() as $name => $display_name) {
            $validators   = $config->getValidators($name);
            $value = isset($resources[$name]) ? $resources[$name] : null;
            $this->add($validators, $value, $name, $display_name);
        }
        return $this->execute();
    }

    /**
     * 検証要素にエラーがあるか判定する
     *
     * @param string 検証項目名
     * @return bool true: エラーあり、false: エラーなし
     */
    public function isError($name)
    {
        return isset($this->validations[$name]) && ($this->validations[$name]['error_massage'] != null);
    }

    /**
     * 検証要素のエラーメッセージを取得する
     *
     * @param string 検証項目名
     * @return string エラーメッセージ
     */
    public function getErrorMessage($name)
    {
        return isset($this->validations[$name]) ? $this->validations[$name]['error_massage'] : null;
    }

    /**
     * 全エラーメッセージを取得する
     *
     * @return array エラーメッセージ配列
     */
    public function getErrorMessages()
    {
        $error_messages = array();
        foreach ($this->validations as &$validation) {
            if ($validation['error_massage'] != null) {
                $error_messages[] = $validation['error_massage'];
            }
        }
        return $error_messages;
    }
}
