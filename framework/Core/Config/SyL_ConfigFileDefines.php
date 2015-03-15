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

/** 特定の文字列を定数値に変換するクラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Util/SyL_UtilReplaceConstant.php';

/**
 * 設定情報取得クラス
 *
 * フレームワーク起動時に、設定ファイルのデータを SyL_Config に登録する。
 *
 * 設定ファイルから、
 *   <define name="キー値">値</define>
 * を取得して、SyL_Config オブジェクトにセットする。
 *
 * 物理的には、以下のファイルから取得する
 *   (1) SYL_APP_CONFIG_DIR / defines.xml
 *   (2) SYL_PROJECT_CONFIG_DIR / defines.xml
 *
 * ファイルは上述の順番に読み込まれ、同じキー値が存在した場合は、
 * 先に読み込まれたほうが有効になる。
 *
 * キー値に使用できる文字は、[a-zA-Z0-9_]+
 *
 * {$foo} が値に含まれると、定義済みの定数値に変換される。
 * 例えば、
 *   project_dir={SYL_APP_NAME}
 *   -> project_dir=test
 *
 * キャッシュが有効な場合、定数変換はキャッシュに保存される前に行われるので、
 * もしキャッシュ保存後、定数値が変更になった場合は、キャッシュをクリアする必要がある。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileDefines extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'defines.xml';

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
        // プロジェクト設定値を読み込み定数化
        $this->file_names[] = SYL_PROJECT_CONFIG_DIR . '/' . $this->config_file_name;
    }

    /**
     * XMLファイルの解析処理
     *
     * @param bool キャッシュ使用フラグ
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

        // 定数一括変換＆リード
        self::readConstants($this->config);
        
        SyL_Config::loadComplete();
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
        $match = false;
        switch ($current_path) {
        case '/syl-defines/application/define':
            if (substr($attribute['name'], 0, 4) == 'SYL_') {
                throw new SyL_InvalidConfigException("defines.xml format error. invalid prefix name `SYL_' @ application define area ({$attribute['name']})");
            }
            $match = true;
            break;

        case '/syl-defines/framework/define':
            if (substr($attribute['name'], 0, 4) != 'SYL_') {
                throw new SyL_InvalidConfigException("defines.xml format error. framework define area prefix name `SYL_' only ({$attribute['name']})");
            }
            $match = true;
            break;
        }

        if ($match) {
            if (isset($attribute['env']) && $attribute['env']) {
                if (SYL_ENV != $attribute['env']) {
                    return;
                }
            }
            $format = isset($attribute['format']) ? $attribute['format'] : '';
            $this->config[$format . ':' . $attribute['name']] = $text;
        }
    }

    /**
     * 設定値を SyL_Config クラスにセットする
     *
     * @param array 設定値配列
     */
    private static function readConstants(array $config)
    {
        foreach ($config as $key => $value) {
            $value = SyL_UtilReplaceConstant::replace($value);
            list($format, $key) = explode(':', $key);
            SyL_Config::set($key, self::convertFormat($value, $format));
        }
    }

    /**
     * 設定値を指定型に変換する
     *
     * @param string 変換前の値
     * @param string フォーマット名
     * @return mixed 指定型の値
     */
    private static function convertFormat($value, $format)
    {
        switch ($format) {
        case 'bool':
            switch (strtolower($value)) {
            case 'true':
            case '1':
                return true;
            default:
                return false;
            }
        case 'int':
            return intval($value);
        case 'float':
            return floatval($value);
        default:
            return $value;
        }
    }
}
