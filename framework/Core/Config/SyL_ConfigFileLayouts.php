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
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * レイアウト設定情報取得クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileLayouts extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'layouts.xml';
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
        // アプリケーションレイアウト設定ファイル
        $config = SYL_APP_CONFIG_DIR . '/' . $this->config_file_name;
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

        if (count($this->config) == 0) {
            // 対応するアクションマッピングが存在しない場合
            throw new SyL_ConfigNotFoundException('layout mapping not found (layout name: ' . $this->router->getLayoutName() . ')');
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
        static $current_parameter = false;
        static $parameter_match = false;

        // 1アクションロード済みの場合以降スキップ
        if ($this->load) return;

        // XML解析結果を取得
        switch ($current_path) {
        case '/syl-layouts/layout':
            if (isset($this->config['file'])) {
                $this->load = true;
                return;
            }
            if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                return;
            }

            // レイアウト名判定
            if ($attribute['name'] == $this->router->getLayoutName()) {
                $this->config['file']       = $attribute['file'];
                $this->config['partial']    = array();
                $this->config['content']    = '';
                $this->config['parameters'] = array();
            }
            break;

        default:
            if (count($this->config) == 0) return;

            switch ($current_path) {
            case '/syl-layouts/layout/partial':
                $this->config['partial'][$attribute['name']] = $attribute['file'];
                break;
            case '/syl-layouts/layout/content':
                $this->config['content'] = $attribute['name'];
                break;
            case '/syl-layouts/layout/parameters/action':
                if ($parameter_match) {
                    $current_parameter = false;
                } else {
                    $current_parameter = (bool)preg_match('!^' . $attribute['path'] . '$!i', $this->router->getActionFile());
                }
                break;
            case '/syl-layouts/layout/parameters/action/parameter':
                if ($current_parameter) {
                    $this->config['parameters'][$attribute['name']] = $attribute['value'];
                    $parameter_match = true;
                }
                break;
            }
        }
    }
}
