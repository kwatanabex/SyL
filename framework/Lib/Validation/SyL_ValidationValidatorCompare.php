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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 文字列比較検証クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationValidatorCompare extends SyL_ValidationValidatorAbstract
{
    /**
     * 検証パラメータ
     *
     * @var array
     */
    protected $parameters = array(
      'comp'   => '==',
      'value'  => null,
      //'comp_object' => null
    );

    /**
     * コンストラクタ
     *
     * @access public
     * @param string バリデータタイプ
     * @param string エラーメッセージのフォーマット
     * @param array 検証パラメータ
     */
/*
    function SyL_ValidationValidatorCompare($type, $error_message, $parameters=array())
    {
        parent::SyL_ValidationValidator($type, $error_message, $parameters);

        if (is_object($this->parameters['comp_object']) && is_subclass_of($this->parameters['comp_object'], 'SyL_FormElement')) {
            $this->parameters['comp_value'] = $this->parameters['comp_object']->getValue();
        }
    }
*/
    /**
     * 検証処理を実行する
     *
     * @param mixed 検証対象値
     * @return bool true: エラー無し, false: エラーあり
     */
    protected function validate($value)
    {
        $compare = $this->parameters['comp'];
        $target  = $this->parameters['value'];

        switch ($compare) {
        case '===': return ($value === $target);
        case '==':  return ($value == $target);
        case '!==': return ($value !== $target);
        case '!=':  return ($value != $target);
        case '<=':  return (strcmp($value, $target) <= 0);
        case '>=':  return (strcmp($value, $target) >= 0);
        case '<':   return (strcmp($value, $target) < 0);
        case '>':   return (strcmp($value, $target) > 0);
        default:    return false;
        }
    }

    /**
     * 比較検証処理のJavaScriptを取得する
     *
     * @access public
     * @return string JavaScript処理ロジック
     */
     /*
    function getJsCode()
    {
        $options = array();
        $options[] = "'compare': '{$this->parameters['comp']}'";
        $options[] = "'min_valids': '{$this->min_valids}'";
        $options[] = "'max_valids': '{$this->max_valids}'";
        if (is_object($this->parameters['comp_object']) && is_subclass_of($this->parameters['comp_object'], 'SyL_FormElement')) {
            $name = $this->parameters['comp_object']->getNames();
            $options[] = "'element': '{$name}'";
        } else {
            $options[] = "'value': '{$this->parameters['comp_value']}'";
        }

        $js  = '';
        $js .= 'var message_tmp = validation.isCompare(name, "' . $this->getErrorMessage() . '", {' . implode(',', $options) . '});' . "\n";
        $js .= 'if (message_tmp) {' . "\n";
        $js .= '  message = message_tmp;' . "\n";
        $js .= '}' . "\n";

        return $js;
    }
    */
}
