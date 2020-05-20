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
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** コンテナ例外クラス */
require_once 'SyL_ContainerException.php';
/** コンポーネントに対する操作定義クラス */
require_once 'SyL_ContainerEventComponent.php';
/** コンポーネントインターフェイス */
require_once 'SyL_ContainerComponentInterface.php';

/**
 * コンテナクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_Container
{
    /**
     * コンポーネント定義
     * 
     * @var array
     */
    private $config = array();
    /**
     * コンポーネントオブジェクト格納配列
     * 
     * @var array
     */
    private $components = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * イベント起動
     * 設定ファイルを読み込みコンテナに登録 or 実行
     *
     * @param string イベントメソッド名
     * @param string 設定ファイル
     * @param string 実行コンポーネント名
     */
    public function raiseEvent($event, $config_type='', $component_name='')
    {
        // イベント開始ログ
        SyL_Logger::trace("{$event} event start");

        // 設定ファイルの読み込み
        if ($config_type) {
            $this->config += $this->readConfig($config_type);
            // 優先順位ソート処理
            uasort($this->config, array(__CLASS__, 'sortConfig'));
        }

        // コンテナの設定ログ
        if ($event == 'finalStream') {
            //SyL_Logger::debug("{$event} event global config ..." . print_r($this->config, true));
        }

        // コンポーネント取得・実行ループ
        foreach ($this->config as $name => &$component) {
            $classname  = $component->class;
            $operations = $component->getOperations($event);
            foreach ($operations as &$operation) {
                switch ($operation->type) {
                case 'constructor':
                    $load = false;
                    try {
                        self::LoadLib($component->file);
                        if (!class_exists($classname)) {
                            throw new SyL_ClassNotFoundException("class not found ({$classname})");
                        }
                        $load = true;
                    } catch (SyL_FileNotFoundException $e) {
                        SyL_Logger::info("library load failed ({$component->file})");
                        if ($component->force) {
                            throw $e;
                        }
                    } catch (SyL_ClassNotFoundException $e) {
                        SyL_Logger::warn("class not found ({$component->file}, {$classname})");
                        if ($component->force) {
                            throw $e;
                        }
                    }

                    if (!$load) {
                        break;
                    }

                    $obj = null;
                    $parameters = $this->arrangeEventMethodParameters($operation->getParameters());
                    if ($operation->static) {
                        SyL_Logger::trace("constructor static injection ({$classname}::{$operation->name})");
                        $obj = call_user_func_array(array($classname, $operation->name), $parameters);
                    } else {
                        $eval_parameter = '';
                        for ($i=0; $i<count($parameters); $i++) {
                            if ($i > 0) {
                                $eval_parameter .= ', ';
                            }
                            $eval_parameter .= '$parameters[' . $i . ']';
                        }
                        SyL_Logger::trace("constructor injection (new {$classname})");
                        eval('$obj = new ' . $classname . '(' . $eval_parameter . ');'); 
                    }
                    $this->setComponent($name, $obj);
                    break;

                case 'method':
                    $obj = $operation->static ? $classname : $this->getComponent($name);
                    $parameters = $this->arrangeEventMethodParameters($operation->getParameters());
                    SyL_Logger::trace("method injection ({$classname}::{$operation->name})");
                    call_user_func_array(array($obj, $operation->name), $parameters);
                    break;

                case 'setter':
                    $parameters = $this->arrangeEventMethodParameters($operation->getParameters());
                    if ($operation->static) {
                        SyL_Logger::trace("setter static injection ({$classname}::{$operation->name})");
                        for ($i=0; $i<count($parameters); $i++) {
                            eval($classname . '::' . $operation->name . ' = $parameters[' . $i . '];'); 
                        }
                    } else {
                        SyL_Logger::trace("setter injection ({$classname}->{$operation->name})");
                        $obj = $this->getComponent($name);
                        foreach ($parameters as $value) {
                            $obj->{$operation->name} = $value;
                        }
                    }
                    break;
                }
            }
        }

        // イベント終了ログ
        SyL_Logger::trace("{$event} event end");
    }

    /**
     * イベントメソッドパラメータを整える
     *
     * @param array イベントメソッドパラメータ
     * @return array 整形後のイベントメソッドパラメータ
     */
    private function arrangeEventMethodParameters($parameters)
    {
        $args = array();
        foreach ($parameters as $parameter) {
            switch ($parameter[0]) {
            case 'constant':
                $args[] = constant($parameter[1]);
                break;
            case 'config':
                $args[] = SyL_Config::get($parameter[1]);
                break;
            case 'component':
                $args[] = $this->getComponent($parameter[1]);
                break;
            default:
                $args[] = $parameter[1];
                break;
            }
        }
        return $args;
    }

    /**
     * 設定ファイルを読み込む
     *
     * @param string 設定ファイルのタイプ
     * @return array 設定ファイルの内容
     */
    private function readConfig($config_type)
    {
        $config = SyL_ConfigFileAbstract::createInstance($config_type);
        if ($this->isComponent('context')) {
            $config->setRouter($this->getComponent('context')->getRouter());
        }
        $config->parse();
        return $config->getConfig();
    }

    /**
     * 実行順序を優先順位ごとにソート
     *
     * @param SyL_ContainerEventComponent 現在の設定配列
     * @param SyL_ContainerEventComponent 次の設定配列
     */
    private static function sortConfig(SyL_ContainerEventComponent $current, SyL_ContainerEventComponent $next)
    {
        return strcmp($current->priority, $next->priority);
    }

    /**
     * ライブラリをロード
     *
     * @param string ロードファイル
     */
    private static function LoadLib($file)
    {
        // アプリケーションライブラリディレクトリからのパス
        $syl_app_file = SYL_APP_LIB_DIR . '/' . $file;
        // プロジェクトディレクトリからのパス
        $syl_project_file = SYL_PROJECT_LIB_DIR . '/' . $file;
        // SyLディレクトリからのパス
        $syl_core_file = SYL_FRAMEWORK_DIR . '/' . $file;

        if (is_file($syl_app_file)) {
            include_once $syl_app_file;
        } else if (is_file($syl_project_file)) {
            include_once $syl_project_file;
        } else if (is_file($syl_core_file)) {
            include_once $syl_core_file;
        } else if (is_file($file)) {
            include_once $file;
        } else {
            // コンポーネントファイルが無い場合
            throw new SyL_FileNotFoundException("component include failed: file not found ({$syl_app_file} or {$syl_project_file} or {$syl_core_file} or {$file})");
        }
    }

    /**
     * コンポーネントを取得
     *
     * @param string コンポーネント名
     * @return SyL_ContainerComponentInterface コンポーネントオブジェクト
     */
    public function getComponent($name)
    {
        return $this->isComponent($name) ? $this->components[$name] : null;
    }

    /**
     * コンポーネントをセット
     *
     * @param string コンポーネント名
     * @param SyL_ContainerComponentInterface コンポーネントオブジェクト
     */
    public function setComponent($name, SyL_ContainerComponentInterface $component)
    {
        $this->components[$name] = $component;
    }

    /**
     * コンポーネントのロード判定
     *
     * @param string コンポーネント名
     * @param bool true: ロード済み、false: 未ロード
     */
    public function isComponent($name)
    {
        return array_key_exists($name, $this->components);
    }

    /**
     * コンポーネントの削除
     *
     * @param string コンポーネント名
     */
    public function deleteComponent($name)
    {
        unset($this->components[$name]);
    }
}
