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
 * 数値範囲検証クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationValidatorNumeric extends SyL_ValidationValidatorAbstract
{
    /**
     * 検証パラメータ
     *
     * @var array
     */
    protected $parameters = array(
      'dot'               => true,
      'min'               => null,
      'min_error_message' => null,
      'max'               => null,
      'max_error_message' => null
    );

    /**
     * 検証処理を実行する
     *
     * @param mixed 検証対象値
     * @return bool true: エラー無し, false: エラーあり
     */
    protected function validate($value)
    {
        $regex = '';
        if ($this->parameters['dot']) {
            $regex = '/^[\-\+]?[0-9]+(\.[0-9]+)?$/';
        } else {
            $regex = '/^[\-\+]?[0-9]+$/';
        }
        if (!preg_match($regex, $value)) {
            return false;
        }

        $value = floatval($value);

        // 最小値検証
        if ($this->parameters['min'] !== null) {
            if ((float)$value < (float)$this->parameters['min']) {
                if ($this->parameters['min_error_message'] !== null) {
                    $this->error_message = $this->parameters['min_error_message'];
                }
                return false;
            }
        }

        // 最大値検証
        if ($this->parameters['max'] !== null) {
            if ((float)$value > (float)$this->parameters['max']) {
                if ($this->parameters['max_error_message'] !== null) {
                    $this->error_message = $this->parameters['max_error_message'];
                }
                return false;
            }
        }

        return true;
    }

    /**
     * 数値検証処理のJavaScriptを取得する
     *
     * @access public
     * @return string JavaScript処理ロジック
     */
     /*
    function getJsCode()
    {
        $min    = $this->parameters['min'];
        $max    = $this->parameters['max'];
        $min_error_message = $this->parameters['min_error_message'];
        $max_error_message = $this->parameters['max_error_message'];

        $options = array();
        $options[] = "'dot': " . ($this->parameters['dot'] ? 'true' : 'false');
        $options[] = "'min': '{$min}'";
        $options[] = "'max': '{$max}'";
        $options[] = "'min_error_message': '{$min_error_message}'";
        $options[] = "'max_error_message': '{$max_error_message}'";
        $options[] = "'min_valids': '{$this->min_valids}'";
        $options[] = "'max_valids': '{$this->max_valids}'";

        $js  = '';
        $js .= 'var message_tmp = validation.isNumeric(name, "' . $this->getErrorMessage() . '", {' . implode(',', $options) . '});' . "\n";
        $js .= 'if (message_tmp) {' . "\n";
        $js .= '  message = message_tmp;' . "\n";
        $js .= '}' . "\n";
        return $js;
    }
    */
}
