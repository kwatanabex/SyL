<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** XMLパーサークラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Xml/SyL_XmlParserAbstract.php';


/** 
 * 設定ファイル取得クラス
 *
 * SyLフレームワークで使用する設定ファイル（XMLファイル）をパースするための
 * 基底クラス。
 * サブクラスを取得する場合は、createInstance メソッドを使用する。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ConfigFileAbstract extends SyL_XmlParserAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
    protected $config_file_name = null;
    /**
     * XMLファイル名（複数指定可能）
     * 
     * @var array
     */
    protected $file_names = array();
    /**
     * 設定ファイル配列
     * 
     * @var array
     */
    protected $config = array();
    /**
     * 遷移情報オブジェクト
     * 
     * @var SyL_RouterAbstract
     */
    protected $router = null;

    /**
     * 初期化処理イベント
     * 
     * @var string
     */
    const EVENT_INIT_SATREAM = 'initStream';
    /**
     * アクション実行前イベント
     * 
     * @var string
     */
    const EVENT_LOAD_SATREAM = 'loadStream';
    /**
     * アクション実行イベント
     * 
     * @var string
     */
    const EVENT_EXECUTE_SATREAM = 'executeStream';
    /**
     * ビュー表示実行前処理イベント
     * 
     * @var string
     */
    const EVENT_MIDDLE_SATREAM = 'middleStream';
    /**
     * ビュー表示処理イベント
     * 
     * @var string
     */
    const EVENT_RENDER_SATREAM = 'renderStream';
    /**
     * ビュー表示実行後処理イベント
     * 
     * @var string
     */
    const EVENT_UNLOAD_SATREAM = 'unloadStream';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->initializeConfigFiles();
        parent::setOutputEncoding(SYL_ENCODE_INTERNAL);
    }

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected abstract function initializeConfigFiles();

    /**
     * 設定ファイル数をセットする
     * 
     * @param array 設定ファイル配列
     * @param bool 既存ファイル初期化フラグ
     */
    public function setConfigFiles(array $file_names, $init=true)
    {
        if ($init) {
            $this->file_names = $file_names;
        } else {
            $this->file_names = array_merge($this->file_names, $file_names);
        }
    }

    /**
     * 設定ファイル取得オブジェクトを取得する
     *
     * @param string 設定ファイル名
     * @return SyL_ConfigFileAbstract 設定ファイル取得オブジェクト
     */
    public static function createInstance($config_name)
    {
        $classname = 'SyL_ConfigFile' . ucfirst($config_name);
        include_once "{$classname}.php";
        return new $classname();
    }

    /**
     * 設定値を取得
     * 
     * @return array 設定値配列
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 遷移情報オブジェクトをセット
     *
     * @param SyL_ContextAbstract 遷移情報オブジェクト
     */
    public function setRouter(SyL_RouterAbstract $router)
    {
        $this->router = $router;
    }

    /**
     * XMLファイルの解析処理
     *
     * 複数のXMLファイルを解析し、$this->config にセットする。
     * 複数のXMLファイルに、同じキー値が存在した場合、最初に読み込まれたほうが有効となる。
     */
    public function parse()
    {
        $config = array();
        foreach ($this->file_names as $file_name) {
            parent::setResource($file_name);
            parent::parse();
            if (is_array($this->config)) {
                $config += $this->config;
            }
            $this->config = array();
        }
        $this->config = $config;
    }
}
