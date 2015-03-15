<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** DAO�A�N�Z�X�N���X */
require_once dirname(__FILE__) . '/../Db/SyL_DbDaoAccessAbstract.php';

/**
 * CRUD DB�A�N�Z�X�N���X
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_CrudDbDaoAccessAbstract extends SyL_DbDaoAccessAbstract
{
    /**
     * CRUD�ݒ�I�u�W�F�N�g
     *
     * @var SyL_CrudConfigAbstract
     */
    protected $config = null;

    /**
     * �R���X�g���N�^
     *
     * @param SyL_CrudConfigAbstract CRUD�ݒ�I�u�W�F�N�g
     */
    public function __construct(SyL_CrudConfigAbstract $config)
    {
        $dao_dir = $this->getDaoDirectory();
        foreach ($this->class_names as $class_name) {
            $include_file = $dao_dir . '/' . $class_name . '.php';
            include_once $include_file;
        }

        parent::__construct($this->getConnection());
        $this->config = $config;
    }

    /**
     * DAO�f�B���N�g�����擾����
     *
     * @return string DAO�f�B���N�g��
     */
    protected abstract function getDaoDirectory();

    /**
     * DB�R�l�N�V�������擾����
     *
     * @return SyL_DbAbstract DB�R�l�N�V����
     */
    public abstract function getConnection();

    /**
     * ���ʃZ�b�g���R�[�h�I�u�W�F�N�g���쐬����
     * 
     * @param bool �X�V���R�[�h�쐬�t���O
     * @return SyL_DbRecord ���ʃZ�b�g���R�[�h�I�u�W�F�N�g
     */
    public function createRecord($update=false)
    {
        $record = parent::createRecord($update);
        $elements = $this->config->getElements();
        foreach ($elements as $name => &$element) {
            $record->setAliasName($name, $element->getName());
        }
        return $record;
    }

    /**
     * �ʖ������C���Ŏg�p���Ă��邩���肷��
     *
     * @param string �ʖ�
     * @return bool �ʖ������C���Ŏg�p���Ă��邩
     */
    public function isMainAlias($alias)
    {
        return ($this->main_alias == $alias);
    }

    /**
     * �v�f�̃I�v�V�����l���f�[�^�\�[�X����擾����
     *
     * @param array �f�[�^�\�[�X�ݒ�
     * @return array �I�v�V�����l
     */
    public function getElementOptionList(array $data_source)
    {
        $table = $this->createTableObject($data_source['alias'], false);
        $table->set($data_source['name']);
        $table->set($data_source['value']);
        if (isset($data_source['where'])) {
            $condition = $this->createCondition();
            foreach ($data_source['where'] as $name => $value) {
                $condition->addEqual($name, $value);
            }
            $table->addCondition($condition);
        }
        if (isset($data_source['order'])) {
            foreach ($data_source['order'] as $name => $asc) {
                $table->addSortColumn($name, $asc);
            }
        }

        $options = array();
        foreach ($this->dao->select(array($table)) as $row) {
            $name = $row->{$data_source['name']};
            $value = $row->{$data_source['value']};
            $options[$name] = $value;
        }

        return $options;
    }

    /**
     * �ꗗ�\�������擾����
     *
     * @param int �y�[�W���iNULL�͑S���j
     * @param array �\�[�g�J����
     * @param array �����J����
     * @param int 1�y�[�W�̕\������
     * @param bool �G�N�X�|�[�g�t���O
     * @return array �ꗗ�\�����
     */
    public function getList($page_count, array $sorts=array(), array $parameters=array(), $row_count=0, $export_flag=false)
    {
        list($headers, $tables) = $this->createListTables($sorts, $parameters, $export_flag);
        $relation = $this->createRelation();
        if ($page_count === null) {
            return array($headers, $this->dao->select($tables, $relation), null);
        } else {
            $pager = $this->dao->getPager($row_count, $page_count);
            return array($headers, $this->dao->select($tables, $relation, $pager), $pager);
        }
    }

    /**
     * �ꗗ�\�������t�@�C���X�g���[���ɏ�������
     *
     * @param resource �t�@�C���X�g���[��
     * @param array �\�[�g�J����
     * @param array �����J����
     * @param string ��؂蕶��
     * @param string �͂ޕ���
     */
    public function writeListCsv(&$stream, array $sorts=array(), array $parameters=array(), $delimiter=',', $enclosure='"')
    {
        list($headers, $tables) = $this->createListTables($sorts, $parameters, true);

        // CSV�w�b�_�̏o��
        $headersOut = array();
        foreach ($headers as $header) {
            $headersOut[] = $header['name'];
        }
        fputcsv($stream, $headersOut, $delimiter, $enclosure);

        // CSV���e�̏o��
        $relation = $this->createRelation();
        $this->dao->writeStreamCsv($stream, $tables, $relation, $delimiter, $enclosure);
    }

    /**
     * �ꗗ�\���e�[�u���I�u�W�F�N�g���쐬����
     *
     * @param array �\�[�g�J����
     * @param array �����J����
     * @param bool �G�N�X�|�[�g�t���O
     * @return array �ꗗ�\���e�[�u���I�u�W�F�N�g
     */
    private function createListTables(array $sorts=array(), array $parameters=array(), $export_flag=false)
    {
        $elements = $this->config->getElements();

        // �ꗗ�ɕ\������v�f�̒��o
        $columns = array();
        foreach ($elements as $name => &$element) {
            if ($export_flag || $element->isDisplay()) {
                $sort = (int)$element->getSort();
                $alias = $element->getAlias();
                if (!$export_flag || $this->isMainAlias($alias)) {
                    $columns[$sort] = array($alias, $name);
                }
            }
        }
        ksort($columns, SORT_NUMERIC);

        // �\�[�g�����`�F�b�N
        foreach ($sorts as $name1 => $asc) {
            $match = false;
            foreach ($columns as $column) {
                $alias = $column[0];
                $name2  = $column[1];
                if (($name1 == $name2) && ($alias == $this->main_alias)) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                throw new SyL_InvalidParameterException('invalid sort parameter');
            }
        }

        // SQL�p�e�[�u���I�u�W�F�N�g�쐬�^���������ݒ�
        $headers = array();
        $tables  = array();
        $condition = $this->dao->createCondition();
        foreach ($columns as $column) {
            $alias = $column[0];
            $name  = $column[1];
            $headers[$name] = array(
              'name'    => $elements[$name]->getName(),
              'sort'    => false,
              'order'   => '0',
              'primary' => false,
              'image_display' => $elements[$name]->isImageDisplay()
            );

            // �e�[�u���I�u�W�F�N�g����
            if (!isset($tables[$alias])) {
                $tables[$alias] = $this->createTableObject($alias);
            }

            if ($alias == $this->main_alias) {
                // ���C���e�[�u���̂�

                // �\�[�g�\
                $headers[$name]['sort'] = true;

                // ���������̐ݒ�
                if (isset($parameters[$name])) {
                    list($value, $text_flag) = $parameters[$name];
                    if (is_array($value)) {
                        $in = array();
                        foreach ($value as $name1 => $value1) {
                            if (is_int($name1)) {
                                // �ʏ��select, checkbox
                                if (($value1 !== null) && ($value1 !== '')) {
                                    $in[] = $value1;
                                }
                            } else {
                                // �O���[�s���O�v�f
                            }
                        }
                        if (count($in) > 0) {
                            $condition->addIn($name, $in);
                        }
                    } else {
                        if (($value !== null) && ($value !== '')) {
                            $schema = $tables[$alias]->getColumnSchema($name);
                            // �e�L�X�g�t�B�[���h���A�e�L�X�g�n�J�����̂�LIKE����
                            if ($text_flag && (($schema['type'] == 'S') || ($schema['type'] == 'M'))) {
                                // TODO: �N�H�[�g���@��DB�ˑ��H
                                // $value = str_replace(array('%','_'), array('\%', '\_'), $value);
                                $condition->addLike($name, '%' . $value . '%');
                            } else {
                                $condition->addEqual($name, $value);
                            }
                        }
                    }
                }
            }

            $tables[$alias]->set($name);
        }

        // �v���C�}���[�̐ݒ�
        $current_sorts = $sorts;
        foreach ($tables[$this->main_alias]->getPrimary() as $primary) {
            if (isset($headers[$primary])) {
                $headers[$primary]['primary'] = true;
            }

            // ��L�[���\�[�g�����ɓ����Ă��Ȃ��ꍇ�͒ǉ�����
            if (!isset($current_sorts[$primary])) {
                // �f�t�H���g����
                $current_sorts[$primary] = true;
            }
        }

        reset($current_sorts);
        list($name, $asc) = each($current_sorts);
        $headers[$name]['order'] = $asc ? '1' : '2';

        // ���������̒ǉ�
        $tables[$this->main_alias]->addCondition($condition);
        // �\�[�g�����ǉ�
        foreach ($current_sorts as $name => $asc) {
            $tables[$this->main_alias]->addSortColumn($name, $asc);
        }

        return array($headers, $tables);
    }

    /**
     * 1���R�[�h�����擾����
     *
     * @param array ��L�[�l
     * @return array 1���R�[�h���
     */
    public function getRecord(array $id)
    {
        $table = $this->createTableObject($this->main_alias);
        $primary = $table->getPrimary();
        if (count($primary) == 0) {
            throw new SyL_DbPrimaryKeyNotFoundException("primary key not found");
        }
        if (count($id) != count($primary)) {
            throw new SyL_InvalidParameterException('primary key count not match (expected: ' . count($primary) . ' actual: ' . count($id) . ')');
        }
        foreach ($id as $name => $value) {
            if (!in_array($name, $primary)) {
                throw new SyL_InvalidParameterException("invalid primary key ({$name})");
            }
        }

        $elements = $this->config->getElements();

        // �ڍׂɕ\������v�f�̒��o
        $columns = array();
        foreach ($elements as $name => &$element) {
            if ($element->isDisplay()) {
                // �\���Ώۗv�f�̂�
                $sort = (int)$element->getSort();
                $alias = $element->getAlias();
                if ($this->isMainAlias($alias)) {
                    // ���C���e�[�u���̂�
                    $columns[$sort] = array($alias, $name);
                }
            }
        }
        ksort($columns, SORT_NUMERIC);

        foreach ($columns as $value) {
            $table->set($value[1]);
        }

        $condition = $this->createCondition();
        foreach ($primary as $i => $column) {
            $condition->addEqual($column, $id[$column]);
        }
        $table->addCondition($condition);

        $relation = $this->createRelation();

        $result = $this->dao->select(array($table), $relation);
        if (count($result) == 0) {
            throw new SyL_DbRowNotFoundException('record not found');
        }
        return $result[0];
    }
}
