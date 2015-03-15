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
 * 検証設定クラス
 * 
 * array(
 *   'def1' => array(
 *     [0] => array(
 *       'validator'    => 'require',
 *       'message' => '{name}は必須です',
 *       'options' => array(
 *         'max'   => '19',
 *         'min'   => '1'
 *       )
 *     ),
 *     [1] => ...
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_ValidationConfigAbstract
{
    /**
     * ファイル名
     *
     * @var string
     */
    protected $filename = '';
    /**
     * 検証設定配列
     *
     * @var array
     */
    protected $config = array();
    /**
     * 検証設定配列（名前）
     *
     * @var array
     */
    protected $config_name = array();

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
     * 設定オブジェクトを作成する
     *
     * @param string 設定ファイル名
     * @param string 設定ファイルの拡張子
     * @return SyL_ValidationConfigAbstract 検証設定オブジェクト
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

        $classname = 'SyL_ValidationConfig' . ucfirst($ext);
        include_once $classname . '.php';
        $config = new $classname($filename);
        $config->parse();
        return $config;
    }

    /**
     * 検証設定リソースをパースし取得する
     */
    public abstract function parse();

    /**
     * 検証設定値を追加する
     *
     * @param string 要素名
     * @param string バリデーション名
     * @param string エラーメッセージ
     * @param array 検証オプション
     * @param string 表示名
     */
    public function add($name, $validation_name, $error_message, array $options=array(), $display_name='')
    {
        if (!isset($this->config[$name])) {
            $this->config[$name] = array();
            $this->config_name[$name] = ($display_name !== '') ? $display_name : $name;
        }

        $i = count($this->config[$name]);
        $this->config[$name][$i] = array(
          'validator' => $validation_name,
          'message'   => $error_message,
          'options'   => ($options ? $options : array())
        );
    }

    /**
     * 要素名に対応する個別検証グループオブジェクトを取得する
     *
     * @param string 要素名
     * @return SyL_ValidationValidators 個別検証グループオブジェクト
     * @throws SyL_KeyNotFoundException 要素名に対する設定値が存在しない場合
     */
    public function getValidators($name)
    {
        if (!isset($this->config[$name])) {
            throw new SyL_KeyNotFoundException("validator name not found ({$name})");
        }
        $validators = SyL_ValidationAbstract::createValidators();
        foreach ($this->config[$name] as &$config) {
            $options = isset($config['options']) ? $config['options'] : array();
            $validator = SyL_ValidationAbstract::createValidator($config['validator'], $config['message'], $options);
            $validators->addValidator($validator);
        }
        return $validators;
    }

    /**
     * 要素名に対応する検証グループ名を取得する
     *
     * @param string 要素名
     * @return string 検証グループ名
     * @throws SyL_KeyNotFoundException 要素名に対する設定値が存在しない場合
     */
    public function getName($name)
    {
        if (!isset($this->config_name[$name])) {
            throw new SyL_KeyNotFoundException("validator name not found ({$name})");
        }
        return $this->config_name[$name];
    }

    /**
     * 全要素名を取得する
     *
     * @return array 全要素名
     */
    public function getNames()
    {
        return $this->config_name;
    }
}
