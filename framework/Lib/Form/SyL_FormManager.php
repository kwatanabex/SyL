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
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** フォームクラス */
require_once 'SyL_Form.php';
/** フォームページ遷移定義クラス */
require_once 'SyL_FormPageState.php';
/** 汎用プロパティクラス */
require_once dirname(__FILE__) . '/../SyL_Property.php';

/**
 * フォームページ遷移管理クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormManager
{
    /**
     * サブミットボタン名の接頭辞
     *
     * @var string
     */
    private $submit_prefix = 'submit_';
    /**
     * ページ構成定義
     *
     * @var array
     */
    private $pages = array();
    /**
     * サブミット表示名
     *
     * @var string
     */
    private $submit_display_name = '';
    /**
     * ページID
     *
     * @var int
     */
    private $page_id = 0;
    /**
     * フォームオブジェクトの配列
     *
     * @var array
     */
    private $forms = array();
    /**
     * 表示ページのサブミットボタン名
     *
     * @var array
     */
    private $result_submit_names = array();
    /**
     * 完了ページでバリデーション後に実行されるコールバック関数
     *
     * @var mixed
     */
    private $complete_callback = null;

    /**
     * 追加バリデーション関数
     *
     * @var mixed
     */
    private $custom_validation_callback = null;

    /**
     * 入力ページタイプ
     *
     * @var string
     */
    const FORM_TYPE_INPUT = 'input';
    /**
     * 確認ページタイプ
     *
     * @var string
     */
    const FORM_TYPE_CONFIRM = 'confirm';
    /**
     * 完了ページタイプ
     *
     * @var string
     */
    const FORM_TYPE_COMPLETE = 'complete';

    /**
     * コンストラクタ
     *
     */
    public function __construct()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new SyL_FormInvalidPageException("environment value `REQUEST_METHOD' not found");
        }
        // 管理外のページ
        $this->pages[''] = new SyL_FormPageState('', '0', '', '1', 'input');
    }

    /**
     * フォームページ遷移定義オブジェクトを追加する
     *
     * @param SyL_FormPageState フォームページ遷移定義オブジェクト
     * @throws SyL_DuplicateException サブミットボタン表示名が重複した場合
     */
    public function addPage(SyL_FormPageState $page)
    {
        $submit_display_name = $page->getSubmitDisplayName();
        if (isset($this->pages[$submit_display_name])) {
            throw new SyL_DuplicateException("duplicate submit_display_name ({$submit_display_name})");
        }

        $this->pages[$submit_display_name] = $page;
    }

    /**
     * サブミットボタン名の接頭辞をセットする
     *
     * @param string サブミットボタン名の接頭辞
     */
    public function setSubmitNamePrefix($submit_prefix)
    {
        $this->submit_prefix = $submit_prefix;
    }

    /**
     * フォームオブジェクトをセットする
     *
     * @param SyL_Form フォームオブジェクト
     */
    public function setForm(SyL_Form $form, $page_id=1)
    {
        $this->forms[$page_id] = $form;
    }

    /**
     * ページ遷移状態を初期化する
     *
     * @param string フォーム遷移設定ファイル
     */
    public function initialize($filename=null)
    {
        if ($filename) {
            include_once 'SyL_FormManagerConfigAbstract.php';
            $manager_config = SyL_FormManagerConfigAbstract::createInstance($filename);
            foreach ($manager_config->getPageStates() as $page) {
                $this->addPage($page);
            }

            foreach ($manager_config->getFormConfigFiles() as $page_id => $filename) {
                $form_config = SyL_FormConfigAbstract::createInstance($filename);
                $form_name = $form_config->getFormName();
                $form = new SyL_Form($form_name);
                $form->createElementFromConfig($form_config, ($_SERVER['REQUEST_METHOD'] != 'POST'));
                $this->forms[$page_id] = $form;
            }
        }

        $submit_name = '';
        foreach ($this->pages as &$page) {
            $next_id = $page->getNextId();
            if ($next_id) {
                $name = $this->submit_prefix . $next_id;
                if (isset($_REQUEST[$name])) {
                    $submit_name = $_REQUEST[$name];
                    break;
                }
            }
        }

        if (!isset($this->pages[$submit_name])) {
            throw new SyL_KeyNotFoundException("page not found ({$submit_name})");
        }

        $this->submit_display_name = $submit_name;
        $this->page_id = $this->pages[$submit_name]->getNextId();
    }

    /**
     * ページIDからページ名を取得する
     *
     * @param int ページID
     * @return string ページ名
     */
    public function getPageType($page_id=null)
    {
        if (!$page_id) {
            $page_id = $this->page_id;
        }
        $form_type = null;
        foreach ($this->pages as &$page) {
            $form_type = $page->getPageType($page_id);
            if ($form_type) {
                break;
            }
        }

        return $form_type;
    }

    /**
     * 追加バリデーション関数をセットする
     *
     * @param mixed パラメータ取得関数
     * @throws SyL_InvalidParameterException パラメータが関数形式でない場合
     */
    public function registerCustomValidationCallback($callback)
    {
        if (is_callable($callback)) {
            $this->custom_validation_callback = $callback;
        } else {
            throw new SyL_InvalidParameterException("Invalid callback parameter. no function or method (" . var_export($callback, true) . ")");
        }
    }

    /**
     * フォームオブジェクトの検証を実行する
     *
     * @return true: エラーなし、false: エラーあり
     * @throws SyL_FormInvalidPageException 無効なページ遷移の場合
     */
    public function validate()
    {
        $current_id = $this->pages[$this->submit_display_name]->getCurrentId();
        $next_id    = $this->pages[$this->submit_display_name]->getNextId();

        $valid = true;
        $form_type = $this->getPageType($next_id);
        switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($current_id == 0) {
                // 初期ページ
            } else if ($form_type == self::FORM_TYPE_COMPLETE) {
                // 完了ページ
                return true;
            } else {
                // 無効なページ遷移
                throw new SyL_FormInvalidPageException('invalid page location');
            }
            break;
        case 'POST':
            // 次ページ以降
            if (($next_id - $current_id) > 0) {
                // 次に進む場合
                switch ($form_type) {
                case self::FORM_TYPE_INPUT:
                    // 入力ページ
                    if (isset($this->forms[$current_id])) {
                        if ($this->custom_validation_callback) {
                            $this->forms[$current_id]->registerCustomValidationCallback($this->custom_validation_callback);
                        }
                        $valid = $this->forms[$current_id]->validate();
                        if (!$valid) {
                            $this->page_id = $current_id;
                        }
                    }
                    break;
                case self::FORM_TYPE_CONFIRM:
                    // 確認ページ
                case self::FORM_TYPE_COMPLETE:
                    // 完了ページへのサブミット
                    $valid = true;
                    foreach ($this->forms as $form_page_id => &$form) {
                        if ($this->custom_validation_callback) {
                            $form->registerCustomValidationCallback($this->custom_validation_callback);
                        }
                        $valid = $form->validate();
                        if ($valid) {
                            $form->isReadOnly(true);
                        } else {
                            $this->page_id = $form_page_id;
                            break;
                        }
                    }
                    if ($valid && ($form_type == self::FORM_TYPE_COMPLETE)) {
                        if ($this->complete_callback) {
                            $properties = new SyL_Property();
                            foreach ($this->forms as &$form) {
                                foreach ($form->getElements() as $element) {
                                    if ($element instanceof SyL_FormElementGroup) {
                                        foreach ($element->getValue() as $element1) {
                                            $properties->set($element1->getName(), $element1->getValue());
                                        }
                                    } else {
                                        $properties->set($element->getName(), $element->getValue());
                                    }
                                }
                            }
                            call_user_func($this->complete_callback, $properties);
                        }
                        $url  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
                        $url .= (strpos($url, '?') === false) ? '?' : '&';
                        $url .= sprintf('%s=%s', $this->submit_prefix . $next_id, urlencode($this->submit_display_name));
                        header('Location: ' . $url); 
                        exit;
                    }
                    break;
                }
            }
            break;
        default:
            // 無効なページ遷移
            throw new SyL_FormInvalidPageException('invalid request method');
        }

        return $valid;
    }

    /**
     * フォーム表示オブジェクトを取得する
     *
     * @return SyL_FormView フォーム表示オブジェクト
     */
    public function getView()
    {
        $view = new SyL_FormView();
        switch ($this->getPageType()) {
        case self::FORM_TYPE_INPUT:
            // 入力ページ
            foreach ($this->forms as $form_page_id => &$form) {
                if ($this->page_id != $form_page_id) {
                    $form->isHidden(true);
                }
                $view->addForm($form);
            }
            break;
        case self::FORM_TYPE_CONFIRM:
            // 確認ページ
            foreach ($this->forms as $form_page_id => &$form) {
                $view->addForm($form);
            }
            break;
        default:
            return $view;
        }

        foreach ($this->pages as $display_name => &$page) {
            if ($this->page_id == $page->getCurrentId()) {
                $name = $this->submit_prefix . $page->getNextId();
                $element = SyL_FormElementAbstract::createInstance('submit', $name, $display_name);
                $view->addButtonElement($element);
            }
        }
        return $view;
    }

    /**
     * 完了ページでバリデーション後に実行されるコールバック関数をセットする
     *
     * @param mixed コールバック関数
     */
    public function setCompleteCallback($callback)
    {
        if (is_callable($callback)) {
            $this->complete_callback = $callback;
        } else {
            throw new SyL_InvalidParameterException("Invalid callback parameter. no function or method (" . var_export($callback, true) . ")");
        }
    }

    /**
     * 表示ページのサブミットボタン名を取得する
     *
     * @return array 表示ページのサブミットボタン名の配列
     */
    public function getSubmitNames()
    {
        return $this->result_submit_names;
    }
}
