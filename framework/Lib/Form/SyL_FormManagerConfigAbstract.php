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

/**
 * フォームページ遷移設定クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_FormManagerConfigAbstract
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
     * フォーム設定ファイルの配列
     *
     * @var array
     */
    protected $config_files = array();

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

        $classname = 'SyL_FormManagerConfig' . ucfirst($ext);
        include_once $classname . '.php';
        $config = new $classname($filename);
        $config->parse();
        return $config;
    }

    /**
     * フォームページ遷移設定リソースをパースし取得する
     */
    public abstract function parse();

    /**
     * 設定で定義されたフォームページ遷移定義オブジェクトを取得する
     *
     * @return array フォーム要素オブジェクトの配列
     */
    public function getPageStates()
    {
        $states = array();
        foreach ($this->config as $name => $state) {
            $states[] = new SyL_FormPageState($name, $state['from.id'], $state['from.type'], $state['to.id'], $state['to.type']);
        }
        return $states;
    }

    /**
     * フォームページ遷移設定に関連したフォーム設定ファイル名を取得する
     *
     * @return array フォーム設定ファイル名の配列
     */
    public function getFormConfigFiles()
    {
        include_once dirname(__FILE__) . '/../Util/SyL_UtilReplaceConstant.php';
        return array_map(array('SyL_UtilReplaceConstant', 'replace'), $this->config_files);
    }
}
