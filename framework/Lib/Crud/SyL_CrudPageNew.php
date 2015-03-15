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

/** CRUD ページ情報操作インターフェイス */
require_once 'SyL_CrudPageInputInterface.php';

/**
 * CRUD 新規登録ページクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudPageNew extends SyL_CrudPageAbstract implements SyL_CrudPageInputInterface
{
    /**
     * フォームオブジェクトの配列
     *
     * @var array
     */
    private $forms = array();

    /**
     * コンストラクタ
     *
     * @param SyL_CrudConfigAbstract CRUD設定オブジェクト
     */
    protected function __construct(SyL_CrudConfigAbstract $config)
    {
        parent::__construct($config);
        $this->forms = $this->config->createInputForms();
    }

    /**
     * 入力情報を検証する
     *
     * @param int ページID
     */
    public function validate($current_page_id)
    {
        $page = $this->config->getInputPage($current_page_id);
        if (!is_array($page) || !isset($page['type'])) {
            throw new SyL_InvalidParameterException("page id not found ({$current_page_id})");
        }
        $type = $page['type'];

        $access = $this->config->getAccess();
        switch ($type) {
        case SyL_CrudConfigAbstract::FORM_TYPE_INPUT:
            // 入力画面は、個別のフォームをバリデート
            $elements = $this->forms[$current_page_id]->getElements();
            $record = $access->createRecord();
            $error_messages = array();
            foreach ($elements as $name => &$element) {
                try {
                    $record->set($name, $element->getValue());
                } catch (SyL_DbDaoValidateException $e) {
                    $error_messages = array_merge($error_messages, $e->getMessages());
                }
            }

            // バリデーションエラーになると、値がレコードにセットされないので、
            // 事前に確認する。（第1段階のバリデーション）
            if (count($error_messages) > 0) {
                throw new SyL_CrudValidateException($error_messages);
            }

            // 主キーなどを含むバリデーション
            // （第2段階のバリデーション）
            try {
                $access->validate($record);
            } catch (SyL_DbDaoValidateException $e) {
                // バリデート対象要素のエラーのみ抽出
                $messages = array_intersect_key($e->getMessages(), $elements);
                if (count($messages) > 0) {
                    throw new SyL_CrudValidateException($messages);
                }
            }
            break;

        case SyL_CrudConfigAbstract::FORM_TYPE_CONFIRM:
            // 確認画面は、すべてのフォームをバリデート
            $record = $access->createRecord();
            foreach ($this->forms as $page_id => &$form) {
                $page = $this->config->getInputPage($page_id);
                if ($page['type'] == SyL_CrudConfigAbstract::FORM_TYPE_INPUT) {
                    $elements = $form->getElements();
                    foreach ($elements as $name => &$element) {
                        $record->set($name, $element->getValue());
                    }
                }
            }

            try {
                $access->validate($record);
            } catch (SyL_DbDaoValidateException $e) {
                throw new SyL_CrudValidateException($e->getMessages());
            }

            break;
        }
    }

    /**
     * 入力情報を反映する
     */
    public function execute()
    {
        $access = $this->config->getAccess();
        foreach ($this->forms as $page_id => &$form) {
            $page = $this->config->getInputPage($page_id);
            if ($page['type'] == SyL_CrudConfigAbstract::FORM_TYPE_CONFIRM) {
                // 確認画面の場合、全ての入力項目がある
                $elements = $form->getElements();
                // 必要なカラムのみ登録する update モード = true
                $record = $access->createRecord(true);
                foreach ($elements as $name => &$element) {
                    $config = $this->config->getElement($name);
                    if (!$config->isDisplay() || $config->isReadOnly()) {
                        continue;
                    }
                    $record->set($name, $element->getValue());
                }
                $access->insert($record);
                break;
            }
        }
    }

    /**
     * 表示するフォーム情報を取得する
     *
     * @return array フォーム情報
     */
    public function getFormViews()
    {
        $form_views = array();
        foreach ($this->forms as $page_id => &$form) {
            $next_id = '';
            $prev_id = '';
            foreach ($this->config->getInputForwards() as $values) {
                if ($values['from'] == $page_id) {
                    if ($values['to'] > $page_id) {
                        $next_id = $values['to'];
                    } else {
                        $prev_id = $values['to'];
                    }
                }
            }

            // ページ遷移情報
            $form->addElement(SyL_FormElementAbstract::createInstance('hidden', '__page_current', null, $page_id));
            $form->addElement(SyL_FormElementAbstract::createInstance('hidden', '__page_next', null, $next_id));
            $form->addElement(SyL_FormElementAbstract::createInstance('hidden', '__page_prev', null, $prev_id));

            $form_views[$page_id] = $form->getView();
        }

        return $form_views;
    }
}

