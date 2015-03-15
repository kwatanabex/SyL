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
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** DAOクラス */
require_once 'SyL_DbDao.php';
/** DAO用の結果セットレコードクラス */
require_once 'SyL_DbDaoAccessRecord.php';
/** 検証クラス */
require_once dirname(__FILE__) . '/../Validation/SyL_ValidationAbstract.php';

/**
 * DAOアクセスクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_DbDaoAccessAbstract
{
    /**
     * DAOオブジェクト
     * 
     * @var SyL_DbDao
     */
    protected $dao = null;
    /**
     * メインテーブル別名
     * 
     * @var string
     */
    protected $main_alias = 'a';
    /**
     * テーブルクラスの配列
     * 
     * @var array
     */
    protected $class_names = array (
/*
      'a' => 'DaoEntityAq_adm_element_validation',
      'b' => 'DaoEntityAq_adm_element',
      'c' => 'DaoEntityAq_adm_validation',
*/
    );
    /**
     * テーブルクラスの関連配列
     * 
     * @var array
     */
    protected $relations = array(
/*
      'a=b' => 'ELEMENT_ID',
      'a=c' => 'VALIDATION_ID = VALIDATION_ID'
*/
    );

    /**
     * バリデーション配列
     * 
     * @var array
     */
    private $validations = array();
    /**
     * DAO基準の型タイプ
     * 
     * @var array
     */
    private $format_types = array();

    /**
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @throws SyL_InvalidParameterException テーブルクラスが定義されていない、SyL_DbDaoTableAbstract クラスを継承していない場合
     */
    public function __construct(SyL_DbAbstract $db)
    {
        $this->dao = new SyL_DBDao($db);

        if (count($this->class_names) == 0) {
            throw new SyL_InvalidParameterException('table class not found');
        }
        if (!isset($this->class_names[$this->main_alias])) {
            throw new SyL_InvalidParameterException('main table class not found');
        }
        foreach ($this->class_names as $class_name) {
            if (!is_subclass_of($class_name, 'SyL_DbDaoTableAbstract')) {
                throw new SyL_InvalidParameterException("table class not subclass of SyL_DbDaoTableAbstract ({$class_name})");
            }
        }

        foreach ($this->createTableObjects() as $alias => $table) {
            foreach ($table->getColumnNames() as $name) {
                $schema = $table->getColumnSchema($name);
                $this->format_types[$name] = $schema['type'];
                if (($alias == $this->main_alias) && isset($schema['validation'])) {
                    $vs = SyL_ValidationAbstract::createValidators();
                    foreach ($schema['validation'] as $v_name => $v_values) {
                        $v_parameters = isset($v_values['parameters']) ? $v_values['parameters'] : array();
                        $vs->addValidator(SyL_ValidationAbstract::createValidator($v_name, $v_values['message'], $v_parameters));
                    }
                    $this->validations[$name] = $vs;
                }
            }
        }
    }

    /**
     * DAO基準の型タイプを取得する
     *
     * @return array DAO基準の型タイプ配列
     */
    public function getFormatTypes()
    {
        return $this->format_types;
    }

    /**
     * バリデーションを追加する
     *
     * @param string バリデーション対象名
     * @param SyL_ValidationValidatorAbstract バリデータ
     */
    public function addValidation($name, SyL_ValidationValidatorAbstract $validator)
    {
        if (!isset($this->validations[$name])) {
            $this->validations[$name] = SyL_ValidationAbstract::createValidators();
        }
        $this->validations[$name]->addValidator($validator);
    }

    /**
     * バリデーションを取得する
     *
     * @return array バリデーション配列
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * 結果セットレコードオブジェクトを作成する
     * 
     * @param bool 更新レコード作成フラグ
     * @return SyL_DbRecord 結果セットレコードオブジェクト
     */
    public function createRecord($update=false)
    {
        $columns = array();
        $table = $this->createTableObject($this->main_alias);
        foreach ($table->getColumnNames() as $name) {
            $columns[$name] = null;
        }

        $record = new SyL_DbDaoAccessRecord($columns, true, $this->validations);
        if ($update) {
            $record->startUpdateSetting();
        }
        return $record;
    }

    /**
     * DAOテーブル条件オブジェクトを作成する
     *
     * @return SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     */
    public function createCondition()
    {
        return new SyL_DbDaoTableConditions();
    }

    /**
     * 主キーを指定してデータを取得する
     * 
     * @param string 主キー値
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return SyL_DbRecord 結果セットレコードオブジェクト
     * @throws SyL_DbPrimaryKeyNotFoundException テーブルクラスにプライマリキーが定義されていない場合
     * @throws SyL_InvalidParameterException 引数の数とプライマリキーの数が一致しない場合
     */
    public function select($id, SyL_DbDaoTableConditions $condition=null)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        $tables = $this->createTableObjects();
        $primary = $tables[$this->main_alias]->getPrimary();
        if (count($primary) == 0) {
            throw new SyL_DbPrimaryKeyNotFoundException("primary key not found in table class");
        }
        if (count($id) != count($primary)) {
            throw new SyL_InvalidParameterException('primary key count not match (expected: ' . count($primary) . ' actual: ' . count($id) . ')');
        }

        if ($condition == null) {
            $condition = $this->createCondition();
        }
        foreach ($primary as $i => $column) {
            $condition->addEqual($column, $id[$i]);
        }
        $tables[$this->main_alias]->addCondition($condition);

        $result = $this->dao->select($tables, $this->createRelation());
        return (count($result) > 0) ? $result[0] : null;
    }

    /**
     * 条件を指定してデータを取得する
     * 
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return array 結果セットレコードオブジェクトの配列
     */
    public function selects(SyL_DbDaoTableConditions $condition, array $sorts=array())
    {
        $tables = array();
        $tables[$this->main_alias] = $this->createTableObject($this->main_alias);
        $tables[$this->main_alias]->addCondition($condition);
        foreach ($sorts as $name => $asc) {
            $tables[$this->main_alias]->addSortColumn($name, $asc);
        }
        return $this->dao->select($tables, $this->createRelation());
    }

    /**
     * データを登録する
     * 
     * @param SyL_DbDaoAccessRecord 登録レコード
     * @return int 影響件数
     */
    public function insert(SyL_DbDaoAccessRecord $record)
    {
        $table = $this->createTableObject($this->main_alias);
        foreach ($table->getColumnNames() as $name) {
            if ($record->is($name)) {
                $table->set($name, $record->get($name));
            }
        }
        return $this->dao->insert($table);
    }

    /**
     * 主キーを指定してデータを更新する
     * 
     * @param SyL_DbDaoAccessRecord 更新レコード
     * @param string 主キー値
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return int 影響件数
     * @throws SyL_DbPrimaryKeyNotFoundException テーブルクラスにプライマリキーが定義されていない場合
     * @throws SyL_InvalidParameterException 引数の数とプライマリキーの数が一致しない場合
     */
    public function update(SyL_DbDaoAccessRecord $record, $id, SyL_DbDaoTableConditions $condition=null)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        $table = $this->createTableObject($this->main_alias, false);
        $primary = $table->getPrimary();
        if (count($primary) == 0) {
            throw new SyL_DbPrimaryKeyNotFoundException("primary key not found in table class ({$class_name})");
        }
        if (count($id) != count($primary)) {
            throw new SyL_InvalidParameterException('primary key id not match (expected: ' . count($primary) . ' actual: ' . count($id) . ')');
        }

        if ($condition == null) {
            $condition = $this->createCondition();
        }
        foreach ($primary as $i => $column) {
            $condition->addEqual($column, $id[$i]);
        }
        $table->addCondition($condition);

        foreach ($table->getColumnNames() as $column) {
            if ($record->is($column, true)) {
                $table->set($column, $record->get($column));
            }
        }
        return $this->dao->update($table);
    }

    /**
     * 条件を指定してデータを更新する
     * 
     * @param SyL_DbDaoAccessRecord 更新レコード
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return int 影響件数
     */
    public function updates(SyL_DbDaoAccessRecord $record, SyL_DbDaoTableConditions $condition)
    {
        $table = $this->createTableObject($this->main_alias, false);
        $table->addCondition($condition);

        foreach ($table->getColumnNames() as $column) {
            if ($record->is($column, true)) {
                $table->set($column, $record->get($column));
            }
        }
        return $this->dao->update($table);
    }

    /**
     * 主キーを指定してデータを削除する
     * 
     * @param string 主キー値
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return int 影響件数
     * @throws SyL_DbPrimaryKeyNotFoundException テーブルクラスにプライマリキーが定義されていない場合
     * @throws SyL_InvalidParameterException 引数の数とプライマリキーの数が一致しない場合
     */
    public function delete($id, SyL_DbDaoTableConditions $condition=null)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        $table = $this->createTableObject($this->main_alias, false);
        $primary = $table->getPrimary();
        if (count($primary) == 0) {
            throw new SyL_DbPrimaryKeyNotFoundException("primary key not found in table class ({$class_name})");
        }
        if (count($id) != count($primary)) {
            throw new SyL_InvalidParameterException('primary key id not match (expected: ' . count($primary) . ' actual: ' . count($id) . ')');
        }

        if ($condition == null) {
            $condition = $this->createCondition();
        }
        foreach ($primary as $i => $column) {
            $condition->addEqual($column, $id[$i]);
        }
        $table->addCondition($condition);
        return $this->dao->delete($table);
    }

    /**
     * 条件を指定してデータを削除する
     * 
     * @param SyL_DbDaoTableConditions DAOテーブル条件オブジェクト
     * @return int 影響件数
     * @throws SyL_DbPrimaryKeyNotFoundException テーブルクラスにプライマリキーが定義されていない場合
     * @throws SyL_InvalidParameterException 引数の数とプライマリキーの数が一致しない場合
     */
    public function deletes(SyL_DbDaoTableConditions $condition)
    {
        $table = $this->createTableObject($this->main_alias, false);
        $table->addCondition($condition);
        return $this->dao->delete($table);
    }

    /**
     * レコードの検証を行う
     * 
     * @param SyL_DbRecord 検証パラメータ
     * @param array 更新前の主キー値
     * @throws SyL_DbDaoValidateException 検証エラーが発生した場合
     */
    public function validate(SyL_DbDaoAccessRecord $record, array $org_keys=array())
    {
        $table = $this->createTableObject($this->main_alias);

        $error_messages = array();

        // カラムチェック
        try {
            $record->validateAll();
        } catch (SyL_DbDaoValidateException $e) {
            $error_messages = array_merge($error_messages, $e->getMessages());
        }

        // 主キーチェック
        $primary = $table->getPrimary();
        $check = false;
        $check_key = true;
        $no_change = true;
        foreach ($primary as $name) {
            $check = ($record->is($name) && !isset($error_messages[$name]));
            if (!$check) break;
            $value = $record->get($name);
            $check = (($value !== null) && ($value !== ''));
            if (!$check) break;
            $check_key = ($check_key && isset($org_keys[$name]));
            if ($check_key && $no_change) {
                $no_change = ($value == $org_keys[$name]);
            }
        }

        if ($check) {
            if ($record->isUpdateSetting()) {
                // 更新時
                if ($check_key) {
                    // 1レコード更新
                    if ($no_change) {
                        // 元のレコードと同値の場合チェックなし
                    } else {
                        // 元のレコードと異なる場合は、重複チェック
                        $table_tmp = clone $table;
                        $condition = $this->createCondition();
                        foreach ($primary as $name) {
                            $condition->addEqual($name, $org_keys[$name]);
                        }
                        $table_tmp->addCondition($condition);
                        $result = $this->dao->select(array($table_tmp));
                        if (count($result) > 0) {
                            $error_messages[$primary[0]] = 'primary value already exists (' . implode(', ', $primary) . ')';
                        }
                        $table_tmp = null;
                    }
                } else {
                    // 主キーが修正されているが、元のレコードが指定されていないためエラー
                    throw new SyL_InvalidParameterException('original primary key not found');
                }
            } else {
                // 登録時
                $table_tmp = clone $table;
                $condition = $this->createCondition();
                foreach ($primary as $name) {
                    $condition->addEqual($name, $record->get($name));
                }
                $table_tmp->addCondition($condition);
                $result = $this->dao->select(array($table_tmp));
                if (count($result) > 0) {
                    $error_messages[$primary[0]] = 'primary value already exists (' . implode(', ', $primary) . ')';
                }
                $table_tmp = null;
            }
        }

        // 一意キーチェック
        foreach ($table->getUniques() as $unique) {
            if (!isset($error_messages[$unique[0]])) {
                if ($record->is($unique[0])) {
                    // 値がセットされている場合は必須
                    $table_tmp = clone $table;
                    $condition = $this->createCondition();
                    foreach ($unique as $name) {
                        $condition->addEqual($name, $record->get($name));
                    }
                    $table_tmp->addCondition($condition);
                    $result = $this->dao->select(array($table_tmp));
                    if (count($result) > 0) {
                        $error_messages[$unique[0]] = 'unique value already exists';
                    }
                } else {
                    // 値がセットされていない場合のチェックは、カラムのチェックで行われている
                }
            }
        }

        // 外部キーチェック
        foreach ($table->getForeigns() as $name => $foreign) {
            $check = true;
            $condition = $this->createCondition();
            $check_name = null;
            foreach ($foreign as $name1 => $name2) {
                if (!$check_name) {
                    if (isset($error_messages[$name1])) {
                        break 2;
                    }
                    $check_name = $name1;
                }
                if (!$record->is($name1)) {
                    // 値がセットされていない場合は任意
                    $check = false;
                    break;
                }
                $condition->addEqual($name2, $record->get($name1));
            }

            if ($check) {
                $where = $condition->create($this->dao->getDB(), $table);
                $result = $this->dao->getDB()->query("SELECT COUNT(*) as CNT FROM {$name} WHERE {$where}");
                if ($result[0]->CNT == 0) {
                    $error_messages[$check_name] = "foreign value not exists in ({$name})";
                    break;
                }
            } else {
                // 値がセットされていない場合のチェックは、カラムのチェックで行われている
            }
        }

        if (count($error_messages) > 0) {
            throw new SyL_DbDaoValidateException($error_messages);
        }
    }

    /**
     * 関連オブジェクトを作成する
     *
     * @param array 関連オブジェクトに含めるテーブル別名の配列
     * @return SyL_DbDaoTableRelations 関連オブジェクト
     */
    protected function createRelation()
    {
        $relation = $this->dao->createRelation();

        foreach ($this->relations as $join => $rel) {
            $values = explode(',', $rel);
            $alias1 = '';
            $alias2 = '';
            $tables = array();
            if (preg_match('/^(.+)([\+\=])(.+)$/', $join, $matches)) {
                $alias1 = $matches[1];
                $join   = $matches[2];
                $alias2 = $matches[3];

                if (!isset($tables[$alias1])) {
                    $tables[$alias1] = $this->createTableObject($alias1);
                }
                if (!isset($tables[$alias2])) {
                    $tables[$alias2] = $this->createTableObject($alias2);
                }
            } else {
                throw new SyL_InvalidParameterException("invalid join relation ({$rel})");
            }
            $columns = array();
            foreach ($values as $column) {
                $columns[] = str_replace('=', ',', $column);
            }

            switch ($join) {
            case '=': $relation->addInnerJoin($tables[$alias1], $tables[$alias2], $columns); break;
            case '+': $relation->addLeftJoin($tables[$alias1], $tables[$alias2], $columns); break;
            default: throw new SyL_InvalidParameterException("invalid join type ({$join})");
            }
        }
        return $relation;
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        $this->dao->getDB()->beginTransaction();
    }

    /**
     * トランザクションを確定する
     */
    public function commit()
    {
        $this->dao->getDB()->commit();
    }

    /**
     * トランザクションを破棄する
     */
    public function rollBack()
    {
        $this->dao->getDB()->rollBack();
    }

    /**
     * 最後に挿入された行の ID あるいはシーケンスの値を取得
     *
     * @param string シーケンス名
     * @return int 最後に挿入された行のID
     */
    public function getLastInsertId($sequence_name='')
    {
        return $this->dao->getDB()->getLastInsertId($sequence_name);
    }

    /**
     * 全テーブルオブジェクトを作成する
     * 
     * @return array 全テーブルオブジェクト
     */
    protected function createTableObjects()
    {
        $tables = array();
        foreach ($this->class_names as $alias => $class_name) {
            $tables[$alias] = $this->createTableObject($alias);
        }
        return $tables;
    }

    /**
     * テーブルオブジェクトを作成する
     * 
     * @param string テーブル別名
     * @param bool 別名セットフラグ
     * @return SyL_DbDaoTableAbstract テーブルオブジェクト
     */
    protected function createTableObject($alias=null, $alias_setting=true)
    {
        if (!$alias) {
            $alias = $this->main_alias;
        }
        $class_name = $this->class_names[$alias];
        $table = new $class_name();
        if ($alias_setting) {
            $table->setAliasName($alias);
        }
        return $table;
    }
}
