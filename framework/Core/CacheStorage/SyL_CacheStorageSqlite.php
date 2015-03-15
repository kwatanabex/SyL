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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/*
CREATE TABLE SYL_CONFIG_CACHE
(
  NAME   TEXT NOT NULL,
  TYPE   TEXT NOT NULL,
  VALUE TEXT,
  TIMESTAMP INTEGER NOT NULL,
  PRIMARY KEY(NAME, PREFIX)
)
*/
/**
 * SQLiteキャッシュ格納クラス
 *
 * すでにキャッシュDBが作成されていることが前提。
 * キャッシュDBは、setup.php のプロジェクト作成時に作成される。
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheStorageSqlite extends SyL_CacheStorageAbstract
{
    /**
     * SQLiteリソース
     * 
     * @var resoure
     */
    private static $conn = null;

    /**
     * キャッシュテーブル名
     * 
     * @var string
     */
    const CONFIG_TANBLE = 'SYL_CONFIG_CACHE';
    /**
     * キャッシュキーカラム名
     * 
     * @var string
     */
    const CONFIG_COLUMN_NAME = 'NAME';
    /**
     * キャッシュのタイプ
     * 
     * @var string
     */
    const CONFIG_COLUMN_TYPE = 'TYPE';
    /**
     * キャッシュ値カラム名
     * 
     * @var string
     */
    const CONFIG_COLUMN_VALUE = 'VALUE';
    /**
     * キャッシュタイムスタンプカラム名
     * 
     * @var string
     */
    const CONFIG_COLUMN_TIMESTAMP = 'TIMESTAMP';

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        parent::__construct();

        $filename = SYL_APP_CACHE_DIR . '/cache.sqlite.db';

        clearstatcache();
        if (is_file($filename)) {
            try {
                $error_message = null;
                self::$conn = sqlite_popen($filename, 0666, $error_message);
                if ($error_message) {
                    throw new Exception($error_message);
                }
            } catch (Exception $e) {
                throw new SyL_CacheStorageException('SQLite cache database cannot open: ' . $e->getMessage());
            }
        } else {
            $this->createDatabase($filename);
        }
    }

    /**
     * Databaseファイルとキャッシュ用テーブルを作成する
     *
     * @param string SQLite Databaseファイル
     */
    private function createDatabase($filename)
    {
        $fp = null;
        $sql = '';
        try {
            $error_message = null;

            self::$conn = sqlite_popen($filename, 0666, $error_message);
            if ($error_message) {
                throw new Exception($error_message);
            }

            $fp = fopen($filename, 'w');
            stream_set_write_buffer($fp, 0);
            flock($fp, LOCK_EX);

            $sql = sprintf("SELECT COUNT(*) AS CNT FROM sqlite_master WHERE type='table' AND name='%s'", self::CONFIG_TANBLE);
            $result = sqlite_single_query(self::$conn, $sql, true);
            if ($result['CNT'] == 0) {
                $sql = sprintf('CREATE TABLE %s (%s TEXT NOT NULL, %s TEXT NOT NULL, %s TEXT, %s INTEGER NOT NULL, PRIMARY KEY(%s, %s))', self::CONFIG_TANBLE, self::CONFIG_COLUMN_NAME, self::CONFIG_COLUMN_TYPE, self::CONFIG_COLUMN_VALUE, self::CONFIG_COLUMN_TIMESTAMP, self::CONFIG_COLUMN_NAME, self::CONFIG_COLUMN_TYPE);
                sqlite_exec(self::$conn, $sql, $error_message);
                if ($error_message) {
                    throw new Exception($error_message);
                }
            }
            flock($fp, LOCK_UN);
            fclose($fp);
            $fp = null;
        } catch (Exception $e) {
            if (is_resource($fp)) {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            SyL_Logger::error("sqlite cache SQL: {$sql}");
            throw new SyL_CacheStorageException('create table failed: ' . $e->getMessage());
        }
    }

    /**
     * キャッシュを取得する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return mixed キャッシュ値
     */
    protected function getCache($type, $name, $lifetime)
    {
        $result = null;
        $sql = '';
        try {
            $sql = sprintf("SELECT %s, %s FROM %s WHERE %s = '%s' AND %s = '%s'", self::CONFIG_COLUMN_VALUE, self::CONFIG_COLUMN_TIMESTAMP, self::CONFIG_TANBLE, self::CONFIG_COLUMN_NAME, sqlite_escape_string($name), self::CONFIG_COLUMN_TYPE, $type);
            $error_message = null;
            $result = sqlite_single_query(self::$conn, $sql, SQLITE_ASSOC, true);
            if ($result === false) {
                throw new Exception(sqlite_error_string(sqlite_last_error(self::$conn)));
            }
        } catch (Exception $e) {
            SyL_Logger::error("sqlite cache SQL: {$sql}");
            throw new SyL_CacheStorageExecuteException('SQLite cache database query failed (' . $e->getMessage() . ')');
        }

        if (count($result) == 0) {
            throw new SyL_CacheNotFoundException("sqlite cache not found ({$name})");
        }
        if ((float)$result[self::CONFIG_COLUMN_TIMESTAMP] + $lifetime < time()) {
            throw new SyL_CacheExpiredException("sqlite cache expired ({$name})");
        }

        return $result[self::CONFIG_COLUMN_VALUE];
    }

    /**
     * アプリケーションキャッシュを保存する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     */
    protected function setCache($type, $name, $data, $lifetime)
    {
        $error_message = null;
        sqlite_exec(self::$conn, 'BEGIN', $error_message);
        if ($error_message != null) {
            throw new SyL_CacheStorageExecuteException("SQLite cache database `BEGIN' query failed ({$error_message})");
        }

        $sql = '';
        try {
            $sql = sprintf("INSERT OR REPLACE INTO %s (%s, %s, %s, %s) VALUES ('%s', '%s', '%s', %s)", self::CONFIG_TANBLE, self::CONFIG_COLUMN_NAME, self::CONFIG_COLUMN_TYPE, self::CONFIG_COLUMN_VALUE, self::CONFIG_COLUMN_TIMESTAMP, sqlite_escape_string($name), $type, sqlite_escape_string(serialize($data)), time());
            sqlite_exec(self::$conn, $sql, $error_message);
            if ($error_message != null) {
                throw new Exception($error_message);
            }
            sqlite_exec(self::$conn, 'COMMIT');
        } catch (Exception $e) {
            sqlite_exec(self::$conn, 'ROLLBACK');
            SyL_Logger::error("sqlite cache SQL: {$sql}");
            throw new SyL_CacheStorageExecuteException('SQLite cache database query failed (' . $e->getMessage() . ')');
        }
    }
}
