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

require_once SYL_FRAMEWORK_DIR . '/Core/Filter/SyL_FilterAbstract.php';

/**
 * フィルタ設定情報取得クラス
 *
 * アクションやビューの前後に処理を追加するような場合に
 * 使用するフィルタの設定を取得する。
 *
 * 物理的には、以下のファイルから取得する
 *   (1) SYL_APP_CONFIG_DIR / filters.xml
 *   (2) SYL_PROJECT_CONFIG_DIR / filters.xml
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileFilters extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
    protected $config_file_name = 'filters.xml';
    /**
     * デフォルト実行順序
     * 
     * @param int
     */
    const DEFAULT_PRIORITY = 3;
    /**
     * アクション実行前イベント名
     * 
     * @var string
     */
    const PRE_ACTION_METHOD_NAME = 'preAction';
    /**
     * アクション実行後イベント名
     * 
     * @var string
     */
    const POST_ACTION_METHOD_NAME = 'postAction';
    /**
     * ビュー表示前イベント名
     * 
     * @var string
     */
    const PRE_RENDER_METHOD_NAME = 'preRender';
    /**
     * ビュー表示後イベント名
     * 
     * @var string
     */
    const POST_RENDER_METHOD_NAME = 'postRender';

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected function initializeConfigFiles()
    {
        // アプリケーションフィルタ設定ファイル
        $config = SYL_APP_CONFIG_DIR . '/' . $this->config_file_name;
        if (is_file($config)) {
            $this->file_names[] = $config;
        }
        // プロジェクトフィルタ設定ファイル
        $config = SYL_PROJECT_CONFIG_DIR . '/' . $this->config_file_name;
        if (is_file($config)) {
            $this->file_names[] = $config;
        }
    }

    /**
     * XMLファイルの解析処理
     *
     * @param bool キャッシュ有効フラグ
     */
    public function parse($enable_cache=true)
    {
        if (count($this->file_names) == 0) {
            return;
        }

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
        static $current_name = '';

        switch ($current_path) {
        case '/syl-filters/filter':
            if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                $current_name = '';
                return;
            }

            // アクションファイル取得用ルータオブジェクト
            if (preg_match('!^' . $attribute['path'] . '$!i', $this->router->getActionFile())) {
                $current_name = $attribute['name'];
                $class = $attribute['class'];
                $file  = $attribute['file'];
                $this->config[$current_name] = new SyL_ContainerEventComponent($class, $file, self::DEFAULT_PRIORITY, self::EVENT_LOAD_SATREAM, false, true);
            }
            break;

        default:
            if (!isset($this->config[$current_name])) return;

            switch ($current_path) {
            case '/syl-filters/filter/preAction':
                $operation = new SyL_ContainerEventComponentOperation('method', self::PRE_ACTION_METHOD_NAME, false);
                $this->config[$current_name]->addOperation($operation, self::EVENT_LOAD_SATREAM);
                break;

            case '/syl-filters/filter/postAction':
                $operation = new SyL_ContainerEventComponentOperation('method', self::POST_ACTION_METHOD_NAME, false);
                $this->config[$current_name]->addOperation($operation, self::EVENT_MIDDLE_SATREAM);
                break;

            case '/syl-filters/filter/preRender':
                $operation = new SyL_ContainerEventComponentOperation('method', self::PRE_RENDER_METHOD_NAME, false);
                $this->config[$current_name]->addOperation($operation, self::EVENT_RENDER_SATREAM);
                break;

            case '/syl-filters/filter/postRender':
                $operation = new SyL_ContainerEventComponentOperation('method', self::POST_RENDER_METHOD_NAME, false);
                $this->config[$current_name]->addOperation($operation, self::EVENT_UNLOAD_SATREAM);
                break;

            case '/syl-filters/filter/preAction/arg':
            case '/syl-filters/filter/postAction/arg':
            case '/syl-filters/filter/preRender/arg':
            case '/syl-filters/filter/postRender/arg':
                $type = !empty($attribute['type']) ? $attribute['type'] : 'value';
                $this->config[$current_name]->getCurrentOperation()->addParameter($type, $text);
                break;
            }
        }
    }
}
