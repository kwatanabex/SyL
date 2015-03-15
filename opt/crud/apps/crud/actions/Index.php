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

SyL_Loader::lib('Template.Text');
SyL_Loader::lib('Db.Abstract');
SyL_Loader::lib('Db.DaoTableAbstract');

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
    public function executeAction(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $project_dir   = $data->geta('d', 0);
        $config_file   = $data->geta('c', 0);
        $table_name    = $data->geta('t', 0);
        $database_name = $data->geta('n', 0);
        $crud_name     = $data->geta('a', 0);
        $config_template_file = $data->geta('f', 0);

        if (!$crud_name) {
            $crud_name = $table_name;
        }

        $cmd = $context->getConsole();

        if (!$config_file) {
            $this->checkProjectDir($project_dir);
            $config_file = $project_dir . '/config/dao.xml';
        }
        $this->checkTableName($table_name);
        $this->checkTableName($crud_name);
        if ($config_template_file) {
            $this->checkTemplateFile($config_template_file);
        }

        $config = $this->getConfig($project_dir, $config_file, $database_name);
        $output_dir = $config['outputDir'];

        $factory_classname  = 'CrudFactory';
        $factory_output_dir = $output_dir . '/Crud';
        $factory_filename   = $factory_classname . '.php';

        $access_abstract_classname  = 'CrudAccessAbstract';
        $access_abstract_output_dir = $output_dir . '/Crud/Access';
        $access_abstract_filename   = $access_abstract_classname . '.php';

        $access_classname   = 'CrudAccess' . ucfirst($crud_name);
        $access_output_dir = $output_dir . '/Crud/Access';
        $access_filename   = $access_classname . '.php';

        $this->checkOutputDir($cmd, $access_output_dir, $access_filename, false);

        $crud_classname = 'CrudConfig' . ucfirst($crud_name);
        $crud_config_output_dir = $output_dir . '/Crud/Config';
        $crud_filename = $crud_classname . '.php';

        $this->checkOutputDir($cmd, $crud_config_output_dir, $crud_filename);

        $conn     = SyL_DbAbstract::getInstance($config['connectionString']);
        $schema   = $conn->getSchema();
        $auto_increment_column = $schema->getAutoIncrementColumn($table_name);
        $auto_increment_column = strtoupper($auto_increment_column);
        $conn->close();

        $entity_classname  = 'DaoEntity' . ucfirst($table_name);
        $entity_file = $output_dir . '/Dao/Entity/' . $entity_classname . '.php';
        $entity = $this->getEntity($entity_file, $entity_classname);

        $elements = array();

        $i = 1;
        foreach ($entity->getColumnNames() as $name) {
            // 大文字統一
            $name = strtoupper($name);
            // フォーム定義用
            $elements[$name] = array(
              'alias'       => 'a',
              'type'        => 'text',
              'name'        => $name,
              'attributes'  => array(),
              'validation'  => array()
            );
            if ($name == $auto_increment_column) {
                // 自動採番カラムは、読み取り専用に
                $elements[$name]['read_only'] = true;
            }
            $i++;
        }

        $access_abstract = null;
        $access_abstract_template_file = SYL_PROJECT_DIR . '/var/templates/CrudAccessAbstractTemplate.php';
        $access_abstract_output_file = $access_abstract_output_dir . '/' . $access_abstract_filename;
        if (!file_exists($access_abstract_output_file)) {
            $template = new SyL_TemplateText($access_abstract_template_file);
            $template->setParameter('SYL_FRAMEWORK_DIR', SYL_FRAMEWORK_DIR);
            $access_abstract = $template->apply();
        }

        $access = null;
        $access_template_file = SYL_PROJECT_DIR . '/var/templates/CrudAccessTemplate.php';
        $access_output_file = $access_output_dir . '/' . $access_filename;
        if (!file_exists($access_output_file)) {
            $template = new SyL_TemplateText($access_template_file);
            $template->setParameter('CLASS_NAME', $access_classname);
            $template->setParameter('ENTITY_CLASS_NAME', $entity_classname);
            $access = $template->apply();
        }

        $crud_config_template_file = $config_template_file ? $config_template_file : SYL_PROJECT_DIR . '/var/templates/CrudConfigTemplate.php';
        $crud_config_output_file = $crud_config_output_dir . '/' . $crud_filename;
        $template = new SyL_TemplateText($crud_config_template_file);
        $template->setParameter('SYL_FRAMEWORK_DIR', SYL_FRAMEWORK_DIR);
        $template->setParameter('CRUD_CLASS_NAME', $crud_classname);
        $template->setParameter('ACCESS_CLASS_NAME', $access_classname);
        $template->setParameter('CRUD_NAME',  $table_name);
        $template->setParameter('CRUD_ELEMENTS',   var_export($elements, true));
        $entity = $template->apply();

        $factory = null;
        $factory_template_file = SYL_PROJECT_DIR . '/var/templates/CrudFactory.php';
        $factory_output_file = $factory_output_dir . '/' . $factory_filename;
        if (!file_exists($factory_output_file)) {
            $template = new SyL_TemplateText($factory_template_file);
            $template->setParameter('SYL_FRAMEWORK_DIR', SYL_FRAMEWORK_DIR);
            $factory = $template->apply();
        }

        if (!is_dir($access_abstract_output_dir)) {
            $parent_dir = dirname($access_abstract_output_dir);
            if (!is_dir($parent_dir)) {
                $cmd->stdout("  -> creating directory: {$parent_dir}");
                mkdir($parent_dir);
            }
            $cmd->stdout("  -> creating directory: {$access_abstract_output_dir}");
            mkdir($access_abstract_output_dir);
        }

        if ($access_abstract) {
            $cmd->stdout("  -> generating file: {$access_abstract_template_file} -> {$access_abstract_output_file}");
            file_put_contents($access_abstract_output_file, $access_abstract);
        }

        if ($access) {
            $cmd->stdout("  -> generating file: {$access_template_file} -> {$access_output_file}");
            file_put_contents($access_output_file, $access);
        }

        if (!is_dir($crud_config_output_dir)) {
            $parent_dir = dirname($crud_config_output_dir);
            $cmd->stdout("  -> creating directory: {$crud_config_output_dir}");
            mkdir($crud_config_output_dir);
        }

        $cmd->stdout("  -> generating file: {$crud_config_template_file} -> {$crud_config_output_file}");
        file_put_contents($crud_config_output_file, $entity);

        if ($factory) {
            $cmd->stdout("  -> generating file: {$factory_template_file} -> {$factory_output_file}");
            file_put_contents($factory_output_file, $factory);
        }
    }
}
