<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** キャッシュ管理クラス */
require_once 'SyL_CacheManagerAbstract.php';
/** ファイルキャッシュクラス */
require_once 'SyL_CacheEntityFile.php';

/**
 * ファイルキャッシュ管理クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheManagerFile extends SyL_CacheManagerAbstract
{
    /**
     * キャッシュディレクトリ
     *
     * @var string
     */
    private $cache_dir = '';
    /**
     * インクルードキャッシュ使用フラグ
     *
     * @var bool
     */
    private $include_cache = false;
    /**
     * 削除中のロックファイル名
     *
     * @var string
     */
    private $lockname = '.locking';

    /**
     * コンストラクタ
     *
     * @param string キャッシュ保存ディレクトリ
     */
    public function __construct($cache_dir)
    {
        parent::__construct();

        if (is_dir($cache_dir)) {
            if (!is_writable($cache_dir)) {
                throw new SyL_PermissionDeniedException("cache directory permission denied ({$cache_dir})");
            }
        } else {
            if (!mkdir($cache_dir)) {
                throw new SyL_PermissionDeniedException("cache directory can't create ({$cache_dir})");
            }
            chmod($cache_dir, 0777);
        }
        $this->cache_dir = $cache_dir;
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュキー
     * @return SyL_CacheEntityAbstract キャッシュオブジェクト
     */
    public function create($key)
    {
        $cache = new SyL_CacheEntityFile($this->cache_dir, $key);
        if ($this->include_cache) {
            $cache->useIncludeCache();
        }
        $cache->setPrefix($this->prefix);
        $cache->setSuffix($this->suffix);
        $cache->setLifeTime($this->life_time);
        $cache->useCrc($this->is_crc);
        $cache->useSerialize($this->is_serialize);
        return $cache;
    }

    /**
     * インクルードキャッシュを使用する
     */
    public function useIncludeCache()
    {
        $this->include_cache = true;
        $this->useCrc(false);
        $this->useSerialize(true);
    }

    /**
     * 期限切れキャッシュを削除する
     */
    public function clean()
    {
        if ($this->life_time == 0) {
            return;
        }

        $lockfile = "{$this->cache_dir}/{$this->lockname}";
        clearstatcache();
        if (is_file($lockfile)) {
            return;
        }

        $fp = null;
        $now = null;
        try {
            $fp = fopen($lockfile, 'wb');
            flock($fp, LOCK_EX);

            $now = time();
            foreach (scandir($this->cache_dir) as $filename) {
                if (($filename == '.') || ($filename == '..')) {
                    continue;
                }

                $filename = basename($filename);
                if ($this->prefix && $this->suffix) {
                    if (!preg_match('/^(' . preg_quote($this->prefix, '/') . ').+(' . preg_quote($this->suffix, '/') . ')$/', $filename)) {
                        continue;
                    }
                } else if ($this->prefix) {
                    if (!preg_match('/^(' . preg_quote($this->prefix, '/') . ')/', $filename)) {
                        continue;
                    }
                } else if ($this->suffix) {
                    if (!preg_match('/(' . preg_quote($this->suffix, '/') . ')$/', $filename)) {
                        continue;
                    }
                } else if ($filename == $this->lockname) {
                    continue;
                }

                $filename = $this->cache_dir . '/' . $filename;

                clearstatcache();
                if (is_file($filename)) {
                    $mtime = filemtime($filename);
                    // 更新時間の最小値判定
                    if ($this->min_mtime && ($mtime < $this->min_mtime)) {
                        unlink($filename);
                    // 更新時間の最大値判定
                    } else if ($this->max_mtime && ($mtime > $this->max_mtime)) {
                        unlink($filename);
                    // 生存時間の判定
                    } else if ($this->life_time && ($now > ($mtime + $this->life_time))) {
                        unlink($filename);
                    }
                }
            }
            fclose($fp);

            unlink($lockfile);

        } catch (Exception $e) {
            if (is_resource($fp)) {
                fclose($fp);
            }
            if ($now) {
                unlink($lockfile);
            }
            throw new SyL_CacheException($e->getMessage());
        }
    }

    /**
     * キャッシュを全て削除する
     */
    public function cleanAll()
    {
        $lockfile = "{$this->cache_dir}/{$this->lockname}";
        clearstatcache();
        if (is_file($lockfile)) {
            return;
        }

        $fp = null;
        $now = null;
        try {
            $fp = fopen($lockfile, 'wb');
            flock($fp, LOCK_EX);

            $now = time();
            foreach (scandir($this->cache_dir) as $filename) {
                if (($filename == '.') || ($filename == '..')) {
                    continue;
                }
                $filename = basename($filename);
                if ($filename == $this->lockname) {
                    continue;
                }

                $filename = $this->cache_dir . '/' . $filename;
                clearstatcache();
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
            fclose($fp);

            unlink($lockfile);

        } catch (Exception $e) {
            if (is_resource($fp)) {
                fclose($fp);
            }
            if ($now) {
                unlink($lockfile);
            }
            throw new SyL_CacheException($e->getMessage());
        }
    }
}