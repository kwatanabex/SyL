<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * アクションクラス
 *
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
abstract class SyL_ActionAbstract implements SyL_ContainerComponentInterface
{
    /**
     * バリデーション起動判定
     * 
     * @var bool
     */
    protected $validation_trigger = false;
    /**
     * バリデーション設定
     * 
     * @var array or string
     */
    protected $validation_config = null;
    /**
     * アクションフォーム名
     * 
     * @var string
     */
    protected $action_form_class = '';
    /**
     * エラーメッセージ
     * 
     * @var array
     */
    protected $error_messages = array();

    /**
     * アクションメソッド
     * 
     * @var string
     */
    private $action_method = 'execute';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * アクションメソッドをセットする
     *
     * @param string アクションメソッド
     */
    protected function setActionMethod($action_method)
    {
        if (!method_exists($this, $action_method)) {
            $name = get_class($this);
            throw new SyL_ResponseNotFoundException("action method not implemented this class ({$name}.{$action_method})");
        }
        $this->action_method = $action_method;
    }

    /**
     * アクションプロセスを実行する
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    public final function process(SyL_ContextAbstract $context, SyL_Data $data)
    {
        SyL_Logger::trace('action.preExecute start');
        $this->preExecute($context, $data);
        SyL_Logger::trace('action.validate start');
        $this->validate($context, $data);
        SyL_Logger::trace('action.' . $this->action_method . ' start');
        $this->{$this->action_method}($context, $data);
        SyL_Logger::trace('action.postExecute start');
        $this->postExecute($context, $data);
    }

    /**
     * 検証実行メソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    public function validate(SyL_ContextAbstract $context, SyL_Data $data)
    {
/*
        if ($this->validation_trigger) {
            // アクションフォームからバリデーション
            if ($this->action_form_class) {
                $form = $this->getActionForm($data);
                if (!$form->validate()) {
                    $this->validateError($form->getErrorMessageAll());
                }
            } else {
                // 設定値からバリデーション
                if (is_array($this->validation_config)) {
                    // バリデーションマネージャインクルード
                    include_once SYL_FRAMEWORK_DIR . '/lib/SyL_ValidationManager.php';
                    // バリデーションマネージャオブジェクト作成
                    $manager =& new SyL_ValidationManager();
                    foreach ($this->validation_config as $name => $values) {
                        if (isset($values['validate']) && is_array($values['validate'])) {
                            $validators =& SyL_Validators::create();
                            foreach ($values['validate'] as $validation_name => $validations) {
                                $options = isset($validations['parameters']) ? $validations['parameters'] : array();
                                $validators->add(SyL_Validator::create($validation_name, $validations['message'], $options));
                            }
                            $display_name = isset($values['name']) ? $values['name'] : $name;
                            $manager->add($validators, $data->get($name), $name, $display_name);
                        }
                    }
                    // バリデーション判定
                    if (!$manager->execute()) {
                        $this->validateError($manager->getErrorMessageAll());
                    }

                // 設定ファイルからバリデーション
                } else if (is_string($this->validation_config)) {
                    $file = SYL_APP_DIR . '/config/' . $this->validation_config;
                    if (!is_file($file)) {
                        $file1 = SYL_PROJECT_DIR . '/config/' . $this->validation_config;
                        if (!is_file($file1)) {
                            trigger_error("[SyL error] Validation config file not found ({$file}, {$file1})", E_USER_ERROR);
                        }
                        $file = $file1;
                    }
                    // バリデーションマネージャ
                    include_once SYL_FRAMEWORK_DIR . '/lib/SyL_ValidationManager.php';
                    $manager =& new SyL_ValidationManager();
                    // バリデーション設定取得
                    $config =& SyL_ValidationManager::getConfig($file);
                    // バリデーション判定
                    if (!$manager->executeConfig($config, $data->gets())) {
                        $this->validateError($manager->getErrorMessageAll());
                    }
                }
            }
        }
*/
    }

    /**
     * アクションメソッド実行前に実行されるメソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    public abstract function preExecute(SyL_ContextAbstract $context, SyL_Data $data);

    /**
     * アクション実行メソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    public abstract function execute(SyL_ContextAbstract $context, SyL_Data $data);

    /**
     * アクションメソッド実行後に実行されるメソッド
     * 
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    public abstract function postExecute(SyL_ContextAbstract $context, SyL_Data $data);


    // -----------------------------------------------------
    // バリデーション関連メソッド
    // -----------------------------------------------------

    /**
     * 検証エラー時に実行されるメソッド
     *
     * @param array エラーメッセージ配列
     */
    protected function validateError($error_messages)
    {
        $this->error_messages = $error_messages;
    }

    /**
     * エラー判定
     *
     * @return bool true: 正常、false: エラー
     */
    public function isValid()
    {
        return (count($this->error_messages) == 0);
    }


    // -----------------------------------------------------
    // アクションフォーム関連共通メソッド
    // -----------------------------------------------------

    /**
     * アクションフォームオブジェクトを取得する
     *
     * @param object データオブジェクト
     * @param bool 初期表示フラグ
     * @param string フォームクラス名
     * @return object アクションフォームオブジェクト
     */
/*
    protected function getActionForm(&$data, $first=false, $action_form_class='')
    {
        static $form = array();
        if (!$action_form_class) {
            $action_form_class = $this->action_form_class;
        }
        if (!isset($form[$action_form_class])) {
            // アクションフォーム
            include_once SYL_FRAMEWORK_DIR . '/core/SyL_ActionForm.php';
            $form[$action_form_class] =& SyL_ActionForm::factory($action_form_class);
            // データ取得オブジェクトセット
            $form[$action_form_class]->registerInput($data);
            // フォーム構築
            $form[$action_form_class]->build($first);
        }
        return $form[$action_form_class];
    }
*/
}
