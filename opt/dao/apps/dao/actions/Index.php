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
 * @subpackage SyL.Apps.Dao
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id: $
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

SyL_Loader::lib('Db.Abstract');
SyL_Loader::lib('Db.SchemaAbstract');
SyL_Loader::lib('Template.Text');

/**
 * DAO クラス作成エントリポイントクラス
 *
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Dao
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class Index extends AppAction
{
    /**
     * アクション実行メソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function executeDao(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $project_dir   = $data->geta('d', 0);
        $config_file   = $data->geta('c', 0);
        $database_name = $data->geta('n', 0);
        $table_name    = $data->geta('t', 0);

        $cmd = $context->getConsole();

        if (!$config_file) {
            $this->checkProjectDir($project_dir);
            $config_file = $project_dir . '/config/dao.xml';
        }
        $this->checkTableName($table_name);

        $config = $this->getConfig($project_dir, $config_file, $database_name);
        $output_dir = $config['outputDir'];

        $entity_classname  = 'DaoEntity' . ucfirst($table_name);
        $entity_output_dir = $output_dir . '/Dao/Entity';
        $entity_filename   = $entity_classname . '.php';

        $this->checkOutputDir($cmd, $entity_output_dir, $entity_filename, false);

        $access_abstract_classname  = 'DaoAccessAbstract';
        $access_abstract_output_dir = $output_dir . '/Dao/Access';
        $access_abstract_filename   = $access_abstract_classname . '.php';

        $access_classname   = 'DaoAccess' . ucfirst($table_name);
        $access_output_dir = $output_dir . '/Dao/Access';
        $access_filename   = $access_classname . '.php';

        $this->checkOutputDir($cmd, $access_output_dir, $access_filename, false);

        $cmd->stdout("=== Input Environment ===");

        if ($project_dir) {
            $cmd->stdout("- project directory: {$project_dir}");
        }
        $cmd->stdout("- config_file: {$config_file}");
        $cmd->stdout("- table_name: {$table_name}");
        $cmd->stdout("- class_name (entity): {$entity_classname}");
        $cmd->stdout("- output_file (entity): {$entity_output_dir}/{$entity_filename}");
        if (!is_file($access_output_dir . '/' . $access_filename)) {
            $cmd->stdout("- class_name (access): {$access_classname}");
            $cmd->stdout("- output_file (access): {$access_output_dir}/{$access_filename}");
        }

        $conn     = SyL_DbAbstract::getInstance($config['connectionString']);
        $schema   = $conn->getSchema();
        $results  = $schema->getColumns($table_name);
        $primary  = $schema->getPrimaryColumns($table_name);
        $uniques  = $schema->getUniqueColumns($table_name);
        $foreigns = $schema->getForeignColumns($table_name);
        $conn->close();
        $conn = null;

        if (count($results) == 0) {
            throw new SyL_Exception("table not found ({$table_name})");
        }

        // 大文字統一
        $primary = array_map('strtoupper', $primary);
        for ($i=0; $i<count($uniques); $i++) {
            $uniques[$i] = array_map('strtoupper', $uniques[$i]);
        }
        foreach (array_keys($foreigns) as $key) {
            $foreigns[$key] = array_change_key_case($foreigns[$key], CASE_UPPER);
            $foreigns[$key] = array_map('strtoupper', $foreigns[$key]);
        }

        $cmd->stdout("");

        $cmd->stdout("=== Table Schema ===");

        $cmd->stdout("[columns]");

        $columns = array();
        foreach ($results as $name => $value) {
            // 大文字統一
            $name = strtoupper($name);
            // テーブル定義用
            $columns[$name] = array(
              'type'       => $value['simple_type'],
              'validation' => $this->getValidateDefinition($value, $config)
            );

            $cmd->stdout("  {$name}");
        }

        $cmd->stdout("[primary]");
        foreach ($primary as $name) {
            $cmd->stdout("  {$name}");
        }

        $cmd->stdout("");

        $cmd->stdout("=== Output Files ===");

        $entity = null;
        $entity_template_file = SYL_PROJECT_DIR . '/var/templates/EntityTemplate.php';
        $entity_output_file = $entity_output_dir . '/' . $entity_filename;

        $template = new SyL_TemplateText($entity_template_file);
        $template->setParameter('CLASS_NAME', $entity_classname);
        $template->setParameter('TABLE_NAME', $table_name);
        $template->setParameter('PRIMARY',  var_export($primary, true));
        $template->setParameter('UNIQUES',  var_export($uniques, true));
        $template->setParameter('FOREIGNS', var_export($foreigns, true));
        $template->setParameter('COLUMNS',  var_export($columns, true));

        $entity = $template->apply();

        $access_abstract = null;
        $access_abstract_template_file = SYL_PROJECT_DIR . '/var/templates/AccessAbstractTemplate.php';
        $access_abstract_output_file = $access_abstract_output_dir . '/' . $access_abstract_filename;
        if (!file_exists($access_abstract_output_file)) {
            $template = new SyL_TemplateText($access_abstract_template_file);
            $template->setParameter('SYL_FRAMEWORK_DIR', SYL_FRAMEWORK_DIR);
            $access_abstract = $template->apply();
        }

        $access = null;
        $access_template_file = SYL_PROJECT_DIR . '/var/templates/AccessTemplate.php';
        $access_output_file = $access_output_dir . '/' . $access_filename;
        if (!file_exists($access_output_file)) {
            $template = new SyL_TemplateText($access_template_file);
            $template->setParameter('CLASS_NAME', $access_classname);
            $template->setParameter('ENTITY_CLASS_NAME', $entity_classname);
            $access = $template->apply();
        }

        if (!is_dir($output_dir)) {
            $cmd->stdout("  -> creating directory: {$output_dir}");
            mkdir($output_dir);
        }

        if (!is_dir($entity_output_dir)) {
            $parent_dir = dirname($entity_output_dir);
            if (!is_dir($parent_dir)) {
                $cmd->stdout("  -> creating directory: {$parent_dir}");
                mkdir($parent_dir);
            }
            $cmd->stdout("  -> creating directory: {$entity_output_dir}");
            mkdir($entity_output_dir);
        }

        if (!is_dir($access_output_dir)) {
            $cmd->stdout("  -> creating directory: {$access_output_dir}");
            mkdir($access_output_dir);
        }

        $cmd->stdout("  -> generating file: {$entity_template_file} -> {$entity_output_file}");
        file_put_contents($entity_output_file, $entity);

        if ($access_abstract) {
            $cmd->stdout("  -> generating file: {$access_abstract_template_file} -> {$access_abstract_output_file}");
            file_put_contents($access_abstract_output_file, $access_abstract);
        }

        if ($access) {
            $cmd->stdout("  -> generating file: {$access_template_file} -> {$access_output_file}");
            file_put_contents($access_output_file, $access);
        }

        $cmd->stdout("");
        $cmd->stdout("=== Result ===");

        $cmd->stdout("  [table entity] {$entity_output_file}");
        if ($access_abstract) {
            $cmd->stdout("  [table access abstract] {$access_abstract_output_file}");
        }
        if ($access) {
            $cmd->stdout("  [table access] {$access_output_file}");
        }
    }
}
