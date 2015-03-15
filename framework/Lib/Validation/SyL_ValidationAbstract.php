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

/** 検証関連の例外クラス */
require_once 'SyL_ValidationException.php';
/** 個別検証クラス */
require_once 'SyL_ValidationValidatorAbstract.php';
/** 個別検証グループクラス */
require_once 'SyL_ValidationValidators.php';

/**
 * 検証クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_ValidationAbstract
{
    /**
     * エラーメッセージ
     *
     * @var string
     */
    protected $error_message = '';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * エラーメッセージを取得する
     *
     * @param string 要素名
     * @return string エラーメッセージ
     */
    protected function getErrorMessage($name='')
    {
        if ($name) {
            return str_replace('{$name}', $name, $this->error_message);
        } else {
            return $this->error_message;
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
    public static function createValidator($type, $error_message, array $parameters=array())
    {
        return SyL_ValidationValidatorAbstract::createInstance($type, $error_message, $parameters);
    }

    /**
     * 個別検証グループオブジェクトの作成する
     *
     * @return SyL_ValidationValidatorAbstract 個別検証グループオブジェクト
     */
    public static function createValidators()
    {
        return new SyL_ValidationValidators();
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
    public abstract function execute($value, $name='');

    /**
     * 必須チェック存在判定
     *
     * @return bool true: 必須チェックあり、false: 必須チェック無し
     */
    public abstract function isRequire();

    /**
     * JavaScript処理ロジックを取得する
     *
     * @param string フォーム要素表示名
     * @param array フォーム要素の部品配列（radio, select, checkboxの場合のみ）
     * @return string JavaScript処理ロジック
     */
     /*
    public function getJs($display_name)
    {
        return '';
    }
    */
}
