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

/** DAOアクセスクラス */
require_once dirname(__FILE__) . '/../Db/SyL_DbDaoAccessAbstract.php';

/**
 * CRUD DBアクセスクラス
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
     * CRUD設定オブジェクト
     *
     * @var SyL_CrudConfigAbstract
     */
    protected $config = null;

    /**
     * コンストラクタ
     *
     * @param SyL_CrudConfigAbstract CRUD設定オブジェクト
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
     * DAOディレクトリを取得する
     *
     * @return string DAOディレクトリ
     */
    protected abstract function getDaoDirectory();

    /**
     * DBコネクションを取得する
     *
     * @return SyL_DbAbstract DBコネクション
     */
    public abstract function getConnection();

    /**
     * 結果セットレコードオブジェクトを作成する
     * 
     * @param bool 更新レコード作成フラグ
     * @return SyL_DbRecord 結果セットレコードオブジェクト
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
     * 別名がメインで使用しているか判定する
     *
     * @param string 別名
     * @return bool 別名がメインで使用しているか
     */
    public function isMainAlias($alias)
    {
        return ($this->main_alias == $alias);
    }

    /**
     * 要素のオプション値をデータソースから取得する
     *
     * @param array データソース設定
     * @return array オプション値
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
     * 一覧表示情報を取得する
     *
     * @param int ページ数（NULLは全件）
     * @param array ソートカラム
     * @param array 条件カラム
     * @param int 1ページの表示件数
     * @param bool エクスポートフラグ
     * @return array 一覧表示情報
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
     * 一覧表示情報をファイルストリームに書き込む
     *
     * @param resource ファイルストリーム
     * @param array ソートカラム
     * @param array 条件カラム
     * @param string 区切り文字
     * @param string 囲む文字
     */
    public function writeListCsv(&$stream, array $sorts=array(), array $parameters=array(), $delimiter=',', $enclosure='"')
    {
        list($headers, $tables) = $this->createListTables($sorts, $parameters, true);

        // CSVヘッダの出力
        $headersOut = array();
        foreach ($headers as $header) {
            $headersOut[] = $header['name'];
        }
        fputcsv($stream, $headersOut, $delimiter, $enclosure);

        // CSV内容の出力
        $relation = $this->createRelation();
        $this->dao->writeStreamCsv($stream, $tables, $relation, $delimiter, $enclosure);
    }

    /**
     * 一覧表示テーブルオブジェクトを作成する
     *
     * @param array ソートカラム
     * @param array 条件カラム
     * @param bool エクスポートフラグ
     * @return array 一覧表示テーブルオブジェクト
     */
    private function createListTables(array $sorts=array(), array $parameters=array(), $export_flag=false)
    {
        $elements = $this->config->getElements();

        // 一覧に表示する要素の抽出
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

        // ソート条件チェック
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

        // SQL用テーブルオブジェクト作成／検索条件設定
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

            // テーブルオブジェクト生成
            if (!isset($tables[$alias])) {
                $tables[$alias] = $this->createTableObject($alias);
            }

            if ($alias == $this->main_alias) {
                // メインテーブルのみ

                // ソート可能
                $headers[$name]['sort'] = true;

                // 検索条件の設定
                if (isset($parameters[$name])) {
                    list($value, $text_flag) = $parameters[$name];
                    if (is_array($value)) {
                        $in = array();
                        foreach ($value as $name1 => $value1) {
                            if (is_int($name1)) {
                                // 通常のselect, checkbox
                                if (($value1 !== null) && ($value1 !== '')) {
                                    $in[] = $value1;
                                }
                            } else {
                                // グルーピング要素
                            }
                        }
                        if (count($in) > 0) {
                            $condition->addIn($name, $in);
                        }
                    } else {
                        if (($value !== null) && ($value !== '')) {
                            $schema = $tables[$alias]->getColumnSchema($name);
                            // テキストフィールドかつ、テキスト系カラムのみLIKE検索
                            if ($text_flag && (($schema['type'] == 'S') || ($schema['type'] == 'M'))) {
                                // TODO: クォート方法がDB依存？
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

        // プライマリーの設定
        $current_sorts = $sorts;
        foreach ($tables[$this->main_alias]->getPrimary() as $primary) {
            if (isset($headers[$primary])) {
                $headers[$primary]['primary'] = true;
            }

            // 主キーがソート条件に入っていない場合は追加する
            if (!isset($current_sorts[$primary])) {
                // デフォルト昇順
                $current_sorts[$primary] = true;
            }
        }

        reset($current_sorts);
        list($name, $asc) = each($current_sorts);
        $headers[$name]['order'] = $asc ? '1' : '2';

        // 検索条件の追加
        $tables[$this->main_alias]->addCondition($condition);
        // ソート条件追加
        foreach ($current_sorts as $name => $asc) {
            $tables[$this->main_alias]->addSortColumn($name, $asc);
        }

        return array($headers, $tables);
    }

    /**
     * 1レコード情報を取得する
     *
     * @param array 主キー値
     * @return array 1レコード情報
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

        // 詳細に表示する要素の抽出
        $columns = array();
        foreach ($elements as $name => &$element) {
            if ($element->isDisplay()) {
                // 表示対象要素のみ
                $sort = (int)$element->getSort();
                $alias = $element->getAlias();
                if ($this->isMainAlias($alias)) {
                    // メインテーブルのみ
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
