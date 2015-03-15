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

/**
 * SQLステートメントクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_DbSqlStatementAbstract
{
    /**
     * DBクラス
     * 
     * @var SyL_DbAbstract
     */
    protected $db = null;
    /**
     * SQLステートメント
     * 
     * @var string
     */
    protected $sql = '';
    /**
     * バインドパラメータ
     * 
     * @var array
     */
    protected $values = array();

    /**
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @param string SQLステートメント
     */
    public function __construct(SyL_DbAbstract $db, $sql)
    {
        $this->db  = $db;
        $this->sql = $sql;

        $this->initialize();
    }

    /**
     * 初期化処理
     */
    protected abstract function initialize();

    /**
     * バインド値を参照でセットする
     *
     * @param mixed バインド名
     * @param mixed バインド値
     * @throws SyL_InvalidParameterException スカラ以外のバインド値の場合
     */
    public function bindParam($name, &$value)
    {
        if (!is_scalar($value)) {
            throw new SyL_InvalidParameterException('invalid argument. value not scalar (' . gettype($value) . ')');
        }
        $this->values[$name] =& $value;
    }

    /**
     * バインド値をセットする
     *
     * @param mixed バインド名
     * @param mixed バインド値
     * @throws SyL_InvalidParameterException スカラ以外のバインド値の場合
     */
    public function bindValue($name, $value)
    {
        if (!is_scalar($value)) {
            throw new SyL_InvalidParameterException('invalid argument. value not scalar (' . gettype($value) . ')');
        }
        $this->values[$name] = $value;
    }

    /**
     * SQL実行し実行結果を取得する
     * 
     * SyL_DbAbstract::exec と同等
     *
     * @return mixed 実行結果
     * @see SyL_DbAbstract#exec
     */
    public abstract function exec();

    /**
     * SQL実行し実行結果を取得する
     *
     * SyL_DbAbstract::query と同等
     *
     * @return array 実行結果
     * @see SyL_DbAbstract#query
     */
    public abstract function query();

    /**
     * SQLステートメントを開放する
     */
    public function close()
    {
        $this->values = array();
    }
}
