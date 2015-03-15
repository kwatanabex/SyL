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
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * DAO用の結果セットレコードクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_DbDaoAccessRecord extends SyL_DbRecord
{
    /**
     * カラム別名（バリデータ用）
     * 
     * @var array
     */
    private $names = array();
    /**
     * バリデーション配列
     * 
     * @var array
     */
    private $validators = array();

    /**
     * コンストラクタ
     * 
     * @param array プロパティ配列
     * @param bool 固定化配列フラグ
     * @param array バリデーション配列
     */
    public function __construct(array $properties=array(), $fixed=false, array $validators=array())
    {
        parent::__construct($properties, $fixed);
        $this->validators = $validators;
    }

    /**
     * カラム名の別名をセットする
     * 
     * @param string カラム名
     * @param string 別名
     */
    public function setAliasName($name, $alias)
    {
        $this->names[$name] = $alias;
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function set($name, $value)
    {
        $name = strtoupper($name);
        $this->validate($name, $value);
        parent::set($name, $value);
    }

    /**
     * 複数プロパティをセットする
     *
     * @param array プロパティ配列
     */
    public function sets(array $values)
    {
        foreach ($values as $name => $value) {
            $this->set($name, $value);
        }
        parent::sets($values);
    }

    /**
     * カラムの検証を行う
     * 
     * @param string カラム名
     * @param mixed 設定値
     * @throws SyL_InvalidOperationException 指定したカラム名が定義されていない場合
     * @throws SyL_DbDaoValidateException 検証エラーが発生した場合
     */
    private function validate($name, $value)
    {
        if (isset($this->validators[$name])) {
            try {
                $alias = isset($this->names[$name]) ? $this->names[$name] : $name;
                $this->validators[$name]->execute($value, $alias);
            } catch (SyL_ValidationValidatorException $e) {
                throw new SyL_DbDaoValidateException(array($e->getMessage()));
            }
        }
    }

    /**
     * セットされた値を全て検証を行う
     * 
     * @throws SyL_DbDaoValidateException 検証エラーが発生した場合
     */
    public function validateAll()
    {
        $error_messages = array();
        foreach ($this->gets() as $name => $value) {
            if ($this->is($name)) {
                if (isset($this->validators[$name])) {
                    try {
                        $alias = isset($this->names[$name]) ? $this->names[$name] : $name;
                        $this->validators[$name]->execute($value, $alias);
                    } catch (SyL_ValidationValidatorException $e) {
                        $error_messages[$name] = $e->getMessage();
                    }
                }
            }
        }

        if (count($error_messages) > 0) {
            throw new SyL_DbDaoValidateException($error_messages);
        }
    }
}
