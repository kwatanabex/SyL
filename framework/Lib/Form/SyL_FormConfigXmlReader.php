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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';

/**
 * フォーム設定XMLファイル読み込みクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormConfigXmlReader extends SyL_XmlParserAbstract
{
    /**
     * フォーム名
     * 
     * @var string
     */
    private $name = 'syl_form';
    /**
     * カスタムバリデーションディレクトリ
     *
     * @var string
     */
    protected $custom_validator_dir = '';
    /**
     * 設定ファイル配列
     * 
     * @var array
     */
    private $config = array();

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        static $name = null;
        static $subname = null;
        static $validator_name = null;

        switch ($current_path) {
        case '/syl-form':
            if (isset($attributes['name'])) {
                $this->name = $attributes['name'];
            }
            if (isset($attributes['custom-validator-dir'])) {
                $this->custom_validator_dir = $attributes['custom-validator-dir'];
            }
            break;
        case '/syl-form/element':
            if (isset($this->config[$attributes['name']])) {
                throw new SyL_DuplicateException('duplicate element name (' . $attributes['name'] . ')');
            }
            $name = $attributes['name'];
            $this->config[$name] = array();
            $this->config[$name]['type']       = $attributes['type'];
            $this->config[$name]['display']    = $attributes['display'];
            $this->config[$name]['default']    = null;
            $this->config[$name]['format']     = null;
            $this->config[$name]['separator']  = null;
            $this->config[$name]['options']    = array();
            $this->config[$name]['attributes'] = array();
            $this->config[$name]['validators'] = array();
            $this->config[$name]['elements']   = array();
            break;
        case '/syl-form/element/format':
            $this->config[$name]['format'] = $text;
            break;
        case '/syl-form/element/separator':
            $this->config[$name]['separator'] = $text;
            break;
        case '/syl-form/element/default':
            $value = $text;
            if (isset($attributes['array']) && ($attributes['array'] == 'true')) {
                $separator = isset($attributes['seperator']) ? $attributes['seperator'] : ',';
                $value = explode($separator, $text);
            }
            $this->config[$name]['default'] = $value;
            break;
        case '/syl-form/element/options/option':
            $this->config[$name]['options'][$attributes['name']] = $text;
            break;
        case '/syl-form/element/attributes/attribute':
            $this->config[$name]['attributes'][$attributes['name']] = $text;
            break;

        case '/syl-form/element/validators/validator':
            $validator_name = $attributes['name'];
            $this->config[$name]['validators'][$validator_name] = array();
            $this->config[$name]['validators'][$validator_name]['message'] = $attributes['message'];
            $this->config[$name]['validators'][$validator_name]['options'] = array();
            break;
        case '/syl-form/element/validators/validator/option':
            $this->config[$name]['validators'][$validator_name]['options'][$attributes['name']] = $text;
            break;

        case '/syl-form/element/element':
            if (isset($this->config[$attributes['name']])) {
                throw new SyL_DuplicateException('duplicate element name (' . $attributes['name'] . ')');
            }
            $subname = $attributes['name'];
            $this->config[$name]['elements'][$subname] = array();
            $this->config[$name]['elements'][$subname]['type']       = $attributes['type'];
            $this->config[$name]['elements'][$subname]['display']    = $attributes['display'];
            $this->config[$name]['elements'][$subname]['default']    = null;
            $this->config[$name]['elements'][$subname]['separator']  = null;
            $this->config[$name]['elements'][$subname]['options']    = array();
            $this->config[$name]['elements'][$subname]['attributes'] = array();
            $this->config[$name]['elements'][$subname]['validators'] = array();
            break;
        case '/syl-form/element/element/separator':
            $this->config[$name]['elements'][$subname]['separator'] = $text;
            break;
        case '/syl-form/element/element/default':
            $value = $text;
            if (isset($attributes['array']) && ($attributes['array'] == 'true')) {
                $separator = isset($attributes['separator']) ? $attributes['separator'] : ',';
                $value = explode($separator, $text);
            }
            $this->config[$name]['elements'][$subname]['default'] = $value;
            break;
        case '/syl-form/element/element/options/option':
            $this->config[$name]['elements'][$subname]['options'][$attributes['name']] = $text;
            break;
        case '/syl-form/element/element/attributes/attribute':
            $this->config[$name]['elements'][$subname]['attributes'][$attributes['name']] = $text;
            break;

        case '/syl-form/element/element/validators/validator':
            $validator_name = $attributes['name'];
            $this->config[$name]['elements'][$subname]['validators'][$validator_name] = array();
            $this->config[$name]['elements'][$subname]['validators'][$validator_name]['message'] = $attributes['message'];
            $this->config[$name]['elements'][$subname]['validators'][$validator_name]['options'] = array();
            break;
        case '/syl-form/element/element/validators/validator/option':
            $this->config[$name]['elements'][$subname]['validators'][$validator_name]['options'][$attributes['name']] = $text;
            break;

        }
    }

    /**
     * XML設定値を取得する
     * 
     * @return array 設定値配列
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * フォーム名を取得する
     * 
     * @return string フォーム名
     */
    public function getFormName()
    {
        return $this->name;
    }

    /**
     * カスタムバリデーションディレクトリを取得する
     * 
     * @return string カスタムバリデーションディレクトリ
     */
    public function getCustomValidatorDir()
    {
        return $this->custom_validator_dir;
    }
}
