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
 * @package   SyL.Apps
 * @subpackage SyL.Apps.Sitemap
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2010 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id: $
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

require_once 'ExitException.php';
require_once 'HelpException.php';

/**
 * アプリケーション共通アクションクラス
 *
 * @package   SyL.Apps
 * @subpackage SyL.Apps.Sitemap
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2010 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
abstract class AppAction extends SyL_ActionAbstract
{
    /**
     * アクションメソッド実行前に実行されるメソッド
     *
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function preExecute(SyL_ContextAbstract $context, SyL_Data $data)
    {
    }

    /**
     * アクションメソッド実行前に実行される検証メソッド
     * 
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function validate(SyL_ContextAbstract $context, SyL_Data $data)
    {
        parent::validate($context, $data);
    }

    /**
     * メイン処理
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function execute(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $cmd = $context->getConsole();

        try {
            if ($data->getLength() <= 1) {
                throw new HelpException('help');
            }
            if ($data->is('v') || $data->is('version') ||
                $data->is('h') || $data->is('help')) {
                throw new HelpException('help');
            }
            $this->executeSitemap($context, $data);
            //$cmd->stdout('successed sitemap!');
        } catch (ExitException $e) {
            $cmd->stdout('stopped sitemap!');
        } catch (HelpException $e) {
            $script_file = $data->get(0);
            if ($data->is('v') || $data->is('version')) {
                $this->displayVersion($cmd, $script_file);
            } else if ($data->is('h') || $data->is('help')) {
                $this->displayHelp($cmd, $script_file);
            } else {
                $cmd->stdout("*** ERROR: {$script_file}: too few or invalid arguments" . PHP_EOL);
                $this->displayHelp($cmd, $script_file);
            }
        } catch (Exception $e) {
            $cmd->stdout('*** ERROR: ' . get_class($e) . ': ' . $e->getMessage());
            $cmd->stdout('stopped sitemap!');
        }
    }

    /**
     * 個別メイン処理
     * 
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public abstract function executeSitemap(SyL_ContextAbstract $context, SyL_Data $data);

    /**
     * アクションメソッド実行後に実行されるメソッド
     * 
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function postExecute(SyL_ContextAbstract $context, SyL_Data $data)
    {
    }

    /**
     * ヘルプを表示
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string 実行ファイル名
     */
    private function displayHelp(SyL_Console $cmd, $file)
    {
        $help = <<<EOF
Usage:
  php {$file} -d <dir> -w <name> -s <site> [-o <file>] [-l] [-e <dir>] [-u <ext>] [-p <ext>]
  php {$file} [option]

Options:
  -d <dir>  project directory
  -w <name> application name
  -s <site> root URL
  -o <file> output file (default: standard output)
  -l        add lastmod Tag (default: none)
  -e        exclude directory (default: none)
  -u <ext>  extention name for URL access (default: .html)
  -p <ext>  extention name for template file (default: .html)
  -h        show this help, then exit
  -v        output version information, then exit
EOF;
        $cmd->stdout($help);
    }

    /**
     * バージョン情報を表示
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string 実行ファイル名
     */
    private function displayVersion(SyL_Console $cmd, $file)
    {
        $syl_version = SYL_VERSION;
        $php_version = PHP_VERSION;
        $php_sapi    = PHP_SAPI;
        $php_os      = PHP_OS;
        $year        = date('Y');
        $version = <<<EOF
{$file} - SyL {$syl_version} (PHP {$php_version} {$php_sapi} - {$php_os})
Copyright (C) 2006-{$year} k.watanabe
EOF;
        $cmd->stdout($version);
    }


    /**
     * プロジェクトディレクトリを確認する
     * 
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     */
    protected function checkProjectDir(SyL_Console $cmd, $project_dir)
    {
        if (!$project_dir) {
            throw new ErrorException("project directory (-d) not found");
        }
        if (!is_dir($project_dir)) {
            throw new ErrorException("project directory not found ({$project_dir})");
        }
    }

    /**
     * アプリケーションディレクトリを確認する
     * 
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     */
    protected function checkApplicationDir(SyL_Console $cmd, $project_dir, $app_name)
    {
        if (!$app_name) {
            throw new ErrorException("application name (-w) not found");
        } else if (!preg_match('/^[\w\-]+$/', $app_name)) {
            throw new ErrorException("invalid application name format. ([a-z][A-Z][0-9]-_ only)");
        }

        $app_dir = "{$project_dir}/apps/{$app_name}";
        if (!is_dir($app_dir)) {
            throw new ErrorException("application directory not found ({$app_dir})");
        }
    }

    /**
     * サイトURLを確認する
     * 
     * @param SyL_Console コンソール表示オブジェクト
     * @param string サイトURL
     */
    protected function checkSiteUrl(SyL_Console $cmd, $site_root_url)
    {
        if ($site_root_url === null) {
            throw new ErrorException("site root url (-s) not found");
        }
        if (!preg_match('/^https?:\/\/(.+)/', $site_root_url)) {
            $site_root_url = "http://{$site_root_url}";
            if ($cmd->getInput('', "site root url ({$site_root_url}) [Y/n]:") != 'Y') {
                $cmd->stdout('*** ' . $data->get(0) . ' stopped');
                throw new ExitException('exit');
            }
        }
    }

    /**
     * 出力ファイルを確認する
     * 
     * @param SyL_Console コンソール表示オブジェクト
     * @param string 出力ファイル
     */
    protected function checkOutputFile(SyL_Console $cmd, $output_file)
    {
        if (file_exists($output_file)) {
            if ($cmd->getInput('', "Already output file exist. overwrite file ? ({$output_file}) [Y/n]:") != 'Y') {
                $cmd->stdout('*** ' . $data->get(0) . ' stopped');
                throw new ExitException('exit');
            }
            if (!is_writable($output_file)) {
                throw new ErrorException("output file permission denied ({$output_file})");
            }
        } else {
            $output_dir = dirname($output_file);
            if (is_dir($output_dir)) {
                if (!is_writable($output_dir)) {
                    throw new ErrorException("output directory permission denied ({$output_dir})");
                }
            } else {
                throw new ErrorException("output directory not found ({$output_dir})");
            }
        }
    }

}
