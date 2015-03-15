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

require_once 'SyL_DbSqlStatementAbstract.php';

/**
 * SQLステートメントクラス （Pgsql）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbSqlStatementPgsql extends SyL_DbSqlStatementAbstract
{
    /**
     * ステートメント名の一意識別子
     * 
     * @var int
     */
    private static $index = 1;
    /**
     * ステートメント名
     * 
     * @var string
     */
    private $name = null;
    /**
     * SQL操作名
     * 
     * @var string
     */
    private $method = '';

    /**
     * 初期化処理
     *
     * 事前にSQLを分解して、変換しやすい形にする
     */
    protected function initialize()
    {
        $this->name = 'syl_db_sql_stmt_' . self::$index++;
        list($this->method) = explode(' ', ltrim($this->sql), 2);
        $this->method = strtolower($this->method);

        if (strpos($this->sql, '$1') === false) {
            $this->sql = preg_replace_callback('/\?/', create_function('', 'static $i=1; return "\$" . $i++;'), $this->sql);
        }

        try {
            $result = pg_prepare($this->db->getResource(), $this->name, $this->sql);
            if ($result === false) {
                throw new Exception($this->db->getLastErrorMessage());
            }
        } catch (Exception $e) {
            throw new SyL_DbSqlPrepareException($e->getMessage());
        }
    }

    /**
     * バインド値を参照でセットする
     *
     * @param mixed バインド名
     * @param mixed バインド値
     */
    public function bindParam($name, &$value)
    {
        if (!is_int($name)) {
            throw new SyL_InvalidParameterException('invalid argument. name is integer only (' . gettype($name) . ')');
        }
        parent::bindParam($name, $value);
    }

    /**
     * バインド値をセットする
     *
     * @param mixed バインド名
     * @param mixed バインド値
     */
    public function bindValue($name, $value)
    {
        if (!is_int($name)) {
            throw new SyL_InvalidParameterException('invalid argument. name is integer only (' . gettype($name) . ')');
        }
        parent::bindValue($name, $value);
    }

    /**
     * SQLを実行し、結果取得
     *
     * @return mixed 実行結果
     */
    public function exec()
    {
        ksort($this->values);

        $query = null;
        try {
            $query = pg_execute($this->db->getResource(), $this->name, $this->values);
            if ($query === false) {
                throw new Exception($this->db->getLastErrorMessage());
            }
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage());
        }

        $result = null;
        switch ($this->method) {
        case 'select':
            $result = pg_num_rows($query);
            break;
        case 'insert':
        case 'update':
        case 'delete':
            $result = pg_affected_rows($query);
            break;
        }
        pg_free_result($query);
        $query = null;

        return $result;
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * @return array 実行結果
     */
    public function query()
    {
        ksort($this->values);

        $query = null;
        try {
            $query = pg_execute($this->db->getResource(), $this->name, $this->values);
            if ($query === false) {
                throw new Exception($this->db->getLastErrorMessage());
            }
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage());
        }

        $record_class_name = SyL_DbAbstract::getRecordClassName();

        $result = array();
        while ($row = pg_fetch_object($query, null, $record_class_name)) {
            $result[] = $row;
        }
        pg_free_result($query);
        $query = null;

        return $result;
    }
}
