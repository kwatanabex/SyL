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
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id: $
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** SyL 汎用例外クラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Exception/SyL_Exception.php';
/** SyLフレームワーク基準のPHPファイルロードクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_Loader.php';
/** 設定値保持クラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Config/SyL_Config.php';
/** 設定ファイル取得クラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Config/SyL_ConfigFileAbstract.php';
/** コンテナクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Container/SyL_Container.php';
/** エラーハンドラクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/ErrorHandler/SyL_ErrorHandlerAbstract.php';
/** キャッシュ格納クラス */
require_once SYL_FRAMEWORK_DIR . '/Core/CacheStorage/SyL_CacheStorageAbstract.php';

/**
 * フレームワークイベントトリガクラス
 *
 * 全体の処理の流れを管理する
 * 主な内容は
 *   ・DIコンテナの保持
 *   ・処理の流れに伴う遷移イベント処理
 *
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
class SyL_EventDispatcher
{
    /**
     * コンテナオブジェクト
     *
     * @var SyL_Container
     */
    private static $container = null;
    /**
     * 計測値
     *
     * @var float
     */
    private static $current_time = null;
    /**
     * 終了時ハンドラメソッド名
     *
     * @var string
     */
    const DESTORY_HANDLER_METHOD = 'finalStream';

    /**
     * コンストラクタ
     */
    private function __construct()
    {
        self::$container = new SyL_Container();

        register_shutdown_function(array($this, self::DESTORY_HANDLER_METHOD));

        if (SyL_Logger::getMode() == SYL_LOG_DEBUG) {
            // リクエスト初期ロギング
            SyL_Logger::debug('startup EventDispatcher="' . SYL_APP_TYPE . '"');
            SyL_Logger::debug('project info: SYL_DIR="' . SYL_DIR . '" project_dir="' . SYL_PROJECT_DIR . '", app_name="' . SYL_APP_NAME . '"');

            self::$current_time = microtime(true);
            SyL_Logger::debug('start processing timer');
        }
    }

    /**
     * イベントトリガを起動する
     *
     * @param array 起動パラメータ配列
     * @return SyL_EventDispatcher イベント主体オブジェクト
     */
    public static function startup(array $config)
    {
        if (empty($config['project_dir'])) {
            throw new SyL_InvalidParameterException("startup parameter `project_dir' not found");
        } else if (!is_dir($config['project_dir'])) {
            throw new SyL_InvalidParameterException("startup parameter `project_dir' directory not found ({$config['project_dir']})");
        }
        if (empty($config['app_name'])) {
            throw new SyL_InvalidParameterException("startup parameter `app_name' not found");
        }

        if (isset($config['env'])) {
            switch ($config['env']) {
            case 'production':
            case 'staging':
            case 'development':
                break;
            default:
                throw new SyL_InvalidParameterException("invalid startup parameter `env'. production or staging or development");
            }
        } else {
            $config['env'] = null;
        }

        $type = null;
        if (empty($config['type'])) {
            if ((PHP_SAPI == 'cgi') && !isset($_SERVER['REQUEST_METHOD'])) {
                throw new SyL_InvalidOperationException('command line SAPI is CLI only');
            } else if (PHP_SAPI == 'cli') {
                $type = SyL_AppType::COMMAND;
            } else {
                $type = SyL_AppType::WEB;
            }
        } else {
            $type = ucfirst($config['type']);
        }

        if (isset($config['cache']) && $config['cache']) {
            switch ($config['cache']) {
            case 'file':
            case 'sqlite':
            case 'memcache':
                break;
            default:
                throw new SyL_InvalidParameterException("invalid startup parameter `cache'. file or sqlite or memcache");
            }
        } else {
            $config['cache'] = null;
        }

        if (!isset($config['log'])) {
            $config['log'] = SYL_LOG_WARN;
        }

        // 実行環境定義
        define('SYL_ENV', $config['env']);

        // アプリケーションタイプ定義
        define('SYL_APP_TYPE', $type);
        // アプリケーション名定義
        define('SYL_APP_NAME', $config['app_name']);
        // アプリケーションキャッシュ使用の有無
        // 設定ファイルキャッシュ使用の有無
        define('SYL_CACHE', $config['cache']);

        // プロジェクトディレクトリ定義
        define('SYL_PROJECT_DIR', $config['project_dir']);
        // プロジェクト設定ファイルディレクトリ定義
        define('SYL_PROJECT_CONFIG_DIR',  SYL_PROJECT_DIR . '/config');
        // プロジェクトライブラリディレクトリ定義
        define('SYL_PROJECT_LIB_DIR',  SYL_PROJECT_DIR . '/lib');
        // アプリケーションディレクトリ定義
        define('SYL_APP_DIR', SYL_PROJECT_DIR . '/apps/' . SYL_APP_NAME);
        // アプリケーション設定ファイルディレクトリ定義
        define('SYL_APP_CONFIG_DIR', SYL_APP_DIR . '/config');
        // アプリケーションライブラリディレクトリ定義
        define('SYL_APP_LIB_DIR', SYL_APP_DIR . '/lib');
        // アプリケーションキャッシュディレクトリ定義
        define('SYL_APP_CACHE_DIR', SYL_PROJECT_DIR . '/var/cache/' . SYL_APP_NAME);
        $log_dir = '';
        if (isset($config['log_dir'])) {
            $log_dir = $config['log_dir'];
        } else {
            $log_dir = SYL_PROJECT_DIR . '/var/logs/' . SYL_APP_NAME;
        }
        // アプリケーションログ出力ディレクトリ定義
        define('SYL_APP_LOG_DIR', $log_dir);

        // PHPシステムエラーログの保存
        if (isset($config['syslog']) && $config['syslog']) {
            ini_set('log_errors', true);
            if (!ini_get('error_log')) {
                $log_file = isset($config['syslog_dir']) ? sprintf('%s/phperror_%s.log', $config['syslog_dir'], date('Ymd'))
                                                         : sprintf('%s/var/syslogs/%s/phperror_%s.log', SYL_PROJECT_DIR, SYL_APP_NAME, date('Ymd'));
                ini_set('error_log', $log_file);
            }
        }

        // ロガー初期化
        SyL_Logger::startup($config['log']);
        // キャッシュ初期化
        if (SYL_CACHE) {
            SyL_CacheStorageAbstract::getInstance()->readConfigCache();
        }
        // フレームワークカスタムクラスのロード
        SyL_ConfigFileAbstract::createInstance('classes')->parse();
        // フレームワークイベントトリガオブジェクト
        $dispatcher = new SyL_EventDispatcher();
        // エラーハンドラ初期化
        SyL_ErrorHandlerAbstract::startup($dispatcher);
        // 設定値のロード
        SyL_ConfigFileAbstract::createInstance('defines')->parse();

        return $dispatcher;
    }

    /**
     * 実行環境ごとのフロー制御
     */
    public function run()
    {
        // 初期化処理
        $this->initStream();
        // アクション実行前
        $this->loadStream();
        // アクション実行処理
        $this->executeStream();
        // ビュー表示実行前処理
        $this->middleStream();
        // ビュー表示処理
        $this->renderStream();
        // ビュー表示実行後処理
        $this->unloadStream();
    }

    /**
     * 初期化処理
     */
    protected function initStream()
    {
        $this->raiseEvent('initStream', 'components');
    }

    /**
     * アクション前処理実行
     */
    protected function loadStream()
    {
        $this->raiseEvent('loadStream', 'filters');
    }

    /**
     * アクション処理実行
     */
    protected function executeStream()
    {
        $this->raiseEvent('executeStream', 'actions');
    }

    /**
     * ビュー表示実行前処理
     */
    protected function middleStream()
    {
        $this->raiseEvent('middleStream');
    }

    /**
     * ビュー表示処理
     */
    protected function renderStream()
    {
        $this->raiseEvent('renderStream');
    }

    /**
     * ビュー表示実行後処理
     */
    protected function unloadStream()
    {
        $this->raiseEvent('unloadStream');

        // キャッシュの保存
        if (SYL_CACHE) {
            $cache_storage = SyL_CacheStorageAbstract::getInstance();
            $cache_storage->saveConfigCache();
        }
    }

    /**
     * 最終処理
     * ※ register_shutdown_function 使用
     */
    public function finalStream()
    {
        if (SyL_Logger::getMode() == SYL_LOG_DEBUG) {
            SyL_Logger::debug('memory status: current="' . number_format(floor(memory_get_usage()/1024)) . 'KB" max="' . number_format(floor(memory_get_peak_usage()/1024)) . 'KB"');
            if (function_exists('sys_getloadavg')) {
                SyL_Logger::debug('load average: ' . implode(', ', sys_getloadavg()));
            }
            self::watchTimestamp();
        }

        switch (connection_status()) {
        case CONNECTION_ABORTED:
            SyL_Logger::info('connection aborted');
            break;
        case CONNECTION_TIMEOUT:
            SyL_Logger::warn('connection timeout (max_execution_time: ' . ini_get('max_execution_time') . ')');
            break;
        }

        try {
            $this->raiseEvent('finalStream');
        } catch (Exception $e) {
            $error_message = get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
            echo $error_message;
            SyL_Logger::error($error_message);
        }

        SyL_ErrorHandlerAbstract::shutdown();
        SyL_Logger::shutdown();
    }

    /**
     * エラー処理
     */
    public function errorStream()
    {
        $this->raiseEvent('errorStream');
    }

    /**
     * イベント実行
     *
     * @param string イベントメソッド名
     * @param string 設定ファイル名
     */
    protected function raiseEvent($event, $config_type='')
    {
        self::$container->raiseEvent($event, $config_type);
    }

    /**
     * カレントの計測値をログに記録する
     */
    private static function watchTimestamp()
    {
        $progress = round(microtime(true) - self::$current_time, 3);
        SyL_Logger::debug('watch processing timer : ' . $progress . ' [s]');
    }

    /**
     * コンポーネントを取得
     *
     * @param string コンポーネント名
     * @return SyL_ContainerComponentInterface コンポーネントオブジェクト
     */
    public function getComponent($name)
    {
        return self::$container->getComponent($name);
    }
}
