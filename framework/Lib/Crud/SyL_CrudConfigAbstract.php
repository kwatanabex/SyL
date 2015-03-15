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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** CRUD 例外クラス */
require_once 'SyL_CrudException.php';
/** Crud ページクラス */
require_once 'SyL_CrudPageAbstract.php';
/** CRUD フォームクラス */
require_once 'SyL_CrudForm.php';
/** CRUD 要素設定クラス */
require_once 'SyL_CrudConfigElement.php';

/**
 * CRUD 設定クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_CrudConfigAbstract
{
    /**
     * CRUDタイプ（一覧）
     *
     * @var string
     */
    const CRUD_TYPE_LST = 'lst';
    /**
     * CRUDタイプ（検索）
     *
     * @var string
     */
    const CRUD_TYPE_SCH = 'sch';
    /**
     * CRUDタイプ（エクスポート）
     *
     * @var string
     */
    const CRUD_TYPE_EXP = 'exp';
    /**
     * CRUDタイプ（インポート）
     *
     * @var string
     */
    const CRUD_TYPE_IMP = 'imp';
    /**
     * CRUDタイプ（新規入力）
     *
     * @var string
     */
    const CRUD_TYPE_NEW = 'new';
    /**
     * CRUDタイプ（詳細）
     *
     * @var string
     */
    const CRUD_TYPE_VEW = 'vew';
    /**
     * CRUDタイプ（編集）
     *
     * @var string
     */
    const CRUD_TYPE_EDT = 'edt';
    /**
     * CRUDタイプ（削除）
     *
     * @var string
     */
    const CRUD_TYPE_DEL = 'del';
    /**
     * CRUDタイプ（RSS）
     *
     * @var string
     */
    const CRUD_TYPE_RSS = 'rss';
    /**
     * CRUDタイプ（AtomPub）
     *
     * @var string
     */
    const CRUD_TYPE_ATM = 'atm';

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
     * CRUD構成名
     *
     * @var string
     */
    protected $name = '';
    /**
     * CRUD構成の概要説明
     *
     * @var string
     */
    protected $description = '';
    /**
     * CRUD機能の使用可否
     *
     * @var string
     */
    protected $enable = array(
/*
      'lst' => true,
      'new' => true,
      'edt' => true,
      'del' => true,
      'vew' => true,
      'sch' => true,
      'rss' => true,
      'atm' => false,
*/
    );
    /**
     * CRUD一覧の設定
     *
     * @var string
     */
    protected $list_config = array(
/*
  'default_sort' => array(),
  'select_row_count' => array(10, 20, 50, 100),
  'default_row_count' => 20,
  'link_range'   => 9,
  'item_max_length' => 30,
*/
    );

    /**
     * テーブル関連リンク
     *
     * @var array
     */
    protected $related_link = array(
/*
  'tdnet_info' => array('class' => 'CrudConfigTdnet_info', 'row_count' => 5)
*/
    );
    /**
     * CRUD入力の設定
     *
     * @var array
     */
    protected $input_config = array();
    /**
     * RSS構成プロパティ
     *
     * @var array
     */
    protected $rss_config = array(
/*
  'row_count' => 20,
  'default_sort' => array(
    'LAST_UPDATE' => false 
  ),
  'item_format'  => array(
    'title' => 'STOCK_NAME',
    'description' => 'DESCRIPTION',
    'pubDate'  => 'LAST_UPDATE'
  )
*/
    );
    /**
     * AtomPub構成プロパティ
     *
     * @var array
     */
    protected $atom_config = array(
/*
  'row_count' => 20,
  'default_sort' => array(
    'LAST_UPDATE' => false 
  ),
  'item_format'  => array(
    'title' => 'STOCK_NAME',
    'link' => '#',
    'summary' => 'DESCRIPTION',
    'author' => null,
    'published' => 'LAST_UPDATE',
    'updated' => 'LAST_UPDATE'
  )
*/
    );
    /**
     * フォーム構成要素
     *
     * @var array
     */
    protected $element_config = array();

    /**
     * CRUD DBアクセスクラス
     *
     * @var SyL_CrudDbDaoAccessAbstract
     */
    private $access = null;

    /**
     * CRUDタイプ
     *
     * @var string
     */
    private $crud_type = '';
    /**
     * CRUD要素の配列
     *
     * @var array
     */
    private $elements = array();
    /**
     * CRUD要素のバリデーション
     *
     * @var array
     */
    private $validations = array();

    /**
     * CRUD画面のベースURL
     *
     * @var string
     */
    protected $base_url = '';
    /**
     * 一覧のファイル名
     *
     * @var string
     */
    protected $file_name_lst = 'index.html';
    /**
     * 新規のファイル名
     *
     * @var string
     */
    protected $file_name_new = 'new.html';
    /**
     * 詳細のファイル名
     *
     * @var string
     */
    protected $file_name_vew = 'vew.html';
    /**
     * 編集のファイル名
     *
     * @var string
     */
    protected $file_name_edt = 'edt.html';
    /**
     * RSSファイル名
     *
     * @var string
     */
    protected $file_name_rss = 'rss.html';
    /**
     * AtomPubのサービス文書ファイル名
     *
     * @var string
     */
    protected $file_name_atm_service = 'atom/index.html';
    /**
     * AtomPubのフィードファイル名
     *
     * @var string
     */
    protected $file_name_atm_feed = 'atom/feed.html';
    /**
     * AtomPubのエントリファイル名
     *
     * @var string
     */
    protected $file_name_atm_entry = 'atom/entry.html';

    /**
     * コンストラクタ
     */
    public function __construct($crud_type)
    {
        $this->crud_type = $crud_type;
        $this->access = $this->createAccess();
    }

    /**
     * CRUDアクセスオブジェクトを作成する
     *
     * @return SyL_CrudDbDaoAccessAbstract CRUDアクセスオブジェクト
     */
    protected abstract function createAccess();

    /**
     * CRUDアクセスオブジェクトを取得する
     *
     * @return SyL_CrudDbDaoAccessAbstract CRUDアクセスオブジェクト
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * 現在のCRUDタイプを取得する
     *
     * @return string CRUDタイプ
     */
    public function getCrudType()
    {
        return $this->crud_type;
    }

    /**
     * ベースURLをセットする
     *
     * @param string ベースURL
     */
    public function setBaseUrl($base_url)
    {
        $this->base_url = $base_url;
    }

    /**
     * 一覧のURLを取得する
     *
     * @return string 一覧のURL
     */
    public function getUrlLst()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_LST, $this->file_name_lst);
    }

    /**
     * 新規のURLを取得する
     *
     * @return string 新規のURL
     */
    public function getUrlNew()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_NEW, $this->file_name_new);
    }

    /**
     * 詳細のURLを取得する
     *
     * @return string 詳細のURL
     */
    public function getUrlVew()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_VEW, $this->file_name_vew);
    }

    /**
     * 編集のURLを取得する
     *
     * @return string 編集のURL
     */
    public function getUrlEdt()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_EDT, $this->file_name_edt);
    }

    /**
     * RSSのURLを取得する
     *
     * @return string RSSのURL
     */
    public function getUrlRss()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_RSS, $this->file_name_rss);
    }

    /**
     * AtomPubのサービス文書のURLを取得する
     *
     * @return string AtomPubのサービス文書のURL
     */
    public function getUrlAtmService()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_ATM, $this->file_name_atm_service);
    }

    /**
     * AtomPubのフィードのURLを取得する
     *
     * @return string AtomPubのフィードのURL
     */
    public function getUrlAtmFeed()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_ATM, $this->file_name_atm_feed);
    }

    /**
     * AtomPubのエントリのURLを取得する
     *
     * @return string AtomPubのエントリのURL
     */
    public function getUrlAtmEntry()
    {
        return $this->getUrl(SyL_CrudConfigAbstract::CRUD_TYPE_ATM, $this->file_name_atm_entry);
    }

    /**
     * URLを取得する
     *
     * @param string CRUDタイプ
     * @param string ファイル名
     * @return string URL
     */
    private function getUrl($crud_type, $filename)
    {
        if (!$this->base_url) {
            throw new SyL_CrudInvalidConfigException('empty base_url');
        }
        if (!$this->enableCrud($crud_type)) {
            return null;
        }
        return $this->base_url . $filename;
    }

    /**
     * 要素設定オブジェクトの構築を行う
     *
     * @param SyL_DbAbstract DBクラス
     */
    public function buildElements()
    {
        $format_types = $this->access->getFormatTypes();
        foreach ($this->element_config as $name => $config) {
            if (isset($config['data_source']) && (count($config['data_source']) > 0)) {
                // データソース取得処理
                if (!isset($config['options']) || !is_array($config['options'])) {
                    $config['options'] = array();
                }
                $config['options'] = array_merge($config['options'], $this->access->getElementOptionList($config['data_source']));
            }
            $this->elements[$name] = new SyL_CrudConfigElement($this->crud_type, $config, $format_types[$name]);

            // 要素バリデーション追加
            if ($this->access->isMainAlias($config['alias'])) {
                foreach ($this->elements[$name]->getValidation() as $v_name => $v_values) {
                    $v_parameters = isset($v_values['parameters']) ? $v_values['parameters'] : array();
                    $validator = SyL_ValidationAbstract::createValidator($v_name, $v_values['message'], $v_parameters);
                    $this->access->addValidation($name, $validator);
                }
            }
        }

        $this->validations = $this->access->getValidations();
    }

    /**
     * 入力フロー以外のフォームオブジェクトを作成する
     *
     * @param string CRUDタイプ
     * @return SyL_CrudForm フォームオブジェクト
     */
    public function createForm($crud_type=null)
    {
        if (!$crud_type) {
            $crud_type = $this->crud_type;
        }

        $form = new SyL_CrudForm($crud_type);
        $form->setDefaultEnable(false);

        $configs = array();
        foreach ($this->getElements() as $name => $config) {
            // メインテーブル別名チェック
            if (!$this->access->isMainAlias($config->getAlias())) {
                continue;
            }
            $tmp_config = clone $config;
            $tmp_config->setCrudType($crud_type);
            $configs[$name] = $tmp_config;
        }
        $form->buildConfig($configs, $this->validations);

        return $form;
    }

    /**
     * 入力フローで使用するフォームオブジェクトを作成する
     *
     * @return array 入力フローで使用するフォームオブジェクト
     */
    public function createInputForms()
    {
        $forms = array();

        // 入力／確認／完了のフォームオブジェクト作成
        $confirm_page_id = '';
        $complete_page_id = '';
        foreach ($this->getInputPages() as $page_id => $values) {
            $forms[$page_id] = new SyL_CrudForm($this->crud_type);
            switch ($values['type']) {
            case SyL_CrudConfigAbstract::FORM_TYPE_CONFIRM:
                $confirm_page_id = $page_id;
                break;
            case SyL_CrudConfigAbstract::FORM_TYPE_COMPLETE:
                $complete_page_id = $page_id;
                break;
            }
        }

        // フォーム毎の要素を作成
        $page_element_config = array();
        $element_config = array();
        foreach ($this->getElements() as $name => $config) {
            // メインテーブル別名チェック
            if (!$this->access->isMainAlias($config->getAlias())) {
                continue;
            }
            $input_page = $config->getInputPage();
            if (array_key_exists($input_page, $forms)) {
                $page_element_config[$input_page][$name] = $config;
                $element_config[$name] = $config;
            }
        }

        // 入力フォームの構築
        foreach ($page_element_config as $page_id => $configs) {
            $forms[$page_id]->buildConfig($configs, $this->validations);
        }

        // 確認フォームの構築
        if (isset($forms[$confirm_page_id])) {
            $forms[$confirm_page_id]->buildConfig($element_config, $this->validations);
            $forms[$confirm_page_id]->isReadOnly(true);
        }
        // 完了フォームの構築
        if (isset($forms[$complete_page_id])) {
            $forms[$complete_page_id]->buildConfig($element_config, $this->validations);
            $forms[$complete_page_id]->isReadOnly(true);
        }

        return $forms;
    }

    /**
     * テーブル関連リンクを取得する
     *
     * @return
     */
    public function getRelatedLinks()
    {
        return $this->related_link;
    }

    /**
     * 構成メタ名を取得する
     *
     * @return string 構成メタ名
     */
    public function getMetaName()
    {
        if (preg_match('/^CrudConfig(.+)$/', get_class($this), $matches)) {
            return $matches[1];
        } else {
            throw new SyL_InvalidClassException('invalid meta name');
        }
    }

    /**
     * CRUD構成名を取得する
     *
     * @return string CRUD構成名
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * CRUD構成の説明を取得する
     *
     * @return string CRUD構成の説明
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 機能が有効か判定する
     *
     * @param string CRUDタイプ
     * @return bool 機能が有効か
     */
    public function enableCrud($crud_type=null)
    {
        if (!$crud_type) {
            $crud_type = $this->crud_type;
        }
        return isset($this->enable[$crud_type]) ? (bool)$this->enable[$crud_type] : false;
    }

    /**
     * デフォルトソートを取得する
     *
     * @return array デフォルトソート
     */
    public function getDefaultSort()
    {
        return $this->list_config['default_sort'];
    }

    /**
     * 件数選択肢を取得する
     *
     * @return array 件数選択肢
     */
    public function getSelectRowCount()
    {
        return $this->list_config['select_row_count'];
    }

    /**
     * デフォルト表示レコード数を取得する
     *
     * @return int デフォルト表示レコード数
     */
    public function getDefaultRowCount()
    {
        return $this->list_config['default_row_count'];
    }

    /**
     * ページングのリンク件数
     * 奇数値で設定
     * 例） 9 -> 前4つ + カレント1つ + 次4つ
     *
     * @return int ページングのリンク件数
     */
    public function getLinkRange()
    {
        return $this->list_config['link_range'];
    }

    /**
     * 一覧表示項目の最大文字数
     *
     * @return int 一覧表示項目の最大文字数
     */
    public function getItemMaxLength()
    {
        return isset($this->list_config['item_max_length']) ? $this->list_config['item_max_length'] : 0;
    }

    /**
     * 一覧画面のレコード背景カラー
     *
     * @return array 一覧画面のレコード背景カラー
     */
    public function getBackgroundRowColor()
    {
        return isset($this->list_config['background_row_color']) ? $this->list_config['background_row_color'] : array();
    }

    /**
     * 入力ページ情報を取得する
     *
     * @return array 入力ページ情報
     */
    public function getInputPage($page_id)
    {
        return isset($this->input_config['pages'][$page_id]) ? $this->input_config['pages'][$page_id] : null;
    }

    /**
     * すべての入力ページ情報を取得する
     *
     * @return array すべての入力ページ情報
     */
    public function getInputPages()
    {
        $pages = $this->input_config['pages'];
        $header_key = 'header_' . $this->crud_type;
        $footer_key = 'footer_' . $this->crud_type;
        foreach ($pages as &$content) {
            if (isset($content[$header_key])) {
                $content['header'] = $content[$header_key];
                unset($content[$header_key]);
            } else if (!isset($content['header'])) {
                $content['header'] = null;
            }

            if (isset($content[$footer_key])) {
                $content['footer'] = $content[$footer_key];
                unset($content[$footer_key]);
            } else if (!isset($content['footer'])) {
                $content['footer'] = null;
            }
        }
        return $pages;
    }

    /**
     * 入力ページ遷移情報を取得する
     *
     * @return array 入力ページ遷移情報
     */
    public function getInputForwards()
    {
        return $this->input_config['forwards'];
    }

    /**
     * RSSの表示レコード数を取得する
     *
     * @return int RSSの表示レコード数
     */
    public function getRssRowCount()
    {
        return $this->rss_config['row_count'];
    }

    /**
     * RSSのデフォルトソートを取得する
     *
     * @return array RSSのデフォルトソート
     */
    public function getRssDefaultSort()
    {
        return $this->rss_config['default_sort'];
    }

    /**
     * RSSのitem要素のフォーマットを取得する
     *
     * @return array RSSのitem要素のフォーマット
     */
    public function getRssItemFormat()
    {
        return $this->rss_config['item_format'];
    }

    /**
     * Atom Feedの表示レコード数を取得する
     *
     * @return int Atom Feedの表示レコード数
     */
    public function getAtomRowCount()
    {
        return $this->atom_config['row_count'];
    }

    /**
     * Atom Feedのデフォルトソートを取得する
     *
     * @return array Atom Feedのデフォルトソート
     */
    public function getAtomDefaultSort()
    {
        return $this->atom_config['default_sort'];
    }

    /**
     * Atom Feedのitem要素のフォーマットを取得する
     *
     * @return array Atom Feedのitem要素のフォーマット
     */
    public function getAtomItemFormat()
    {
        return $this->atom_config['item_format'];
    }

    /**
     * フォーム構成要素を取得する
     *
     * @return SyL_CrudConfigElement フォーム構成要素
     */
    public function getElement($name)
    {
        return isset($this->elements[$name]) ? $this->elements[$name] : null;
    }

    /**
     * フォーム構成要素をすべて取得する
     *
     * @return array フォーム構成要素
     */
    public function getElements()
    {
        return $this->elements;
    }
}
