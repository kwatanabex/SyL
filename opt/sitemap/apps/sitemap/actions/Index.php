<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
 *
 * PHP version 5 (>= 5.2.x)
 *
 * Copyright (C) 2006-2010 k.watanabe
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
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id: $
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** サイトマップ作成クラス */
SyL_Loader::lib('Xml.Sitemap.Writer');

/**
 * サイトマップ作成クラス
 *
 * [ sitemap.org ]
 * http://www.sitemaps.org/
 *
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2011 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class Index extends AppAction
{
    /**
     * デフォルトインデックス名
     * 
     * @var string
     */
    const DEFAULT_INDEX_NAME = 'index';

    /**
     * サイトマップ作成処理
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function executeSitemap(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $cmd = $context->getConsole();

        $project_dir    = $data->geta('d', 0);
        $app_name       = $data->geta('w', 0);
        $site_root_url  = $data->geta('s', 0);
        $output_file    = $data->geta('o', 0);
        $lastmod        = $data->is('l');
        $ext_url        = $data->geta('u', 0);
        $ext_file       = $data->geta('p', 0);
        $excluding_dirs = $data->geta('e');

        // プロジェクトディレクトリチェック
        $this->checkProjectDir($cmd, $project_dir);
        $project_dir = realpath($project_dir);

        // アプリケーション名チェック
        $this->checkApplicationDir($cmd, $project_dir, $app_name);
        $app_dir = "{$project_dir}/apps/{$app_name}";

        // サイトURLチェック
        $this->checkSiteUrl($cmd, $site_root_url);
        if (preg_match('/^(.+)\/$/', $site_root_url, $matches)) {
            $site_root_url =  $matches[1];
        }

        // ファイルチェック
        if ($output_file !== null) {
            $this->checkOutputFile($cmd, $output_file);
        }

        // 拡張子補正
        if ($ext_url === null) {
            $ext_url = '.html';
        }
        if ($ext_file === null) {
            $ext_file = '.html';
        }

        // アプリケーションのテンプレートディレクトリ
        $template_dir = "{$app_dir}/templates";

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($template_dir));

        $writer = new SyL_XmlSitemapWriter($output_file);

        while ($it->valid()) {
            if (!$it->isDot()) {
                $path = '/' . $it->getSubPathName();
                if (self::checkValidPath($path, $excluding_dirs, $ext_file)) {
                    $path = self::convertPath($path, $ext_url, $ext_file);

                    $mtime = null;
                    if ($lastmod) {
                        $mtime = date('Y-m-d', $it->getMTime());
                    }

                    $default_index_name = self::DEFAULT_INDEX_NAME . $ext_url;
                    if (preg_match('/^(.+)(' . preg_quote($default_index_name) . ')$/', $path, $matches)) {
                        $url = new SyL_XmlSitemapUrl();
                        $url->loc = $site_root_url . $matches[1];
                        if ($mtime) {
                            $url->lastmod = $mtime;
                        }
                        $writer->addUrl($url);
                    }

                    $url = new SyL_XmlSitemapUrl();
                    $url->loc = $site_root_url . $path;
                    if ($mtime) {
                        $url->lastmod = $mtime;
                    }
                    $writer->addUrl($url);
                }
            }

            $it->next();
        }

        // サトマップ作成
        $result = $writer->createXml();
        if (!$output_file) {
            echo $result;
        }
    }

    /**
     * サイトマップ用に出力するURLか判定する
     *
     * @param string パス
     * @param array 除外ディレクトリ
     * @param string テンプレート拡張子
     * @return boolean 判定結果
     */
    private static function checkValidPath($path, array $excluding_dirs, $ext_file)
    {
        // 除外ディレクトリ確認
        foreach ($excluding_dirs as $excluding_dir) {
            if (strpos($path, $excluding_dir) === 0) {
                return false;
            }
        }

        // テンプレートファイルの拡張子の確認
        if (!preg_match('/' . preg_quote($ext_file) . '$/', $path)) {
            return false;
        }

        // 先頭が「_」のディレクトリ／ファイルの確認
        foreach (preg_split('/(\/|\\\\)/', $path) as $element) {
            if ((strlen($element) > 0) && ($element[0] == '_')) {
                return false;
            }
        }

        return true;
    }

    /**
     * パスをURLに変換する
     *
     * @param string パス
     * @param string URL拡張子
     * @param string テンプレート拡張子
     * @return URL
     */
    private static function convertPath($path, $ext_url, $ext_file)
    {
        // 拡張子変換
        if (preg_match('/(.+)(' . preg_quote($ext_file) . ')$/', $path, $matches)) {
            $path = $matches[1] . $ext_url;
        } else {
            throw new ErrorException("invalid convert path");
        }

        // 先頭の大文字を小文字に変換
        $converted_path = '';
        foreach (preg_split('/(\/|\\\\)/', $path) as $element) {
            if (trim($element)) {
                $element[0] = strtolower($element[0]);
                $converted_path .= '/' . $element;
            }
        }
        return $converted_path;
    }

}
