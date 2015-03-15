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
 * @package   SyL.Core
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
 * @package   SyL.Core
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
            $this->executeSetup($context, $data);
            $cmd->stdout('successed setup!');
        } catch (ExitException $e) {
            $cmd->stdout('stopped setup!');
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
            $cmd->stdout('stopped setup!');
        }
    }

    /**
     * 個別メイン処理
     * 
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public abstract function executeSetup(SyL_ContextAbstract $context, SyL_Data $data);

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
  php {$file} -s project [or pro] -d <dir> -n <name> [-c]
  php {$file} -s application [or app] -d <dir> -n <name> [-c]
  php {$file} -s controller [or con] -d <dir> -n <name>
  php {$file} -s action [or act] -d <dir> -n <name> -a <file>
  php {$file} -s template [or tem] -d <dir> -n <name> -t <file>
  php {$file} [option]

Options:
  -s <name>  setup type
  -d <dir>   project directory
  -n <name>  application name
  -c         front controller file (create SYL_PROJECT_DIR/public/[app_name]/[app_name].php)
  -a <file>  action file (path from action directory)
  -t <file>  template file (path from template directory)
  -h         show this help, then exit
  -v         output version information, then exit
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
        if (file_exists($project_dir)) {
            if (is_file($project_dir)) {
                throw new ErrorException("already file exists ({$project_dir})");
            }
            if (!is_writable($project_dir)) {
                throw new ErrorException("project directory permission denied ({$project_dir})");
            }
        } else {
            if ($cmd->getInput('', "create project ({$project_dir}) [Y/n]:") != 'Y') {
                throw new ExitException('exit');
            }
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

        if (file_exists($app_dir)) {
            if (is_file($app_dir)) {
                throw new ErrorException("already file exists ({$app_dir})");
            }
            if (!is_writable($app_dir)) {
                throw new ErrorException("application directory permission denied ({$app_dir})");
            }
            if ($cmd->getInput('already application directory exists', "overwrite application ({$app_dir}) [Y/n]:") != 'Y') {
                throw new ExitException('exit');
            }
        } else {
            if ($cmd->getInput('', "create application ({$app_dir}) [Y/n]:") != 'Y') {
                throw new ExitException('exit');
            }
        }
    }

    /**
     * コントローラファイルを確認する
     * 
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     * @param string コントローラファイル
     */
    protected function checkControllerFile(SyL_Console $cmd, $project_dir, $app_name, $controller_file)
    {
        if (file_exists($controller_file)) {
            if ($cmd->getInput('already controller file exists', "overwrite controller file ({$controller_file}) [Y/n]:") != 'Y') {
                throw new ExitException('exit');
            }
            if (!is_writable($controller_file)) {
                throw new ErrorException("controller file permission denied ({$controller_file})");
            }
        } else {
            $controller_dir = dirname($controller_file);

            if (is_dir($controller_dir)) {
                if (!is_writable($controller_dir)) {
                    throw new ErrorException("controller directory permission denied ({$controller_dir})");
                }
            } else {
                $public_dir = dirname($controller_dir);

                if (is_dir($public_dir)) {
                    if (!is_writable($public_dir)) {
                        throw new ErrorException("public directory permission denied ({$public_dir})");
                    }
                }
            }
        }
    }

    /**
     * プロジェクトディレクトリを作成する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     */
    protected function createProjectDir(SyL_Console $cmd, $project_dir)
    {
        if (!is_dir($project_dir)) {
            if (!mkdir($project_dir, 0755)) {
                throw new ErrorException("project directory can't create ({$project_dir})");
            }
        }

        $dirs = array(
          '/apps'        => 0755,
          '/config'      => 0755,
          '/lib'         => 0755,
          '/public'      => 0755,
          '/var'         => 0755,
          '/var/cache'   => 0755,
          '/var/logs'    => 0755,
          '/var/session' => 0777,
          '/var/skel-templates' => 0755,
          '/var/templates'      => 0755,
          '/var/syslogs' => 0755,
        );

        $files = array(
          'defines.xml'    => '/config/defines.xml',
          'filters.xml'    => '/config/filters.xml',
          'components.xml' => '/config/components.xml',
          'dao.xml'        => '/config/dao.xml'
        );

        foreach ($dirs as $dir => $mode) {
            if (!is_dir($project_dir . $dir)) {
                $cmd->stdout("  -> creating directory {$project_dir}{$dir}");
                mkdir("{$project_dir}{$dir}");
                chmod("{$project_dir}{$dir}", $mode);
            }
        }

        foreach ($files as $file_org => $file_dist) {
            $cmd->stdout("  -> copying file {$project_dir}{$file_dist}");
            if (file_exists("{$project_dir}/var/skel-templates/{$file_org}")) {
                copy("{$project_dir}/var/skel-templates/{$file_org}", "{$project_dir}{$file_dist}");
            } else {
                copy(SYL_PROJECT_DIR . "/var/skel-templates/{$file_org}", "{$project_dir}{$file_dist}");
            }
        }

        foreach (scandir(SYL_PROJECT_DIR . '/var/skel-templates/') as $file_org) {
            if (is_file(SYL_PROJECT_DIR . '/var/skel-templates/' . $file_org)) {
                $cmd->stdout("  -> copying file {$project_dir}/var/skel-templates/{$file_org}");
                copy(SYL_PROJECT_DIR . "/var/skel-templates/{$file_org}", "{$project_dir}/var/skel-templates/{$file_org}");
            }
        }
    }

    /**
     * アプリケーションディレクトリを作成する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     */
    protected function createApplicationDir(SyL_Console $cmd, $project_dir, $app_name)
    {
        $app_dir = "{$project_dir}/apps/{$app_name}";
        if (!is_dir($app_dir)) {
            $cmd->stdout("  -> creating directory {$app_dir}");
            if (!mkdir($app_dir, 0755)) {
                throw new ErrorException("application directory can't create ({$app_dir})");
            }
        }

        $dirs = array(
          '/actions'   => 0755,
          '/config'    => 0755,
          '/lib'       => 0755,
          '/lib/App'   => 0755,
          '/templates' => 0755,
          '/templates/_App' => 0755,
          "/../../var/cache/{$app_name}" => 0755,
          "/../../var/cache/{$app_name}/app" => 0777,
          "/../../var/cache/{$app_name}/config" => 0777,
          "/../../var/cache/{$app_name}/response" => 0777,
          "/../../var/logs/{$app_name}" => 0777,
          "/../../var/templates/{$app_name}" => 0755,
          "/../../var/syslogs/{$app_name}" => 0777,
        );

        $files = array(
          '/actions.xml'     => '/config/actions.xml',
          '/classes.xml'     => '/config/classes.xml',
          '/defines_app.xml' => '/config/defines.xml',
          '/filters.xml'     => '/config/filters.xml',
          '/layouts.xml'     => '/config/layouts.xml',
          '/routers.xml'     => '/config/routers.xml',
          '/error_template_not_found.html' => '/templates/_App/error_template_not_found.html',
          '/error_template_server_error.html' => '/templates/_App/error_template_server_error.html',
          '/AppAction.php'   => '/lib/App/AppAction.php',
          '/AppErrorHandler.php' => '/lib/App/AppErrorHandler.php'
        );

        foreach ($dirs as $dir => $mode) {
            if (!is_dir($app_dir . $dir)) {
                $cmd->stdout("  -> creating directory {$app_dir}{$dir}");
                mkdir("{$app_dir}{$dir}");
                chmod("{$app_dir}{$dir}", $mode);
            }
        }

        foreach ($files as $file_org => $file_dist) {
            $cmd->stdout("  -> copying file {$app_dir}{$file_dist}");
            if (file_exists("{$project_dir}/var/skel-templates{$file_org}")) {
                copy("{$project_dir}/var/skel-templates{$file_org}", "{$app_dir}{$file_dist}");
            } else {
                copy(SYL_PROJECT_DIR . "/var/skel-templates{$file_org}", "{$app_dir}{$file_dist}");
            }
        }
    }

    /**
     * コントローラファイルを作成する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     * @param string コントローラファイル
     */
    protected function createControllerFile(SyL_Console $cmd, $project_dir, $app_name, $controller_file)
    {
        $controller_template = SYL_PROJECT_DIR . '/var/skel-templates/controller.php';
        $contents = file_get_contents($controller_template);

        $trans = array(
          '{{SYL_FRAMEWORK_DIR}}' => SYL_FRAMEWORK_DIR,
          '{{PROJECT_DIR}}' => $project_dir,
          '{{APP_NAME}}'    => $app_name
        );
        $contents = strtr($contents, $trans);

        $controller_dir = dirname($controller_file);

        if (!is_dir($controller_dir)) {
            $cmd->stdout("  -> creating directory {$controller_dir}");
            if (!mkdir($controller_dir, 0755)) {
                throw new ErrorException("controller directory can't create ({$controller_dir})");
            }
        }

        $cmd->stdout("  -> creating file {$controller_file}");
        $fp = fopen($controller_file, 'wb');
        fwrite($fp, $contents);
        fclose($fp);
    }

    /**
     * アクションディレクトリを作成する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     * @param array アクションファイル
     */
    protected function createActionDir(SyL_Console $cmd, $project_dir, $app_name, $action_files)
    {
        $action_dir = "{$project_dir}/apps/{$app_name}/actions";
        if (!is_dir($action_dir)) {
            $cmd->stdout("  -> creating directory {$action_dir}");
            if (!mkdir($action_dir, 0755)) {
                throw new ErrorException("action directory can't create ({$action_dir})");
            }
        }

        if (file_exists("{$project_dir}/var/skel-templates/action.php")) {
            $action_template = "{$project_dir}/var/skel-templates/action.php";
        } else {
            $action_template = SYL_PROJECT_DIR . '/var/skel-templates/action.php';
        }
        $contents = file_get_contents($action_template);

        if (!is_array($action_files)) {
            $action_files = array($action_files);
        }

        for ($i=0; $i<count($action_files); $i++) {
            $class_name = implode('_', array_map('ucfirst', preg_split('/(\\\\|\/)/', $action_files[$i])));
            $trans = array(
              '{{SYL_CLASS}}' => $class_name,
              '{{APP_NAME}}'  => $app_name
            );
            $contents_tmp = strtr($contents, $trans);

            $file = $action_dir . '/' . ucfirst($action_files[$i]) . '.php';
            $tmp = dirname($file) . '/';
            $create_dir = array();
            while (preg_match('/^' . preg_quote($action_dir, '/') . '/', $tmp)) {
                array_unshift($create_dir, $tmp);
                $tmp = dirname($tmp);
            }

            foreach ($create_dir as $dir) {
                if (!is_dir($dir)) {
                    $cmd->stdout("  -> creating directory {$dir}");
                    mkdir($dir, 0755);
                }
            }

            $cmd->stdout("  -> creating file {$file}");
            $fp = fopen($file, 'wb');
            fwrite($fp, $contents_tmp);
            fclose($fp);
        }
    }

    /**
     * テンプレートディレクトリを作成する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string プロジェクトディレクトリ
     * @param string アプリケーション名
     * @param array テンプレートファイル
     */
    protected function createTemplateDir(SyL_Console $cmd, $project_dir, $app_name, $template_files)
    {
        $template_dir = "{$project_dir}/apps/{$app_name}/templates";
        if (!is_dir($template_dir)) {
            if (!mkdir($template_dir, 0755)) {
                throw new ErrorException("template directory can't create ({$template_dir})");
            }
        }

        if (!is_array($template_files)) {
            $template_files = array($template_files);
        }

        for ($i=0; $i<count($template_files); $i++) {
            $file = $template_dir . '/' . ucfirst($template_files[$i]) . '.html';
            $tmp = dirname($file) . '/';
            $create_dir = array();
            while (preg_match('/^' . preg_quote($template_dir, '/') . '/', $tmp)) {
                array_unshift($create_dir, $tmp);
                $tmp = dirname($tmp);
            }

            foreach ($create_dir as $dir) {
                if (!is_dir($dir)) {
                    $cmd->stdout("  -> creating directory {$dir}");
                    mkdir($dir, 0755);
                }
            }

            $cmd->stdout("  -> copying file {$file}");
            if (file_exists("{$project_dir}/var/skel-templates/template.html")) {
                copy("{$project_dir}/var/skel-templates/template.html", $file);
            } else {
                copy(SYL_PROJECT_DIR . '/var/skel-templates/template.html', $file);
            }
        }
    }
}
