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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * DAO取得クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ConfigFileDao extends SyL_ConfigFileAbstract
{
    /**
     * 設定ファイル名
     * 
     * @var string
     */
     protected $config_file_name = 'dao.xml';

    /**
     * 設定ファイルを初期化する
     *
     * 設定ファイルは配列として複数指定可能。
     */
    protected function initializeConfigFiles()
    {
        // アプリケーション設定値を読み込み定数化
        $config = SYL_PROJECT_CONFIG_DIR . '/' . $this->config_file_name;
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

        if (!isset($this->config['database'])) {
            $this->config['database'] = array();
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
        static $name = null;
        switch ($current_path) {
        case '/syl-dao/generation/database':
            if (!isset($this->config['database'])) {
                $this->config['database'] = array();
            }
            $name = $attribute['name'];
            $this->config['database'][$name] = array();
            break;

        case '/syl-dao/generation/database/connectionString':
            $this->config['database'][$name]['connectionString'] = $text;
            break;
        case '/syl-dao/generation/database/outputDir':
            $this->config['database'][$name]['outputDir'] = str_replace('{$SYL_PROJECT_DIR}', SYL_PROJECT_DIR, $text);
            break;
        case '/syl-dao/generation/database/encoding':
            $this->config['database'][$name]['encoding'] = $text;
            break;

        case '/syl-dao/validationMessage':
            $this->config['validationMessage'] = array();
            break;

        case '/syl-dao/validationMessage/require':
            $this->config['validationMessage']['require'] = array();
            break;
        case '/syl-dao/validationMessage/require/message':
            $this->config['validationMessage']['require']['message'] = $text;
            break;

        case '/syl-dao/validationMessage/numeric':
            $this->config['validationMessage']['numeric'] = array();
            break;
        case '/syl-dao/validationMessage/numeric/message':
            $this->config['validationMessage']['numeric']['message'] = $text;
            break;
        case '/syl-dao/validationMessage/numeric/min-error-message':
            $this->config['validationMessage']['numeric']['min-error-message'] = $text;
            break;
        case '/syl-dao/validationMessage/numeric/max-error-message':
            $this->config['validationMessage']['numeric']['max-error-message'] = $text;
            break;

        case '/syl-dao/validationMessage/date':
            $this->config['validationMessage']['date'] = array();
            break;
        case '/syl-dao/validationMessage/date/message':
            $this->config['validationMessage']['date']['message'] = $text;
            break;

        case '/syl-dao/validationMessage/time':
            $this->config['validationMessage']['time'] = array();
            break;
        case '/syl-dao/validationMessage/time/message':
            $this->config['validationMessage']['time']['message'] = $text;
            break;

        case '/syl-dao/validationMessage/byte':
            $this->config['validationMessage']['byte'] = array();
            break;
        case '/syl-dao/validationMessage/byte/message':
            $this->config['validationMessage']['byte']['message'] = $text;
            break;

        case '/syl-dao/validationMessage/multibyte':
            $this->config['validationMessage']['multibyte'] = array();
            break;
        case '/syl-dao/validationMessage/multibyte/message':
            $this->config['validationMessage']['multibyte']['message'] = $text;
            break;
        }
    }
}
