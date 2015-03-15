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
 * 日付検証クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationValidatorDate extends SyL_ValidationValidatorAbstract
{
    /**
     * 検証パラメータ
     *
     * @var array
     */
    protected $parameters = array(
      'min'       => null, // strtotimeフォーマット
      'min_error_message' => null,
      'max'       => null, // strtotimeフォーマット
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
        $yyyy = $mm = $dd = null;
        $hh   = $mi = $ss = null;
        if (preg_match('/^([0-9]{4})[\-\/]?([0-1][0-9])[\-\/]?([0-9]{2})\s?([0-1][0-9]|2[0-3]):?([0-5][0-9]):?([0-5][0-9])$/', $value, $matches)) {
          $yyyy = $matches[1];
          $mm   = $matches[2];
          $dd   = $matches[3];
          $hh   = $matches[4];
          $mi   = $matches[5];
          $ss   = $matches[6];
        } else if (preg_match('/^([0-9]{4})[\-\/]?([0-1][0-9])[\-\/]?([0-9]{2})$/', $value, $matches)) {
          $yyyy = $matches[1];
          $mm   = $matches[2];
          $dd   = $matches[3];
        } else {
          return false;
        }

        if (!checkdate($mm, $dd, $yyyy)) {
            return false;
        }

        $timestamp = null;
        if (($hh !== null) && ($mi !== null) && ($ss !== null)) {
            $timestamp = mktime($hh, $mi, $ss, $mm, $dd, $yyyy);
        } else {
            $timestamp = mktime(0, 0, 0, $mm, $dd, $yyyy);
        }

        if ($this->parameters['min']) {
            if (strtotime($this->parameters['min']) > $timestamp) {
                if ($this->parameters['min_error_message']) {
                    $this->error_message = $this->parameters['min_error_message'];
                }
                return false;
            }
        }

        if ($this->parameters['max']) {
            if (strtotime($this->parameters['max']) < $timestamp) {
                if ($this->parameters['max_error_message']) {
                    $this->error_message = $this->parameters['max_error_message'];
                }
                return false;
            }
        }

        return true;
    }

    /**
     * 日付検証処理のJavaScriptを取得する
     *
     * @access public
     * @return string JavaScript処理ロジック
     */
     /*
    function getJsCode()
    {
        $date   = date('Y-m-d');
        $past   = ($this->parameters['past'])   ? 'true' : 'false';
        $future = ($this->parameters['future']) ? 'true' : 'false';
        $min    = ($this->parameters['min']) ? date('Y-m-d', strtotime($this->parameters['min'])) : '';
        $max    = ($this->parameters['max']) ? date('Y-m-d', strtotime($this->parameters['max'])) : '';
        $min_error_message = $this->parameters['min_error_message'];
        $max_error_message = $this->parameters['max_error_message'];

        $options = array();
        $options[] = "'date': '{$date}'";
        $options[] = "'past': {$past}";
        $options[] = "'future': {$future}";
        $options[] = "'min': '{$min}'";
        $options[] = "'max': '{$max}'";
        $options[] = "'min_error_message': '{$min_error_message}'";
        $options[] = "'max_error_message': '{$max_error_message}'";
        $options[] = "'min_valids': '{$this->min_valids}'";
        $options[] = "'max_valids': '{$this->max_valids}'";

        $js  = '';
        $js .= 'var message_tmp = validation.isDate(name, "' . $this->getErrorMessage() . '", {' . implode(',', $options) . '});' . "\n";
        $js .= 'if (message_tmp) {' . "\n";
        $js .= '  message = message_tmp;' . "\n";
        $js .= '}' . "\n";
        return $js;
    }
    */
}
