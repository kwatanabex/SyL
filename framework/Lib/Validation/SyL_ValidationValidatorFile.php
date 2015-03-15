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
 * ファイルアップロード検証クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationValidatorFile extends SyL_ValidationValidator
{
    /**
     * パラメータ
     *
     * @access protected
     * @var array
     */
    var $parameters = array(
      'name_format'       => null,
      'name_format_error' => null,
      'min'               => 1, // サイズ
      'min_error'         => null,
      'max'               => null, // checkbox, select, fileの配列の最大チェック数
      'max_error'         => null,
      'mime_format'       => null, // mime typeを正規表現で検証
      'mime_format_error' => null,
      'is_upload'         => true, // is_uploaded_file関数で検証
      'is_upload_error'   => null
    );

    /**
     * 検証処理を実行する
     *
     * @access public
     * @param string 検証対象値
     * @param string 検証対象名
     * @return bool true: エラー無し, false: エラーあり
     */
    function execute($value, $name='')
    {
        if ($value === null) {
            // ファイルアップロード以外は、チェックしない
            return true;
        }

        // 必須チェック or ファイルチェック以外で、検証値が空の場合はtrue
        if (!$this->isRequire()) {
            if (!call_user_func(array(__CLASS__, 'executeImmediate'), 'requirefile', $value)) {
                return true;
            }
        }

        if (!$this->validate($value)) {
            $this->replaceErrorMessage($name);
            return false;
        } else {
            return true;
        }
    }

    /**
     * ファイルアップロード検証処理を実行する
     *
     * @access public
     * @param array 検証対象値
     * @return bool true: エラー無し, false: エラーあり
     */
    function validate($value)
    {
        sort($value['size'], SORT_NUMERIC);
        // サイズ最小値チェック
        if ($this->parameters['min'] !== null) {
            if ($value['size'][0] < $this->parameters['min']) {
                if ($this->parameters['min_error'] !== null) {
                    $this->error_message = $this->parameters['min_error'];
                }
                return false;
            }
        }
        // サイズ最大値チェック
        if ($this->parameters['max'] !== null) {
            if ($value['size'][count($value['size'])-1] > $this->parameters['max']) {
                if ($this->parameters['max_error'] !== null) {
                    $this->error_message = $this->parameters['max_error'];
                }
                return false;
            }
        }
        // ファイル名チェック
        if ($this->parameters['name_format'] !== null) {
            foreach ($value['name'] as $tmp) {
                if (!preg_match($this->parameters['name_format'], $tmp)) {
                    if ($this->parameters['name_format_error'] !== null) {
                        $this->error_message = $this->parameters['name_format_error'];
                    }
                    return false;
                }
            }
        }
        // MIMEタイプチェック
        if ($this->parameters['mime_format'] !== null) {
            foreach ($value['type'] as $tmp) {
                if (!preg_match($this->parameters['mime_format'], $tmp)) {
                    if ($this->parameters['mime_format_error'] !== null) {
                        $this->error_message = $this->parameters['mime_format_error'];
                    }
                    return false;
                }
            }
        }
        // POST送信チェック
        if ($this->parameters['is_upload'] !== null) {
            foreach ($value['tmp_name'] as $tmp) {
                if (!is_uploaded_file($tmp)) {
                    if ($this->parameters['is_upload_error'] !== null) {
                        $this->error_message = $this->parameters['is_upload_error'];
                    }
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * ファイル名検証処理のJavaScriptを取得する
     *
     * @access public
     * @return string JavaScript処理ロジック
     */
    function getJsCode()
    {
        $format = $this->parameters['name_format'];

        $js  = '';
        if ($format) {
            $error_message = $this->parameters['name_format_error'];
            if (!$error_message) {
                $error_message = $this->getErrorMessage();
            }

            $options = array();
            $options[] = "'format': {$format}";
            $options[] = "'min_valids': '{$this->min_valids}'";
            $options[] = "'max_valids': '{$this->max_valids}'";

            $js  = '';
            $js .= 'var message_tmp = validation.isRegex(name, "' . $error_message . '", {' . implode(',', $options) . '});' . "\n";
            $js .= 'if (message_tmp) {' . "\n";
            $js .= '  message = message_tmp;' . "\n";
            $js .= '}' . "\n";
        }
        return $js;
    }

}
