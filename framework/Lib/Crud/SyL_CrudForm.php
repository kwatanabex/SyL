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

/** フォームクラス */
require_once dirname(__FILE__) . '/../Form/SyL_Form.php';

/**
 * CRUD フォームクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2011 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudForm extends SyL_Form
{
    /**
     * CRUDタイプ
     * 
     * @var string
     */
    private $crud_type = null;
    /**
     * デフォルト値有効フラグ
     * 
     * @var bool
     */
    private $enable_default = false;

    /**
     * コンストラクタ
     *
     * @param string CRUDタイプ
     * @param string フォーム名
     * @param string フォームサブミット時のアクション先
     */
    public function __construct($crud_type, $name='crud_form', $action='')
    {
        parent::__construct($name, $action);
        $this->crud_type = $crud_type;
        $this->enable_default = !(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'));
    }

    /**
     * デフォルト値有効フラグをセットする
     *
     * @param bool デフォルト値有効フラグ
     */
    public function setDefaultEnable($enable_default)
    {
        $this->enable_default = (bool)$enable_default;
    }

    /**
     * CRUD設定からフォームを構築する
     *
     * @param array CRUD設定配列
     * @param array バリデーション配列
     */
    public function buildConfig(array $configs, array $validations)
    {
        $elements = array();
        // 要素登録ループ
        foreach ($configs as $name => &$config) {
            if ($this->crud_type != SyL_CrudConfigAbstract::CRUD_TYPE_IMP) {
                if (!$config->isDisplay()) {
                    continue;
                }
            }
            // バリデーション
            $validation = isset($validations[$name]) ? $validations[$name] : null;
            // 要素作成
            $element = $this->createElementFromCrudConfig($name, $config, $validation);
            // 並び順変更のため一時保存
            $elements[$config->getSort()] = $element;
        }

        ksort($elements, SORT_NUMERIC);
        foreach (array_keys($elements) as $name) {
            $this->addElement($elements[$name]);
        }
    }

    /**
     * 要素を作成する
     *
     * @param string 要素名
     * @param array 属性配列
     */
    private function createElementFromCrudConfig($name, SyL_CrudConfigElement $config, SyL_ValidationAbstract $validation=null)
    {
        // 表示名設定
        $display_name = $config->getName();
        if (!$display_name) {
            $display_name = $name;
        }

        // 要素作成
        $element = SyL_FormElementAbstract::createInstance($config->getType(), $name, $display_name, null, $config->getAttributes(), $validation);
        // 初期値設定
        if ($this->enable_default) {
            $element->setValue($config->getDefaultValue());
        } else {
            $element->setValue(self::getParameter($name));
        }
        // オプション設定
        foreach ($config->getOptions() as $k => $v) {
            $element->setOption($k, $v);
        }
        switch ($this->crud_type) {
        case SyL_CrudConfigAbstract::CRUD_TYPE_NEW:
        case SyL_CrudConfigAbstract::CRUD_TYPE_EDT:
        case SyL_CrudConfigAbstract::CRUD_TYPE_IMP:
            // 読み取り専用
            $element->isReadOnly($config->isReadOnly());
            // バリデーション設定
            if ($validation) {
                $element->setValidation($validation);
            }
            break;
        case SyL_CrudConfigAbstract::CRUD_TYPE_VEW:
            // 読み取り専用
            $element->isReadOnly(true);
            break;
        }
        // 表示要素のセパレータ
        if (method_exists($element, 'setSeparator')) {
            if ($this->crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_SCH) {
                // 検索時は、半角スペース固定
                $element->setSeparator(' ');
            } else {
                $separator = $config->getSeparator();
                if ($separator !== null) {
                    $element->setSeparator($separator);
                }
            }
        }

        // 画像フォーム
        if ($element instanceof SyL_FormElementImage) {
            switch ($this->crud_type) {
            case SyL_CrudConfigAbstract::CRUD_TYPE_EDT:
            case SyL_CrudConfigAbstract::CRUD_TYPE_VEW:
                // 画像を表示判定
                $element->setImageDisplay($config->isImageDisplay());
            case SyL_CrudConfigAbstract::CRUD_TYPE_NEW:
                // ファイルマネージャリンク表示判定
                $element->setFileArea($config->getFileArea());
                break;
            }
        }

        return $element;
    }
}
