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
 * SQLステートメントクラス （Mysqli）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbSqlStatementMysqli extends SyL_DbSqlStatementAbstract
{
    /**
     * MySQLi_STMTオブジェクト
     * 
     * @var MySQLi_STMT
     */
    private $stmt = null;
    /**
     * SQL操作名
     * 
     * @var string
     */
    private $method = '';

    /**
     * 初期化処理
     */
    protected function initialize()
    {
        list($this->method) = explode(' ', ltrim($this->sql), 2);
        $this->method = strtolower($this->method);

        try {
            $this->stmt = mysqli_prepare($this->db->getResource(), $this->sql);
            if ($this->stmt === false) {
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
        $this->applyParameters();

        try {
            mysqli_stmt_execute($this->stmt);
            if (mysqli_stmt_errno($this->stmt) != 0) {
                throw new Exception(mysqli_stmt_error($this->stmt));
            }
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage());
        }

        $result = null;
        switch ($this->method) {
        case 'select':
            $result = mysqli_stmt_num_rows($this->stmt);
            break;
        case 'insert':
        case 'update':
        case 'delete':
            $result = mysqli_stmt_affected_rows($this->stmt);
            break;
        }
        return $result;
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * @return array 実行結果
     */
    public function query()
    {
        throw new SyL_NotImplementedException('`' . __METHOD__ . "' method not implemented in `" . __CLASS__ . "' class");
    }

    /**
     * SQLステートメントに値をバインドし、結果を取得する
     */
    private function applyParameters()
    {
        ksort($this->values);

        $types = '';
        foreach ($this->values as $index => $value) {
            if (is_float($value)) {
                $types .= 'd';
            } else if (is_int($value)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
        }
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($this->stmt, $types), $this->values));
    }

    /**
     * SQLステートメントを開放する
     */
    public function close()
    {
        parent::close();
        mysqli_stmt_free_result($this->stmt);
        mysqli_stmt_close($this->stmt);
        $this->stmt = null;
    }
}
