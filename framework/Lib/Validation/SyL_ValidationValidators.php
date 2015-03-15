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
 * 個別検証グループクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationValidators extends SyL_ValidationAbstract
{
    /**
     * 個別検証クラスをまとめた配列
     *
     * @var array
     */
    private $validators = array();

    /**
     * 個別検証オブジェクトを追加する
     *
     * @param SyL_ValidationValidator 個別検証オブジェクト
     */
    public function addValidator(SyL_ValidationValidatorAbstract $validator)
    {
        $this->validators[] = clone $validator;
    }

    /**
     * 必須チェック存在判定
     *
     * @return bool true: 必須チェックあり、false: 必須チェック無し
     */
    public function isRequire()
    {
        $require = false;
        foreach ($this->validators as &$validator) {
            if ($validator->isRequire()) {
                $require = true;
                break;
            }
        }
        return $require;
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
        foreach ($this->validators as &$validator) {
            $validator->execute($value, $name);
        }
    }

    /**
     * JavaScript処理ロジックを取得する
     *
     * @access public
     * @param string フォーム要素表示名
     * @param array フォーム要素の部品配列（radio, select, checkboxの場合のみ）
     * @return string JavaScript処理ロジック
     */
     /*
    function getJs($display_name)
    {
        $js = '';
        foreach (array_keys($this->validators) as $key) {
            $tmp = $this->validators[$key]->getJs($display_name);
            if ($tmp) {
                if ($this->validators[$key]->isRequire()) {
                    $js .= 'if (!message) {' . "\n";
                } else {
                    $js .= 'if (!message && !validation.isRequire(name, "require!")) {' . "\n";
                }
                $js .= $tmp . "\n";
                $js .= '}' . "\n";
            }
        }
        return $js;
    }
    */
}

/**
 * エイリアス
 */
//class SyL_Validators extends SyL_ValidationValidators{}
