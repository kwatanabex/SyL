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
 * SQLステートメントクラス （PDO）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbSqlStatementPdo extends SyL_DbSqlStatementAbstract
{
    /**
     * PDOStatementオブジェクト
     * 
     * @var PDOStatement
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
        $this->sql = $this->db->convertEncoding($this->sql);

        list($this->method) = explode(' ', ltrim($this->sql), 2);
        $this->method = strtolower($this->method);

        try {
            $this->stmt = $this->db->getResource()->prepare($this->sql);
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new SyL_DbSqlPrepareException($e->getMessage());
        }
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
            if (!$this->stmt->execute()) {
                throw new Exception($this->db->getLastErrorMessage());
            }
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage());
        }

        $result = null;
        switch ($this->method) {
        case 'select':
        case 'insert':
        case 'update':
        case 'delete':
            $result = $this->stmt->rowCount();
            break;
        }
        $this->stmt->closeCursor();

        return $result;
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * @return array 実行結果
     */
    public function query()
    {
        $this->applyParameters();

        try {
            if (!$this->stmt->execute()) {
                throw new Exception($this->db->getLastErrorMessage());
            }
        } catch (Exception $e) {
             throw new SyL_DbSqlExecuteException($e->getMessage());
        }

        $result = $this->stmt->fetchAll();
        $this->stmt->closeCursor();
        if ($result) {
            $result = $this->db->convertDecoding($result);
        }
        return $result;
    }

    /**
     * SQLステートメントに値をバインドし、結果を取得する
     */
    private function applyParameters()
    {
        foreach ($this->values as $index => &$value) {
            if (is_int($value) || is_float($value)) {
                $this->stmt->bindParam($index, $value, PDO::PARAM_INT);
            } else if ($value === null) {
                $this->stmt->bindParam($index, $value, PDO::PARAM_NULL);
            } else {
                $this->stmt->bindParam($index, $value, PDO::PARAM_STR);
            }
        }
    }

    /**
     * SQLステートメントを開放する
     */
    public function close()
    {
        parent::close();
        $this->stmt = null;
    }
}
