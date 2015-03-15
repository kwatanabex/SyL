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

/** フレームワークカスタムクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_CustomClass.php';

/**
 * フレームワークカスタムクラス取得クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileClasses extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'classes.xml';
    /**
     * データクラスのキー
     * 
     * @var string
     */
    const DATA_CLASS = 'data';
    /**
     * エラーハンドラクラスのキー
     * 
     * @var string
     */
    const ERROR_HANDLER_CLASS = 'error-handler';
    /**
     * リクエストクラスのキー
     * 
     * @var string
     */
    const REQUEST_CLASS = 'request';
    /**
     * レスポンスクラスのキー
     * 
     * @var string
     */
    const RESPONSE_CLASS = 'response';
    /**
     * ルータクラスのキー
     * 
     * @var string
     */
    const ROUTER_CLASS = 'router';
    /**
     * セッションクラスのキー
     * 
     * @var string
     */
    const SESSION_CLASS = 'session';
    /**
     * ユーザークラスのキー
     * 
     * @var string
     */
    const USER_CLASS = 'user';

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected function initializeConfigFiles()
    {
        // アプリケーション設定値を読み込み定数化
        $config = SYL_APP_CONFIG_DIR . '/' . $this->config_file_name;
        if (is_file($config)) {
            $this->file_names[] = $config;
        }
    }

    /**
     * XMLファイルの解析処理
     *
     * @param bool キャッシュ使用フラグ
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

        SyL_CustomClass::initialize($this->config);
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
        switch ($current_path) {
        case '/syl-classes/data':
            $this->config[self::DATA_CLASS] = $text;
            break;
        case '/syl-classes/error-handler':
            $this->config[self::ERROR_HANDLER_CLASS] = $text;
            break;
        case '/syl-classes/request':
            $this->config[self::REQUEST_CLASS] = $text;
            break;
        case '/syl-classes/response':
            $this->config[self::RESPONSE_CLASS] = $text;
            break;
        case '/syl-classes/router':
            $this->config[self::ROUTER_CLASS] = $text;
            break;
        case '/syl-classes/session':
            $this->config[self::SESSION_CLASS] = $text;
            break;
        case '/syl-classes/user':
            $this->config[self::USER_CLASS] = $text;
            break;
        }
    }
}
