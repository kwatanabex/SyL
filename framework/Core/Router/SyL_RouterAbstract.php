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
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 遷移情報例外クラス */
require_once 'SyL_RouterException.php';

/**
 * 遷移情報クラス
 *
 * フレームワーク内部遷移に関する情報を保持する。
 * 内部遷移はフレームワークの流れであるが、
 * このクラスでは特に、アクションとビュー（テンプレート）の情報を扱う。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_RouterAbstract
{
    /**
     * アクションディレクトリルート
     *
     * @var string
     */
    private $action_dir = '';
    /**
     * テンプレートディレクトリルート
     *
     * @var string
     */
    private $template_dir = '';
    /**
     * アクションファイル
     *
     * @var string
     */
    private $action_file = '';
    /**
     * アクションファイル（オリジナル）
     *
     * @var string
     */
    private $action_file_org = '';
    /**
     * アクション基底クラス
     *
     * @var string
     */
    private $action_base_class = '';

    /**
     * テンプレートファイル
     *
     * @var string
     */
    private $template_file = '';

    /**
     * ビュークラス
     *
     * @var string
     */
    private $view_class = '';
    /**
     * レイアウト名
     *
     * @var string
     */
    private $layout_name = '';
    /**
     * アクション実行フラグ
     *
     * @var bool
     */
    protected $enable_action = true;
    /**
     * テンプレート実行フラグ
     *
     * @var bool
     */
    protected $enable_template = true;

    /**
     * デフォルトアクションファイル名
     * ※先頭大文字
     *
     * @var string
     */
    protected $action_default_file = 'Index.php';
    /**
     * アクションファイルの拡張子
     *
     * @var string
     */
    protected $action_file_ext = '.php';
    /**
     * テンプレートファイルの拡張子
     * 
     * @var string
     */
    protected $template_ext = '.html';

    /**
     * パス一致パラメータ
     * 
     * @var array
     */
    protected $path_match_parameter = array();

    /**
     * コンストラクタ
     *
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_Data $data)
    {
        // アクションディレクトリ定義
        $this->action_dir = SYL_APP_DIR . '/actions';
        // テンプレートディレクトリ定義
        $this->template_dir = SYL_APP_DIR . '/templates';
        // アクション情報取得
        list($this->action_file, $this->template_file) = $this->createActionInfo($data);
        $this->action_file_org = $this->action_file;

        // ファイルパスチェック
        $this->validPath();
        // 設定ファイルからルートを構築
        $this->buildRouting();

        // ルーティングログ
        SyL_Logger::debug(sprintf('router info: type="%s", action_file="%s", template_file="%s"', get_class($this), $this->action_file, $this->template_file));
    }

    /**
     * 遷移情報オブジェクトを取得
     *
     * @param SyL_Data データオブジェクト
     * @return SyL_RouterAbstract 遷移情報オブジェクト
     */
    public static function createInstance(SyL_Data $data)
    {
        $classname = '';
        $name = SyL_CustomClass::getRouterClass();
        if ($name) {
            $classname = SyL_Loader::userLib($name);
        } else {
            $classname = (SYL_APP_TYPE == SyL_AppType::COMMAND) ? 'SyL_RouterCommand' : 'SyL_RouterPathinfo';
            include_once $classname . '.php';
        }
        return new $classname($data);
    }

    /**
     * 遷移情報オブジェクトのプロパティを作成
     *
     * @param SyL_Data データオブジェクト
     * @return array array(アクションファイル, テンプレートファイル)
     */
    protected abstract function createActionInfo(SyL_Data $data);

    /**
     * アクションディレクトリルートを取得
     *
     * @return string アクションディレクトリルート
     */
    public function getActionDir()
    {
        return $this->action_dir;
    }

    /**
     * テンプレートディレクトリルートを取得
     *
     * @return string テンプレートディレクトリルート
     */
    public function getTemplateDir()
    {
        return $this->template_dir;
    }

    /**
     * アクションファイルを取得
     *
     * @return string アクションファイル
     */
    public function getActionFile()
    {
        return $this->action_file;
    }

    /**
     * テンプレートファイルをセット
     *
     * 先頭に「/」必須
     *
     * @param string テンプレートファイル
     */
    public function setTemplateFile($template_file)
    {
        $this->template_file = $template_file;
    }

    /**
     * テンプレートファイルを取得
     *
     * @param bool テンプレートファイルが無い場合エラー起動
     * @return string テンプレートファイル
     * @throws SyL_RouterNotFoundException 引数が true かつテンプレートファイルが無い場合
     */
    public function getTemplateFile($file_not_found=true)
    {
        // テンプレートファイル存在チェック
        if ($file_not_found && !is_file($this->template_dir . $this->template_file)) {
            throw new SyL_RouterNotFoundException("template file not found (dir: {$this->template_dir} file: {$this->template_file})");
        }
        return $this->template_file;
    }

    /**
     * クラス名を取得
     *
     * クラス名はカレントのアクションファイルから取得する。
     *
     * @return string クラス名
     */
    public function getActionClassName()
    {
        $classname = str_replace('/', '_', substr($this->action_file, 1));
        return preg_replace('/(' . preg_quote($this->action_file_ext) . ')$/', '', $classname);
    }

    /**
     * アクション基底クラス名を取得する
     *
     * @return string アクション基底クラス名
     */
    public function getActionBaseClass()
    {
        return $this->action_base_class;
    }

    /**
     * ビュークラスをセット
     *
     * @param string ビュークラス
     */
    public function setViewClass($view_class)
    {
        $this->view_class = $view_class;
    }

    /**
     * ビュークラスを取得
     *
     * @return string ビュークラス
     */
    public function getViewClass()
    {
        return $this->view_class;
    }

    /**
     * レイアウト名をセット
     *
     * @param string レイアウト名
     */
    public function setLayoutName($layout_name)
    {
        $this->layout_name = $layout_name;
    }

    /**
     * レイアウト名を取得
     *
     * @return string レイアウト名
     */
    public function getLayoutName()
    {
        return $this->layout_name;
    }

    /**
     * アクション実行判定を行う
     *
     * @param bool アクション実行フラグ
     * @return bool アクション実行判定結果
     */
    public function enableAction($enable_action=null)
    {
        if (is_bool($enable_action)) {
            $this->enable_action = $enable_action;
        }
        return $this->enable_action;
    }

    /**
     * テンプレート実行判定を行う
     *
     * @return bool テンプレート実行判定結果
     */
    public function enableTemplate()
    {
        return $this->enable_template;
    }

    /**
     * パス一致パラメータを取得する
     *
     * @return array パス一致パラメータ
     */
    public function getPathMatches()
    {
        return $this->match_parameter;
    }

    /**
     * ルーティング設定を適用する
     */
    private function buildRouting()
    {
        $config = SyL_ConfigFileAbstract::createInstance('routers');
        $config->setRouter($this);
        $config->parse();
        $router = $config->getConfig();
        if (!$router) {
            throw new SyL_RouterConfigNotFoundException('router config not found in routers.xml (' . $this->action_file . ')');
        }

        if ($router['actionBaseClass']) {
            $this->action_base_class = $router['actionBaseClass'];
        }

        if ($router['enableAction']) {
            if ($router['forwardAction']) {
                $this->action_file = $router['forwardAction'];
            }
        } else {
            $this->enable_action = false;
        }

        if ($router['enableTemplate']) {
            if ($router['forwardTemplate']) {
                $this->setTemplateFile($router['forwardTemplate']);
            }
            if ($router['viewClass'])  $this->view_class  = $router['viewClass'];
            if ($router['layoutName']) $this->layout_name = $router['layoutName'];
        } else {
            $this->enable_template = false;
        }
        $this->match_parameter = $router['match_parameter'];
    }

    /**
     * ディレクトリ／ファイルのパスチェック
     * ※ディレクトリ／ファイルの先頭「_」はアクセス不可
     *
     * @throws SyL_RouterInvalidPathException 無効なパスの場合
     */
    public function validPath()
    {
        foreach (explode('/', $this->action_file) as $tmp) {
            if ($tmp && ($tmp[0] == '_')) {
                throw new SyL_RouterInvalidPathException("invalid first char `_' of directory or file name ({$tmp})");
            }
        }
    }
}
