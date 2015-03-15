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

/** ファイルキャッシュクラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Cache/SyL_CacheEntityFile.php';

/**
 * ファイルキャッシュ格納クラス
 *
 * すでにキャッシュDBが作成されていることが前提。
 * キャッシュDBは、setup.php のプロジェクト作成時に作成される。
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CacheStorageFile extends SyL_CacheStorageAbstract
{
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
        return self::createCache($type, $name, $lifetime)->read();
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
        self::createCache($type, $name, $lifetime)->write($data);
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュタイプ
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return SyL_CacheEntityFile キャッシュオブジェクト
     */
    private static function createCache($type, $name, $lifetime)
    {
        $cache = null;
        switch ($type) {
        case 'app':      $cache = new SyL_CacheEntityFile(SYL_APP_CACHE_DIR . '/app/', $name); break;
        case 'response': $cache = new SyL_CacheEntityFile(SYL_APP_CACHE_DIR . '/response/', $name); break;
        case 'config':   $cache = new SyL_CacheEntityFile(SYL_APP_CACHE_DIR . '/config/', $name); break;
        default: throw new SyL_InvalidParameterException('invalid type parameter');
        }
        $cache->setLifeTime($lifetime);
        return $cache;
    }
}
