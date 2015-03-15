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
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * SyLフレームワーク基準のPHPファイルロードクラス
 *
 * SyLフレームワーク基準のロード名からファイルとクラス
 * を検索してロードする。
 * 具体的なロード名の命名ルールは下記の通り。
 * 
 * [type]:[class]@[prefix]
 *   ※ type、prefix は省略可
 *
 * 例） ロード名: SyL_Loader::core('Data.Web@SyL_')
 *   -> ロードファイル: SYL_FRAMEWORK_DIR /Core/Data/SyL_DataWeb.php
 *   -> ロードクラス名: SyL_DataWeb
 *
 * 例） ロード名: SyL_Loader::lib('Cache.Abstract@SyL_')
 *   -> ロードファイル: SYL_FRAMEWORK_DIR /Lib/Cache/SyL_CacheAbstract.php
 *   -> ロードクラス名: SyL_CacheAbstract
 *
 * 例） ロード名: SyL_Loader::read('lib:Cache.Abstract@SyL_')
 *   -> ロードファイル: SYL_FRAMEWORK_DIR /Lib/Cache/SyL_CacheAbstract.php
 *   -> ロードクラス名: SyL_CacheAbstract
 *
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
class SyL_Loader
{
    /**
     * フレームワークコアディレクトリからPHPファイルをロードする
     * 
     * フレームワークコアディレクトリは、SYL_FRAMEWORK_DIR /Core 以下。
     * 読み込むディレクトリが決まっているので、ロード名のタイプは無視される。
     * フレームワーク接頭辞は固定なので省略可能。
     *
     * @param string ロード名
     * @return string ロードしたクラス名
     */
    public static function core($name)
    {
        $name = self::addFrameworkPrefix($name);
        $path = SYL_FRAMEWORK_DIR . '/Core' . self::convertPath($name);
        self::read($path);
        return self::convertClass($name);
    }

    /**
     * フレームワークライブラリディレクトリからPHPファイルをロードする
     * 
     * フレームワークライブラリディレクトリは、SYL_FRAMEWORK_DIR /Lib 以下
     * 読み込むディレクトリが決まっているので、ロード名のタイプは無視される。
     *
     * @param string ロード名
     * @return string ロードしたクラス名
     */
    public static function lib($name)
    {
        $name = self::addFrameworkPrefix($name);
        $path = SYL_FRAMEWORK_DIR . '/Lib' . self::convertPath($name);
        self::read($path);
        return self::convertClass($name);
    }

    /**
     * ユーザーライブラリディレクトリからPHPファイルをロードする
     *
     * ユーザーライブラリディレクトリは、
     *   1. ユーザーアプリケーションライブラリディレクトリ
     *   2. プロジェクトライブラリディレクトリ
     * の順で検索される。
     * 読み込むディレクトリが決まっているので、ロード名のタイプは無視される。
     *
     * @param string ロード名
     * @param string 拡張子
     * @return string ロードしたクラス名
     * @throws SyL_FileNotFoundException クラスファイルが見つからない場合
     */
    public static function userLib($name, $ext='.php')
    {
        $path = self::convertPath($name, $ext);

        // アプリケーションライブラリディレクトリ
        $base1 = SYL_APP_LIB_DIR . $path;
        try {
            self::read($base1);
            return self::convertClass($name);
        } catch (SyL_FileNotFoundException $e) {}

        // プロジェクトライブラリディレクトリ
        $base2 = SYL_PROJECT_LIB_DIR . $path;
        try {
            self::read($base2);
            return self::convertClass($name);
        } catch (SyL_FileNotFoundException $e) {}

        throw new SyL_FileNotFoundException("loader failed. user library file not found ({$base1} or {$base2})");
    }

    /**
     * SyLフレームワークに基づくPHPファイルをロードする
     *
     * ユーザーライブラリディレクトリ、フレームワークライブラリディレクトリ、
     * フレームワークコアディレクトリ の3つのディレクトリを検索（同ロード順）しロードする。
     * 
     * タイプは下記の通り
     *   userLib - ユーザーライブラリディレクトリ
     *   lib     - フレームワークライブラリディレクトリ
     *   core    - フレームワークコアディレクトリ
     * ※カンマ区切りで複数指定可能。複数指定された場合は、指定順に検索する。
     *
     * @param string ロード名
     * @param string 拡張子
     * @return string ロードしたクラス名
     * @throws SyL_InvalidParameterException ロード名のタイプが指定外の場合
     * @throws SyL_FileNotFoundException クラスファイルが見つからない場合
     */
    public static function load($name, $ext='.php')
    {
        list($types, $path, $prefix) = self::analyzeName($name);
        if ($types) {
            $types = array($types);
        } else {
            $types = array('userLib', 'lib', 'core');
        }
        foreach ($types as $type) {
            switch ($type) {
            case 'userLib':
            case 'lib':
            case 'core':
                try {
                    return self::$type($name, $ext);
                } catch (SyL_FileNotFoundException $e) {}
                break;
            default:
                throw new SyL_InvalidParameterException("invalid load name parameter ({$types})");
            }
        }

        throw new SyL_FileNotFoundException("loader failed (type: {$types} path: {$path} prefix: {$prefix})");
    }

    /**
     * ディレクトリからPHPファイルをロードする
     * 
     * @param string ロードファイルパス
     * @throws SyL_FileNotFoundException クラスファイルが見つからない場合
     */
    private static function read($path)
    {
        if (is_readable($path)) {
            include_once $path;
        } else {
            throw new SyL_FileNotFoundException("loader failed. loading file not found or permission denied ({$path})");
        }
    }

    /**
     * ロード名からロードファイルパスに変換する
     * 
     * @param string ロード名
     * @param string 拡張子
     * @return string ロードファイルパス
     */
    public static function convertPath($name, $ext='.php')
    {
        $classname = self::convertClass($name);
        list($type, $path, $prefix) = self::analyzeName($name);

        $path = array_map('ucfirst', explode('.', $path));
        array_pop($path); // ファイルを削除
        $path = implode('/', $path);
        if ($path == '') {
            return "/{$classname}{$ext}";
        } else {
            return "/{$path}/{$classname}{$ext}";
        }
    }

    /**
     * ロード名からクラス名を取得する
     * 
     * @param string ロード名
     * @return string クラス名
     */
    public static function convertClass($name)
    {
        list($type, $path, $prefix) = self::analyzeName($name);
        return $prefix . implode('', array_map('ucfirst', explode('.', $path)));
    }

    /**
     * ロード名にフレームワーク接頭辞を追加する
     *
     * @param string ロード名
     * @return string SyL接頭辞を追加したロード名
     */
    private static function addFrameworkPrefix($name)
    {
        $pos = strrpos($name, '@');
        if ($pos !== false) {
            $name = substr($name, 0, $pos);
        }
        $name .= '@SyL_';
        return $name;
    }

    /**
     * ロード名を解析して、[type]、[class]、[prefix] に分解する
     * 
     * @param string ロード名
     * @return array (タイプ,クラス,接頭辞)
     */
    private static function analyzeName($name)
    {
        $type   = '';
        $path   = '';
        $prefix = '';

        $names = explode(':', $name, 2);
        if (isset($names[1])) {
            $type = $names[0];
            $path = $names[1];
        } else {
            $path = $names[0];
        }
        $names = explode('@', $path, 2);
        if (isset($names[1])) {
            $prefix = $names[1];
        }
        $path = $names[0];

        return array($type, $path, $prefix);
    }
}
