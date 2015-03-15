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
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** キャッシュ格納例外クラス */
require_once 'SyL_CacheStorageException.php';
/** キャッシュ例外クラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Cache/SyL_CacheException.php';

/**
 * キャッシュ格納クラス
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_CacheStorageAbstract
{
    /**
     * 設定ファイルのキャッシュのキー
     * 
     * @var string
     */
    private static $config_cache_name = null;
    /**
     * 設定ファイルのキャッシュ
     * 
     * @var array
     */
    private static $config_caches = array();
    /**
     * 設定ファイルのキャッシュ更新フラグ
     * 
     * @var bool
     */
    private static $config_cache_update = false;
    /**
     * アプリケーションキャッシュ
     * 
     * @var array
     */
    private static $app_caches = array();
    /**
     * 設定ファイルのキャッシュ有効時間
     * 
     * @var int
     */
    const CONFIG_CACHE_TIME = 86400;
    /**
     * デフォルトアプリケーションキャッシュ有効時間
     * 
     * @var int
     */
    const APP_CACHE_TIME = 3600;
    /**
     * デフォルトレスポンスキャッシュ有効時間
     * 
     * @var int
     */
    const RESPONSE_CACHE_TIME = 3600;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        self::$config_cache_name = $_SERVER['PHP_SELF'];
    }

    /**
     * キャッシュ格納オブジェクトを取得する
     *
     * @return SyL_CacheStorage キャッシュ格納オブジェクト
     */
    public static function getInstance()
    {
        static $singleton = null;
        if ($singleton == null) {
            switch (SYL_CACHE) {
            case 'file':
                include_once 'SyL_CacheStorageFile.php';
                $singleton = new SyL_CacheStorageFile();
                break;
            case 'sqlite':
                include_once 'SyL_CacheStorageSqlite.php';
                $singleton = new SyL_CacheStorageSqlite();
                break;
            case 'memcache':
                include_once 'SyL_CacheStorageMemcache.php';
                $singleton = new SyL_CacheStorageMemcache();
                break;
            default:
                throw new SyL_InvalidParameterException("invalid constant `SYL_CACHE' (" . SYL_CACHE . ")");
            }
        }
        return $singleton;
    }

    /**
     * キャッシュを取得する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return mixed キャッシュ値
     */
    protected abstract function getCache($type, $name, $lifetime);

    /**
     * アプリケーションキャッシュを保存する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     */
    protected abstract function setCache($type, $name, $data, $lifetime);

    /**
     * アプリケーションキャッシュを取得する
     *
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return mixed アプリケーションキャッシュ値
     */
    public function getAppCache($name, $lifetime=self::APP_CACHE_TIME)
    {
        if (array_key_exists($name, self::$app_caches)) {
            return self::$app_caches[$name];
        }

        try {
            self::$app_caches[$name] = $this->getCache('app', $name, $lifetime);
            return self::$app_caches[$name];
        } catch (SyL_CacheException $e) {
            return null;
        }
    }

    /**
     * アプリケーションキャッシュを保存する
     *
     * @param string キャッシュのキー
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     */
    public function setAppCache($name, $data, $lifetime=self::APP_CACHE_TIME)
    {
        $this->setCache('app', $name, $data, $lifetime);
        self::$app_caches[$name] = $data;
    }

    /**
     * レスポンスキャッシュを取得する
     *
     * @param int キャッシュのライフタイム
     * @return mixed レスポンスキャッシュ
     */
    public function getResponseCache($lifetime=self::RESPONSE_CACHE_TIME)
    {
        try {
            return $this->getCache('response', self::$config_cache_name, $lifetime);
        } catch (SyL_CacheException $e) {
            return null;
        }
    }

    /**
     * レスポンスキャッシュを保存する
     *
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     */
    public function setResponseCache($data, $lifetime=self::RESPONSE_CACHE_TIME)
    {
        $this->setCache('response', self::$config_cache_name, $data, $lifetime);
    }

    /**
     * 設定ファイルのキャッシュ情報を取得する
     *
     * @param string キャッシュのキー
     * @param array 設定ファイル名の配列
     * @return mixed 設定ファイルのキャッシュ値
     * @throws SyL_CacheStorageNotFoundException 有効なキャッシュでない場合
     */
    public function getConfigCache($name, array $file_names)
    {
        $mtime = $this->getModifyFileTime($file_names);
        if ($mtime == 0) {
            throw new SyL_FileNotFoundException('original config file not found (' . implode(', ', $file_names) . ')');
        }

        // キャッシュ判定
        if (isset(self::$config_caches[$name])) {
            if ((float)self::$config_caches[$name]['mtime'] >= $mtime) {
                // キャッシュから取得
                return self::$config_caches[$name]['content'];
            }
        }
        throw new SyL_CacheStorageNotFoundException("cache not found ({$name})");
    }

    /**
     * 設定ファイルのキャッシュ情報を更新する
     *
     * @param string キャッシュのキー
     * @param string 設定ファイルキャッシュ値
     * @param array 設定ファイル名の配列
     */
    public function updateConfigCache($name, $value, array $file_names)
    {
        $mtime = $this->getModifyFileTime($file_names);
        if ($mtime == 0) {
            throw new SyL_FileNotFoundException('original config file not found');
        }

        self::$config_caches[$name] = array(
            'mtime'   => $mtime,
            'content' => $value
        );
        self::$config_cache_update = true;
    }

    /**
     * 設定ファイルの更新時間を取得する
     *
     * @param string キャッシュのキー
     * @return float 設定ファイルの更新時間
     */
    private function getModifyFileTime($file_names)
    {
        $mtime = 0;
        foreach ($file_names as $file_name) {
            $mtime_tmp = filemtime($file_name);
            if ($mtime < $mtime_tmp) {
                $mtime = $mtime_tmp;
            }
        }
        return (float)$mtime;
    }

    /**
     * 設定ファイルのキャッシュ情報を読み込む
     */
    public function readConfigCache()
    {
        try {
            self::$config_caches = $this->getCache('config', self::$config_cache_name, self::CONFIG_CACHE_TIME);
        } catch (SyL_CacheException $e) {}
    }

    /**
     * 設定ファイルのキャッシュ情報を保存する
     */
    public function saveConfigCache()
    {
        if (self::$config_cache_update) {
             $this->setCache('config', self::$config_cache_name, self::$config_caches, self::CONFIG_CACHE_TIME);
        }
    }
}
