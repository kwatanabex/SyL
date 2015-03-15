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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

require_once 'SyL_DbSqlStatementAbstract.php';

/**
 * SQLステートメントクラス（エミュレート）
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_DbSqlStatementEmulator extends SyL_DbSqlStatementAbstract
{
    /**
     * SQLステートメントの分解配列
     * 
     * @var array
     */
    private $sqls = array();

    /**
     * 初期化処理
     *
     * 事前にSQLを分解して、変換しやすい形にする
     */
    protected function initialize()
    {
        $this->sqls = preg_split("/(\"[^\"]*\")|('[^']*')|(\?)|(:\w+)/i", $this->sql, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * SQL実行し実行結果を取得する
     * 
     * SyL_DbAbstract::exec と同等
     *
     * @return mixed 実行結果
     * @see SyL_DbAbstract#exec
     */
    public function exec()
    {
        return $this->db->exec($this->applySql());
    }

    /**
     * SQL実行し実行結果を取得する
     *
     * SyL_DbAbstract::query と同等
     *
     * @return array 実行結果
     * @see SyL_DbAbstract#query
     */
    public function query()
    {
        return $this->db->query($this->applySql());
    }

    /**
     * SQLステートメントに値をバインドし、結果を取得する
     *
     * @return array 実行結果
     */
    private function applySql()
    {
        $i = 1;
        $sql = '';
        foreach ($this->sqls as $str) {
            if ($str == '?') {
                if (isset($this->values[$i])) {
                    $str = $this->db->quote($this->values[$i]);
                    $i++;
                }
            } else if ($str[0] == ':') {
                if (isset($this->values[$str])) {
                    $str = $this->db->quote($this->values[$str]);
                }
            }
            $sql .= $str;
        }
        return $sql;
    }

    /**
     * SQLステートメントを開放する
     */
    public function close()
    {
        parent::close();
        $this->sqls = array();
    }
}
