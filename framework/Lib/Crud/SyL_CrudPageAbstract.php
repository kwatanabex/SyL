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

/**
 * Crud ページクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_CrudPageAbstract
{
    /**
     * CRUD設定オブジェクト
     *
     * @var SyL_CrudConfigAbstract
     */
    protected $config = null;
    /**
     * レコードを特定するプライマリ情報
     *
     * @var array
     */
    private $id = array();
    /**
     * 内部文字コード
     *
     * @var string
     */
    protected static $internal_encoding = 'UTF-8';
    /**
     * ロケール情報
     *
     * @var string
     */
    protected static $internal_locale = 'ja_JP.UTF-8';

    /**
     * コンストラクタ
     *
     * @param SyL_CrudConfigAbstract CRUD設定オブジェクト
     */
    protected function __construct(SyL_CrudConfigAbstract $config)
    {
        $this->config = $config;
    }

    /**
     * Crud ページクラスのインスタンスを作成する
     *
     * @return SyL_CrudPageAbstract Crud ページオブジェクト
     */
    public static function createInstance(SyL_CrudConfigAbstract $config, $internal_encoding='UTF-8')
    {
        $page = null;

        switch ($config->getCrudType()) {
        case SyL_CrudConfigAbstract::CRUD_TYPE_LST:
            $classname = 'SyL_CrudPageLst';
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_NEW:
            $classname = 'SyL_CrudPageNew';
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_VEW:
            $classname = 'SyL_CrudPageVew';
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_EDT:
            $classname = 'SyL_CrudPageEdt';
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_RSS:
            $classname = 'SyL_CrudPageRss';
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_ATM:
            $classname = 'SyL_CrudPageAtm';
            break;
        default:
            throw new SyL_ClassNotFoundException('invalid crud_type (' . $config->getCrudType() . ')');
        }

        self::$internal_encoding = $internal_encoding;

        include_once $classname . '.php';
        $page = new $classname($config);

        return $page;
    }

    /**
     * レコードを特定するプライマリ情報をセットする
     *
     * @param string レコードを特定するプライマリ情報
     */
    public function setId($id)
    {
        $this->id = self::decodeId($id);
    }

    /**
     * レコードを特定するプライマリ情報を取得する
     *
     * @return array レコードを特定するプライマリ情報
     */
    protected function getId()
    {
        return $this->id;
    }

    /**
     * CRUD名を取得する
     *
     * @return string CRUD名
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * 構成の説明を取得する
     *
     * @return string 構成の説明
     */
    public function getDescription()
    {
        return $this->config->getDescription();
    }

    /**
     * 構成の説明を取得する
     *
     * @return string 構成の説明
     */
    public function getElements()
    {
        return $this->config->getElements();
    }

    /**
     * 入力画面構成情報を取得する
     *
     * @return array 入力画面名
     */
    public function getInputPages()
    {
        return $this->config->getInputPages();
    }

    /**
     * 画面遷移時のプライマリーパラメータ情報をエンコードする
     *
     * @param array プライマリーパラメータ情報
     * @return string エンコードしたプライマリーパラメータ情報
     */
    protected static function encodeId(array $id)
    {
        return urlencode(base64_encode(serialize($id)));
    }

    /**
     * 画面遷移時のプライマリーパラメータ情報をデコードする
     *
     * @param array プライマリーパラメータ情報
     * @return string デコードしたプライマリーパラメータ情報
     */
    protected static function decodeId($id)
    {
        $error_level = error_reporting(0);
        $id = unserialize(base64_decode($id));
        error_reporting($error_level);

        if (!$id || !is_array($id) || (count($id) == 0)) {
            throw new SyL_InvalidParameterException('primary parameter restore error');
        }
        return $id;
    }
}
