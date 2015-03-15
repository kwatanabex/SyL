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
            if ($data->is('v') || $data->is('version') ||
                $data->is('h') || $data->is('help')) {
                throw new HelpException('help');
            }
            $this->executeDao($context, $data);
            $cmd->stdout('successed!');
        } catch (ExitException $e) {
            $cmd->stdout('stopped!');
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
        } catch (ErrorException $e) {
            $cmd->stdout('*** ERROR: ' . get_class($e) . ': ' . $e->getMessage());
            $cmd->stdout('stopped!');
        }
    }

    /**
     * 個別メイン処理
     * 
     * @param SyL_ContextAbstract コンテキストオブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public abstract function executeDao(SyL_ContextAbstract $context, SyL_Data $data);

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
  php {$file} -d <dir> -t <name> [-n <name>]
  php {$file} -c <file> -t <name> [-n <name>]
  php {$file} [option]

Options:
  -d <dir>   project directory (use DAO setting file : SYL_PROJECT_DIR/config/dao.xml)
  -c <file>  DAO setting file
  -n <name>  database name (default: first element database)
  -t <name>  table name
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
     * @param string プロジェクトディレクトリ
     */
    protected function checkProjectDir($project_dir)
    {
        if (!$project_dir || !is_dir($project_dir)) {
            throw new ErrorException("project directory (-d) or DAO setting file (-c) not found");
        }
        $project_lib_dir = $project_dir . '/lib';
        if (!is_dir($project_lib_dir)) {
            throw new ErrorException("lib directory not found ({$project_lib_dir})");
        }
        $project_config_dir = $project_dir . '/config';
        if (!is_dir($project_config_dir)) {
            throw new ErrorException("config directory not found ({$project_config_dir})");
        }
        $project_config_file = $project_dir . '/config/dao.xml';
        if (!is_file($project_config_file)) {
            throw new ErrorException("config file not found ({$project_config_file})");
        }
    }

    /**
     * テーブル名を確認する
     *
     * クラス名として使用できない文字はエラーとする
     * 
     * @param string テーブル名
     */
    protected function checkTableName($table_name)
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
            throw new ErrorException("invalid table name (-t). [a-zA-Z0-9_] only ({$table_name})");
        }
    }

    /**
     * デフォルトの出力ディレクトリを確認する
     *
     * @param SyL_Console コンソール表示オブジェクト
     * @param string 出力ディレクトリ
     * @param string 出力ファイル名
     * @param bool ファイル名上書きの確認メッセージの表示フラグ
     */
    protected function checkOutputDir(SyL_Console $cmd, $output_dir, $filename, $confirm=true)
    {
        if (is_dir($output_dir)) {
            $output_file = $output_dir . '/' . $filename;
            if (is_file($output_file)) {
                if ($confirm && ($cmd->getInput('already output file exists', "overwrite file? ({$output_file}) [Y/n]:") != 'Y')) {
                    throw new ExitException('exit');
                } else if (!is_writable($output_file)) {
                    throw new ErrorException("output file permission denied ({$output_file})");
                }
            } else if (!is_writable($output_dir)) {
                throw new ErrorException("output directory permission denied ({$output_dir})");
            }
        }
    }

    /**
     * 設定ファイルから設定値を読み込む
     *
     * @param string プロジェクトディレクトリ
     * @param string 設定ファイル
     * @param string データベース名
     * @return array 設定値
     */
    protected function getConfig($project_dir, $config_file, $database_name)
    {
        if (!is_file($config_file)) {
            throw new ErrorException("config file not found ({$config_file})");
        }

        $config = array();
        $xml = simplexml_load_file($config_file);

        if (!isset($xml->generation) || !isset($xml->generation->database)) {
            throw new ErrorException("empty config setting `generation/database' ({$config_file})");
        }

        $database = null;
        if ($database_name) {
            foreach ($xml->generation[0]->database as $tmp) {
                if ($tmp['name'] == $database_name) {
                    $database = $tmp;
                    break;
                }
            }
        } else {
            $database = $xml->generation[0]->database[0];
            $database_name = (string)$database['name'];
        }

        if ($database == null) {
            throw new ErrorException("database name not found (`{$database_name}' in {$config_file})");
        }

        $value = (string)$database->connectionString;
        if ($value) {
            $config['connectionString'] = $value;
        } else {
            throw new ErrorException("empty config setting `connectionString' ({$config_file})");
        }

        $value = (string)$database->outputDir;
        if ($value) {
            $value = str_replace('{$SYL_PROJECT_DIR}', $project_dir, $value);
            if (is_dir($value)) {
                $config['outputDir'] = $value;
            } else {
                throw new ErrorException("config setting `outputDir' directory not exist ({$value})");
            }
        } else {
            throw new ErrorException("empty config setting `outputDir' ({$config_file})");
        }

        $value = (string)$database->encoding;
        if ($value) {
            $encoding = strtoupper($value);
            $match = false;
            foreach (array_map('strtoupper', mb_list_encodings()) as $tmp) {
                if ($encoding == $tmp) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                throw new ErrorException("invalid config setting `encoding' not supported encoding ({$value})");
            }
            $config['encoding'] = $value;
        } else {
            $config['encoding'] = null;
        }

        $config['validationMessage'] = array();
        $config['validationMessage']['require'] = array();
        $config['validationMessage']['numeric'] = array();
        $config['validationMessage']['date'] = array();
        $config['validationMessage']['time'] = array();
        $config['validationMessage']['byte'] = array();
        $config['validationMessage']['multibyte'] = array();

        if ($xml->validationMessage) {
            $value = (string)$xml->validationMessage->require->message;
            $config['validationMessage']['require']['message'] = $value ? $value : null;

            $value = (string)$xml->validationMessage->numeric->message;
            $config['validationMessage']['numeric']['message'] = $value ? $value : null;
            $value = (string)$xml->validationMessage->numeric->{'min-error-message'};
            $config['validationMessage']['numeric']['min-error-message'] = $value ? $value : null;
            $value = (string)$xml->validationMessage->numeric->{'max-error-message'};
            $config['validationMessage']['numeric']['max-error-message'] = $value ? $value : null;

            $value = (string)$xml->validationMessage->date->message;
            $config['validationMessage']['date']['message'] = $value ? $value : null;

            $value = (string)$xml->validationMessage->time->message;
            $config['validationMessage']['time']['message'] = $value ? $value : null;

            $value = (string)$xml->validationMessage->byte->message;
            $config['validationMessage']['byte']['message'] = $value ? $value : null;

            $value = (string)$xml->validationMessage->multibyte->message;
            $config['validationMessage']['multibyte']['message'] = $value ? $value : null;
        } else {
            $config['validationMessage']['require']['message'] = null;
            $config['validationMessage']['numeric']['message'] = null;
            $config['validationMessage']['numeric']['min-error-message'] = null;
            $config['validationMessage']['numeric']['max-error-message'] = null;
            $config['validationMessage']['date']['message'] = null;
            $config['validationMessage']['time']['message'] = null;
            $config['validationMessage']['byte']['message'] = null;
            $config['validationMessage']['multibyte']['message'] = null;
        }

        $xml = null;

        return $config;
    }

    /**
     * カラム型からバリデーションを取得する
     *
     * @param array 属性配列
     * @param array バリデーション
     */
    protected function getValidateDefinition($column, $config)
    {
        if (!$config['validationMessage']['require']['message']) {
            $config['validationMessage']['require']['message'] = '{$name} is required';
        }
        if (!$config['validationMessage']['numeric']['message']) {
            $config['validationMessage']['numeric']['message'] = '{$name} is invalid number format';
        }
        if (!$config['validationMessage']['numeric']['min-error-message']) {
            $config['validationMessage']['numeric']['min-error-message'] = '{$name} must be greater than (or equal) {$min}';
        }
        if (!$config['validationMessage']['numeric']['max-error-message']) {
            $config['validationMessage']['numeric']['max-error-message'] = '{$name} must be less than (or equal) {$max}';
        }
        if (!$config['validationMessage']['date']['message']) {
            $config['validationMessage']['date']['message'] = '{$name} is invalid date format';
        }
        if (!$config['validationMessage']['time']['message']) {
            $config['validationMessage']['time']['message'] = '{$name} is invalid time format';
        }
        if (!$config['validationMessage']['byte']['message']) {
            $config['validationMessage']['byte']['message'] = '{$name} is too long. (maximum is {$max} bytes)';
        }
        if (!$config['validationMessage']['multibyte']['message']) {
            $config['validationMessage']['multibyte']['message'] = '{$name} is too long. (maximum is {$max} characters)';
        }

        $validate = array();
        // 必須チェック
        if ($column['not_null'] && !$column['default']) {
            $validate['require'] = array('message' => $config['validationMessage']['require']['message']);
        }
        switch ($column['simple_type']) {
        // 整数型
        case 'I':
            $validate['numeric'] = array(
              'message'    => $config['validationMessage']['numeric']['message'],
              'parameters' => array(
                'dot' => false,
                'min' => $column['min'],
                'max' => $column['max'],
                'min_error_message' =>  $config['validationMessage']['numeric']['min-error-message'],
                'max_error_message' =>  $config['validationMessage']['numeric']['max-error-message'],
              )
            );
            break;

        // 浮動小数点型
        case 'F':
            $validate['numeric'] = array(
              'message'    => $config['validationMessage']['numeric']['message'],
              'parameters' => array(
                'dot' => true
              )
            );
            break;

        // 桁数固定数値型
        case 'N':
            $validate['numeric'] = array(
              'message'    => $config['validationMessage']['numeric']['message'],
              'parameters' => array(
                'dot' => true,
                'min' => $column['min'],
                'max' => $column['max'],
                'min_error_message' =>  $config['validationMessage']['numeric']['min-error-message'],
                'max_error_message' =>  $config['validationMessage']['numeric']['max-error-message'],
              )
            );
            break;

        // 日付型
        case 'D':
        case 'DT':
            $validate['date'] = array(
              'message' => $config['validationMessage']['date']['message']
            );
            break;

        // 時間型
        case 'T':
            $validate['regex'] = array(
              'message' => $config['validationMessage']['time']['message'],
              'parameters' => array(
                'format' => '/^([0-1][0-9]|2[0-3]):?([0-5][0-9]):?([0-5][0-9])$/'
              )
            );
            break;

        // 文字列型（バイト）
        case 'S':
            $validate['byte'] = array(
              'message'    => $config['validationMessage']['byte']['message'],
              'parameters' => array(
                'max' => $column['max']
              )
            );
            break;

        // 文字列型（文字長）
        case 'M':
            $validate['multibyte'] = array(
              'message'    =>  $config['validationMessage']['multibyte']['message'],
              'parameters' => array(
                'max' => $column['max'],
                'encoding' => $config['encoding']
              )
            );
            break;
        }
        return $validate;
    }

}
