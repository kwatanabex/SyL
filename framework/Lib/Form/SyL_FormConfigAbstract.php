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
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * フォーム要素設定クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_FormConfigAbstract
{
    /**
     * ファイル名
     *
     * @var string
     */
    protected $filename = '';
    /**
     * フォーム設定配列
     *
     * @var array
     */
    protected $config = array();
    /**
     * フォーム名
     *
     * @var string
     */
    protected $form_name = array();
    /**
     * カスタムバリデーションディレクトリ
     *
     * @var string
     */
    protected $custom_validator_dir = '';

    /**
     * コンストラクタ
     *
     * @param string ファイル名
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * フォーム設定オブジェクトを作成する
     *
     * @param string 設定ファイル名
     * @param string 設定ファイルの拡張子
     * @return SyL_FormConfigAbstract フォーム設定オブジェクト
     * @throws SyL_FileNotFoundException ファイルが存在しない場合
     * @throws SyL_PermissionDeniedException ファイルの読み込み権限が無い場合
     */
    public static function createInstance($filename, $ext=null)
    {
        if (!file_exists($filename)) {
            throw new SyL_FileNotFoundException("file not found ({$filename})");
        }
        if (!is_readable($filename)) {
            throw new SyL_PermissionDeniedException("read permission denied ({$filename})");
        }

        if (!$ext) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
        }

        $classname = 'SyL_FormConfig' . ucfirst($ext);
        include_once $classname . '.php';
        $config = new $classname($filename);
        $config->parse();
        return $config;
    }

    /**
     * フォーム設定リソースをパースし取得する
     */
    public abstract function parse();

    /**
     * 設定で定義されたフォーム要素オブジェクトの配列を取得する
     *
     * @param bool デフォルト値使用フラグ
     * @return array フォーム要素オブジェクトの配列
     */
    public function getElements($default=false)
    {
        if ($this->custom_validator_dir) {
            include_once dirname(__FILE__) . '/../Util/SyL_UtilReplaceConstant.php';
            SyL_ValidationValidatorAbstract::addSearchDir(SyL_UtilReplaceConstant::replace($this->custom_validator_dir));
        }
        return $this->getElementsRecursive($this->config, $default);
    }

    /**
     * 設定で定義されたフォーム要素オブジェクトの配列を再帰的に取得する
     *
     * @param array フォーム要素設定配列
     * @param bool デフォルト値使用フラグ
     * @return array フォーム要素オブジェクトの配列
     */
    private function getElementsRecursive(array &$config, $default=false)
    {
        $elements = array();
        foreach ($config as $name => &$values) {
            $element = SyL_FormElementAbstract::createInstance($values['type'], $name, $values['display'], null, $values['attributes']);
            if ($element instanceof SyL_FormElementGroup) {
                $element->setFormat($values['format']);
                $element->setValue($this->getElementsRecursive($values['elements'], $default));
            } else {
                if ($default) {
                    $element->setValue($values['default']);
                }
                if ($values['separator'] && method_exists($element, 'setSeparator')) {
                    $element->setSeparator($values['separator']);
                }
                foreach ($values['options'] as $oname => $ovalue) {
                    $element->setOption($oname, $ovalue);
                }
                $validators = SyL_ValidationAbstract::createValidators();
                foreach ($values['validators'] as $vname => $vvalues) {
                    $voptions = isset($vvalues['options']) ? $vvalues['options'] : array();
                    $validator = SyL_ValidationAbstract::createValidator($vname, $vvalues['message'], $voptions);
                    $validators->addValidator($validator);
                }
                $element->setValidation($validators);
            }
            $elements[$name] = $element;
        }
        return $elements;
    }

    /**
     * フォーム名を取得する
     *
     * @return string フォーム名
     */
    public function getFormName()
    {
        return $this->form_name;
    }
}
