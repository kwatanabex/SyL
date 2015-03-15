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

/** キャッシュクラス */
require_once 'SyL_CacheEntityAbstract.php';

/**
 * ファイルキャッシュクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheEntityFile extends SyL_CacheEntityAbstract
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
     * コンストラクタ
     *
     * @param string キャッシュディレクトリ
     * @param string キャッシュキー
     * @throws SyL_PermissionDeniedException ディレクトリに書き込み権限が無いか、ディクトリが存在しないときにディレクトリが作成できない場合
     */
    public function __construct($cache_dir, $key)
    {
        parent::__construct($key);

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
     * キャッシュファイル名を取得する
     * 
     * @return string キャッシュファイル名
     */
    public function getFileName()
    {
        return $this->cache_dir . '/' . $this->getKey();
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
     * キャッシュの更新時間を更新する
     *
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheException キャッシュ更新時例外
     */
    public function updateCacheTime()
    {
        $filename = $this->getFileName();
        clearstatcache();
        if (!is_file($filename)) {
            throw new SyL_CacheNotFoundException("cache file not found ({$filename})");
        }
        try {
            touch($filename);
        } catch (Exception $e) {
            throw new SyL_CacheException($e->getMessage());
        }
    }

    /**
     * キャッシュを読み込む
     *
     * @return mixed キャッシュデータ
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheInvalidHashException キャッシュのハッシュ値が一致しない場合
     * @throws SyL_CacheException キャッシュ読み込み時例外
     */
    public function read()
    {
        $filename = $this->getFileName();
        clearstatcache();
        if (!is_file($filename)) {
            throw new SyL_CacheNotFoundException("cache file not found ({$filename})");
        }

        $mtime = filemtime($filename);

        // キャッシュ有効期間切れ判定
        if ($this->life_time > 0) {
            $life_time = $mtime + $this->life_time;
            if ($life_time < time()) {
                $this->remove();
                throw new SyL_CacheNotFoundException("cache expired ({$filename})");
            }
        }

        if ($this->include_cache) {
            try {
                $data = include $filename;
            } catch (Exception $e) {
                throw new SyL_CacheNotFoundException($e->getMessage());
            }
        } else {
            $fp = null;
            try {
                $fp = fopen($filename, 'rb');
                flock($fp, LOCK_SH);

                clearstatcache();
                if (!is_file($filename)) {
                    throw new SyL_CacheNotFoundException("cache file not found ({$filename})");
                }

                $size = filesize($filename);
                if ($this->is_crc) {
                    $hash = fread($fp, 32);
                    $data = ($size > 32) ? fread($fp, $size-32) : '';
                    $rhash = $this->getCrc($data);
                    if ($hash != $rhash) {
                        throw new SyL_CacheInvalidHashException("invalid hash (expected: {$hash} - actual: {$rhash})");
                    }
                } else {
                    $data = fread($fp, $size);
                }
                flock($fp, LOCK_UN);
                fclose($fp);
            } catch (SyL_CacheInvalidHashException $e) {
                if (is_resource($fp)) {
                    flock($fp, LOCK_UN); 
                    fclose($fp);
                }
                $this->remove();
                throw $e;
            } catch (Exception $e) {
                if (is_resource($fp)) {
                    flock($fp, LOCK_UN);
                    fclose($fp);
                }
                throw new SyL_CacheException($e->getMessage());
            }

            if ($this->is_serialize) {
                $data = unserialize($data);
            }
        }

        return $data;
    }

    /**
     * キャッシュを保存する
     *
     * @param mixed キャッシュデータ
     * @throws SyL_CacheException キャッシュ保存時例外
     */
    public function write($data)
    {
        if ($this->include_cache) {
            if ($this->is_serialize) {
                $data = var_export($data, true);
                $data = "return {$data};";
            }
            if (substr($data, 0, 2) != '<?') {
                $data = "<?php\n" . $data;
            }
            if (substr($data, -2) != '?>') {
                $data .= "\n?>\n";
            }
        } else {
            if ($this->is_serialize) {
                $data = serialize($data);
            }
        }
        $size = strlen($data);

        $filename = $this->getFileName();
        $fp = null;
        try {
            $fp = fopen($filename, 'wb');
            stream_set_write_buffer($fp, 0);
            flock($fp, LOCK_EX);

            if ($this->is_crc) {
                fwrite($fp, $this->getCrc($data), 32);
            }
            fwrite($fp, $data, $size);
            chmod($filename, 0777);
            flock($fp, LOCK_UN); 
            fclose($fp);
        } catch (Exception $e) {
            if (is_resource($fp)) {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            throw new SyL_CacheException($e->getMessage());
        }
    }

    /**
     * キャッシュを削除する
     *
     * @throws SyL_CacheException キャッシュ削除時例外
     */
    public function remove()
    {
        $filename = $this->getFileName();
        $fp = null;
        try {
            $fp = fopen($filename, 'wb');
            stream_set_write_buffer($fp, 0);
            flock($fp, LOCK_EX);

            clearstatcache();
            if (is_file($filename)) {
                unlink($filename);
            }
            flock($fp, LOCK_UN); 
            fclose($fp);
        } catch (Exception $e) {
            if (is_resource($fp)) {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            throw new SyL_CacheException($e->getMessage());
        }
    }
}