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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * コンポーネント設定情報取得クラス
 *
 * initStream イベントで、コンテナにコンポーネントを作成するための
 * 設定を取得する。
 *
 * 物理的には、以下のファイルから取得する
 *   SYL_PROJECT_CONFIG_DIR / defines.xml
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileComponents extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'components.xml';
    /**
     * デフォルト実行順序
     * 
     * @param int
     */
    const DEFAULT_PRIORITY = 3;

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected function initializeConfigFiles()
    {
        $this->file_names[] = SYL_PROJECT_CONFIG_DIR . '/' . $this->config_file_name;
    }

    /**
     * XMLファイルの解析処理
     *
     * @param bool キャッシュ有効フラグ
     */
    public function parse($enable_cache=true)
    {
        if (SYL_CACHE && $enable_cache) {
            // キャッシュを使用する
            $cache_storage = SyL_CacheStorageAbstract::getInstance();
            try {
                $this->config = $cache_storage->getConfigCache($this->config_file_name, $this->file_names);
            } catch (SyL_CacheStorageNotFoundException $e) {
                parent::parse();
                $cache_storage->updateConfigCache($this->config_file_name, $this->config, $this->file_names);
            }
        } else {
            // キャッシュを使用しない
            parent::parse();
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
        static $current_name  = '';
        static $event = '';

        // XML解析結果を取得
        switch ($current_path) {
        case '/syl-components/component':
            if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                $current_name = '';
                return;
            }
            $current_name = $attribute['name'];

            $class    = $attribute['class'];
            $file     = $attribute['file'];
            $priority = isset($attribute['priority']) ? (float)$attribute['priority'] : self::DEFAULT_PRIORITY;
            $event    = isset($attribute['event']) ? $attribute['event'] : self::EVENT_INIT_SATREAM;
            $this->config[$current_name] = new SyL_ContainerEventComponent($class, $file, $priority, $event, false, true);
            break;

        default:
            if (!isset($this->config[$current_name])) return;

            switch ($current_path) {
            case '/syl-components/component/constructor':
                $static = (isset($attribute['static']) && ($attribute['static'] == 'true'));
                $static_method = null;
                if ($static && !empty($attribute['name'])) {
                    $static_method = $attribute['name'];
                }
                $operation = new SyL_ContainerEventComponentOperation('constructor', $static_method, $static);
                $this->config[$current_name]->addOperation($operation);
                break;

            case '/syl-components/component/setter':
                $static = (isset($attribute['static']) && ($attribute['static'] === 'true'));
                $operation = new SyL_ContainerEventComponentOperation('setter', $attribute['name'], $static);
                $event_method = isset($attribute['event']) ? $attribute['event'] : $event;
                $this->config[$current_name]->addOperation($operation, $event_method);
                break;

            case '/syl-components/component/method':
                $static = (isset($attribute['static']) && ($attribute['static'] == 'true'));
                $operation = new SyL_ContainerEventComponentOperation('method', $attribute['name'], $static);
                $event_method = isset($attribute['event']) ? $attribute['event'] : $event;
                $this->config[$current_name]->addOperation($operation, $event_method);
                break;

            case '/syl-components/component/constructor/arg':
            case '/syl-components/component/setter/arg':
            case '/syl-components/component/method/arg':
                $type = !empty($attribute['type']) ? $attribute['type'] : 'value';
                $this->config[$current_name]->getCurrentOperation()->addParameter($type, $text);
                break;
            }
        }
    }
}
