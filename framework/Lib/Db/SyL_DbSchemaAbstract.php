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
 * DBスキーマ取得クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Db
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_DbSchemaAbstract
{
    /**
     * DBクラス
     * 
     * @var SyL_DbAbstract
     */
    protected $db = null;

    /**
     * コンストラクタ
     *
     * @param SyL_DbAbstract DBオブジェクト
     */
    protected function __construct(SyL_DbAbstract $db)
    {
        $this->db  = $db;
    }

    /**
     * DBスキーマ取得オブジェクトを取得する
     *
     * @param SyL_DbAbstract DBオブジェクト
     * @return SyL_DbSchemaAbstract DBスキーマ取得オブジェクト
     */
    public static function createInstance(SyL_DbAbstract $db)
    {
        switch (get_class($db)) {
        case 'SyL_DbDriverMysql':
        case 'SyL_DbDriverMysqli':
        case 'SyL_DbDriverPdoMysql':
            include_once 'SyL_DbSchemaMysql.php';
            return new SyL_DbSchemaMysql($db);
        case 'SyL_DbDriverPgsql':
        case 'SyL_DbDriverPdoPgsql':
            include_once 'SyL_DbSchemaPgsql.php';
            return new SyL_DbSchemaPgsql($db);
        case 'SyL_DbDriverSqlite':
        case 'SyL_DbDriverPdoSqlite':
            include_once 'SyL_DbSchemaSqlite.php';
            return new SyL_DbSchemaSqlite($db);
        default:
            throw new SyL_NotImplementedException('schema class not implemented (' . get_class($db) . ')');
        }
    }

    /**
     * 接続しているDBに対するテーブル一覧を取得する
     *
     * 取得できるテーブル一覧配列は以下の形式
     * array (
     *  [0] => array (
     *           'name' => テーブル名,
     *           'schema' => スキーマ名,
     *           'owner' => オーナー名,
     *         ),
     *  [1] => ...
     * )
     *
     * @return array テーブル一覧
     */
    public abstract function getTables();

    /**
     * 接続しているDBに対するビュー一覧を取得する
     *
     * 取得できるビュー一覧配列は以下の形式
     * array (
     *  [0] => array (
     *           'name' => ビュー名,
     *           'schema' => スキーマ名,
     *           'owner' => オーナー名,
     *         ),
     *  [1] => ...
     * )
     *
     * @return array ビュー一覧
     */
    public abstract function getViews();

    /**
     * 指定したテーブルに対するカラム情報を取得する
     *
     * 取得できるカラム情報配列は下記の形式
     * array (
     *  [カラム名1] => array (
     *           'type' => カラム型,
     *           'simple_type' => 簡易カラム型,
     *           'min' => 最小値,
     *           'not_null' => NULL不許可,
     *           'default' => デフォルト値の有無,
     *         ),
     *  [カラム名2] => ...
     * )
     *
     * 簡易カラム型の分類は下記の形式
     *   I  - 整数
     *   S  - 文字列（バイト）
     *   M  - 文字列（文字長）
     *   N  - 固定小数点
     *   F  - 浮動小数点数
     *   DT - 日時
     *   D  - 日付
     *   T  - 時間
     *
     * カラムが取得できない場合は、空の配列を返す
     *
     * @return array カラム情報
     * @throws SyL_DbTableNotFoundException テーブルが存在しない場合
     */
    public abstract function getColumns($name);

    /**
     * 指定したテーブルの主キーカラムを取得する
     *
     * 取得できる主キーカラム配列は下記の形式
     * array (
     *     [0] => 主キーカラム1
     *     [1] => ...
     * )
     *
     * 主キーカラムが取得できない場合は、空の配列を返す
     *
     * @return array 主キーカラム
     */
    public abstract function getPrimaryColumns($name);

    /**
     * 指定したテーブルの一意キーカラムを取得する
     *
     * 取得できる一意キーカラム配列は下記の形式
     * array (
     *  [0] => array (
     *          [0] => 一意キーカラム1
     *          [1] => ...
     *         ),
     *  [1] => ...
     * )
     *
     * 一意キーカラムが取得できない場合は、空の配列を返す
     *
     * @return array 一意キーカラム
     */
    public abstract function getUniqueColumns($name);

    /**
     * 指定したテーブルの外部キーカラムを取得する
     *
     * 取得できる一意キーカラム配列は下記の形式
     * array (
     *  [外部テーブル名1] => array (
     *                       [元のカラム1] => 外部テーブルのカラム1
     *                       [元のカラム2] => ...
     *         ),
     *  [外部テーブル名2] => ...
     * )
     *
     * 外部キーカラムが取得できない場合は、空の配列を返す
     *
     * @return array 外部キーカラム
     */
    public abstract function getForeignColumns($name);

    /**
     * 指定したテーブルのシーケンス（自動採番）カラムを取得する
     *
     * シーケンスが取得できない場合は、NULLを返す
     *
     * @return string シーケンス（自動採番）カラム
     */
    public abstract function getAutoIncrementColumn($name);

}
