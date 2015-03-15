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
 * ルーティング設定情報取得クラス
 *
 * アクション／テンプレートフローを制御するための設定を取得する。
 *
 * 物理的には、以下のファイルから取得する
 *   SYL_APP_CONFIG_DIR / routers.xml
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileRouters extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'routers.xml';
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
        // 1アクションロード済みの場合以降スキップ
        if ($this->load) return;

        // XML解析結果を取得
        switch ($current_path) {
        case '/syl-routers/router':
            if (isset($attribute['enable']) && ($attribute['enable'] === 'false')) {
                return;
            }

            // アクションファイル取得用ルータオブジェクト
            if (preg_match('!^' . $attribute['path'] . '$!i', $this->router->getActionFile(), $matches)) {
                $this->config['actionBaseClass'] = isset($attribute['actionBaseClass']) ? $attribute['actionBaseClass'] : '';
                $this->config['forwardAction']   = isset($attribute['forwardAction'])   ? $attribute['forwardAction']   : '';
                $this->config['forwardTemplate'] = isset($attribute['forwardTemplate']) ? $attribute['forwardTemplate'] : '';
                $this->config['viewClass']       = isset($attribute['viewClass'])       ? $attribute['viewClass']       : '';
                $this->config['layoutName']      = isset($attribute['layoutName'])      ? $attribute['layoutName']      : '';
                $this->config['enableAction']    = isset($attribute['enableAction'])    ? ($attribute['enableAction'] == 'true') : true;
                $this->config['enableTemplate']  = isset($attribute['enableTemplate'])  ? ($attribute['enableTemplate'] == 'true') : true;
                $this->config['match_parameter'] = $matches;
                $this->load = true;
            }
            break;
        }
    }
}
