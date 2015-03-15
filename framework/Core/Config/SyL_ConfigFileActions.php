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

/** アクションクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_ActionAbstract.php';

/**
 * アクション設定情報取得クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileActions extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
    protected $config_file_name = 'actions.xml';
    /**
     * デフォルト実行順序
     * 
     * @var int
     */
    const DEFAULT_PRIORITY = 5;
    /**
     * アクション実行メソッド名
     * 
     * @var string
     */
    const EXECUTE_METHOD_NAME = 'process';
    /**
     * 内部の整合性に関する名称
     * 
     * @var string
     */
    const ACTION_COMPONENT_NAME = 'action';
    /**
     * 設定ロード判定フラグ
     * 
     * @var bool
     */
     private $load = false;

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected function initializeConfigFiles()
    {
        $this->file_names[] = SYL_APP_CONFIG_DIR . '/' . $this->config_file_name;
    }

    /**
     * XMLファイルの解析処理
     *
     * @param bool キャッシュ有効フラグ
     */
    public function parse($enable_cache=true)
    {
        if (!$this->router->enableAction()) {
            SyL_Logger::info('action enable action: false');
            return;
        }

        $action_base_class = $this->router->getActionBaseClass();
        if ($action_base_class) {
            SyL_Loader::userLib($action_base_class);
        }

        if (SYL_CACHE && $enable_cache) {
            // キャッシュを使用する
            $cache_storage = SyL_CacheStorageAbstract::getInstance();
            try {
                $this->config = $cache_storage->getConfigCache($this->config_file_name, $this->file_names);
            } catch (SyL_CacheStorageNotFoundException $e) {
                parent::parse();
                if (!isset($this->config[self::ACTION_COMPONENT_NAME])) {
                    // 対応するアクションマッピングが存在しない場合
                    throw new SyL_ConfigNotFoundException("action mapping not found (" . $this->router->getActionFile() . ")");
                }
                $cache_storage->updateConfigCache($this->config_file_name, $this->config, $this->file_names);
            }
        } else {
            // キャッシュを使用しない
            parent::parse();
            if (!isset($this->config[self::ACTION_COMPONENT_NAME])) {
                // 対応するアクションマッピングが存在しない場合
                throw new SyL_ConfigNotFoundException("action mapping not found (" . $this->router->getActionFile() . ")");
            }
        }
    }

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attribute, $text)
    {
        static $method_name    = '';
        static $component_name = '';

        // 1アクションロード済みの場合以降スキップ
        if ($this->load) return;

        // XML解析結果を取得
        switch ($current_path) {
        case '/syl-actions/action':
            if (count($this->config) > 0) {
                $this->load = true;
                return;
            }
            if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                return;
            }

            // アクセスURL確認
            if (preg_match('!^' . $attribute['path'] . '$!i', $this->router->getActionFile(), $matches)) {
                $class  = $this->router->getActionClassName();
                $file   = $this->router->getActionDir() . $this->router->getActionFile();
                if (!is_file($file)) {
                    throw new SyL_ResponseNotFoundException("action file not found ({$file})");
                }
                $this->config[self::ACTION_COMPONENT_NAME] = new SyL_ContainerEventComponent($class, $file, self::DEFAULT_PRIORITY, self::EVENT_EXECUTE_SATREAM, false, true);

                $operation = new SyL_ContainerEventComponentOperation('method', self::EXECUTE_METHOD_NAME, false);
                $this->config[self::ACTION_COMPONENT_NAME]->addOperation($operation);

                $this->config[self::ACTION_COMPONENT_NAME]->getCurrentOperation()->addParameter('component', 'context');
                $this->config[self::ACTION_COMPONENT_NAME]->getCurrentOperation()->addParameter('component', 'data');
            }
            break;

        default:
            if (count($this->config) == 0) return;

            switch ($current_path) {
            case '/syl-actions/action/setter':
                $static = (isset($attribute['static']) && ($attribute['static'] === 'true'));
                $operation = new SyL_ContainerEventComponentOperation('setter', $attribute['name'], $static);
                $event_method = isset($attribute['event']) ? $attribute['event'] : $event;
                $this->config[self::ACTION_COMPONENT_NAME]->addOperation($operation);
                break;

            case '/syl-actions/action/method':
                $static = (isset($attribute['static']) && ($attribute['static'] == 'true'));
                $operation = new SyL_ContainerEventComponentOperation('method', $attribute['name'], $static);
                $this->config[self::ACTION_COMPONENT_NAME]->addOperation($operation);
                break;

            case '/syl-actions/action/setter/arg':
            case '/syl-actions/action/method/arg':
                $type = !empty($attribute['type']) ? $attribute['type'] : 'value';
                $this->config[self::ACTION_COMPONENT_NAME]->getCurrentOperation()->addParameter($type, $text);
                break;

           case '/syl-actions/action/components/component':
                if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                    $component_name = '';
                    return;
                }

                $component_name = $attribute['name'];

                $class    = $attribute['class'];
                $file     = $attribute['file'];
                $priority = self::DEFAULT_PRIORITY - 2;
                $priority = isset($attribute['priority']) ? floatval($priority . '.' . $attribute['priority']) : $priority;
                $this->config[$component_name] = new SyL_ContainerEventComponent($class, $file, $priority, self::EVENT_EXECUTE_SATREAM, false, true);
                break;

            default:
                if (!$component_name) return;

                switch ($current_path) {
                case '/syl-actions/action/components/component/constructor':
                    $static = (isset($attribute['static']) && ($attribute['static'] == 'true'));
                    $static_method = null;
                    if ($static && !empty($attribute['name'])) {
                        $static_method = $attribute['name'];
                    }
                    $operation = new SyL_ContainerEventComponentOperation('constructor', $static_method, $static);
                    $this->config[$component_name]->addOperation($operation);
                    break;

                case '/syl-actions/action/components/component/setter':
                    $static = (isset($attribute['static']) && ($attribute['static'] === 'true'));
                    $operation = new SyL_ContainerEventComponentOperation('setter', $attribute['name'], $static);
                    $this->config[$component_name]->addOperation($operation);
                    break;

                case '/syl-actions/action/components/component/method':
                    $static = (isset($attribute['static']) && ($attribute['static'] == 'true'));
                    $operation = new SyL_ContainerEventComponentOperation('method', $attribute['name'], $static);
                    $this->config[$component_name]->addOperation($operation);
                    break;

                case '/syl-actions/action/components/component/setter/arg':
                case '/syl-actions/action/components/component/constructor/arg':
                case '/syl-actions/action/components/component/method/arg':
                    $type = !empty($attribute['type']) ? $attribute['type'] : 'value';
                    $this->config[$component_name]->getCurrentOperation()->addParameter($type, $text);
                    break;
                }
            }
        }
    }
}
