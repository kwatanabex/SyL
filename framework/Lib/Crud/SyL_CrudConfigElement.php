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

/**
 * CRUD 要素設定クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudConfigElement
{
    /**
     * CRUDタイプ
     *
     * @var string
     */
    private $crud_type = '';
    /**
     * フォーム要素設定配列
     *
     * @var array
     */
    private $config = array();
    /**
     * DAO基準の型タイプ
     *
     * @var string
     */
    private $format_type = null;

    /**
     * コンストラクタ
     *
     * @param string CRUDタイプ
     * @param array 要素設定情報
     * @param string DAO基準の型タイプ
     */
    public function __construct($crud_type, array $config, $format_type)
    {
        $this->crud_type = $crud_type;
        $this->config = $config;
        $this->format_type = $format_type;
    }

    /**
     * CRUDタイプをセットする
     *
     * @param string CRUDタイプ
     */
    public function setCrudType($crud_type)
    {
        $this->crud_type = $crud_type;
    }

    /**
     * 表示／非表示を判定する
     *
     * @return bool 表示／非表示の判定
     */
    public function isDisplay()
    {
        if ($this->config['type'] == 'password') {
            if ($this->crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_SCH) {
                // 検索時のパスワードフィールドは、非表示
                return false;
            }
        }

        $name = 'display_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return (bool)$this->config[$name];
        } else if (isset($this->config['display'])) {
            return (bool)$this->config['display'];
        } else {
            // default
            return true;
        }
    }

    /**
     * 表示のみを判定する
     *
     * @return bool 表示のみ判定
     */
    public function isReadOnly()
    {
        $name = 'read_only_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return (bool)$this->config[$name];
        } else if (isset($this->config['read_only'])) {
            return (bool)$this->config['read_only'];
        } else {
            // default
            return false;
        }
    }

    /**
     * ファイルエリア名を取得する
     *
     * @return string ファイルエリア名
     */
    public function getFileArea()
    {
        $name = 'file_area_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else if (isset($this->config['file_area'])) {
            return $this->config['file_area'];
        } else {
            // default
            return null;
        }
    }

    /**
     * 画像の表示／非表示を判定する
     *
     * @return bool 画像の表示／非表示の判定
     */
    public function isImageDisplay()
    {
        if ($this->config['type'] != 'image') {
            return false;
        }

        $name = 'image_display_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return (bool)$this->config[$name];
        } else if (isset($this->config['image_display'])) {
            return (bool)$this->config['image_display'];
        } else {
            // default
            return false;
        }
    }

    /**
     * DAO基準の型タイプを取得する
     *
     * @return string DAO基準の型タイプ
     */
    public function getFormatType()
    {
        return $this->format_type;
    }

    /**
     * 入力ページを判定する
     *
     * @return int 入力ページ
     */
    public function getInputPage()
    {
        return isset($this->config['input_page']) ? $this->config['input_page'] : '1';
    }

    /**
     * 補足を取得する
     *
     * @return string 補足
     */
    public function getNote()
    {
        $name = 'note_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else if (isset($this->config['note'])) {
            return $this->config['note'];
        } else {
            // default
            return null;
        }
    }

    /**
     * 列数を取得する
     *
     * @return int 列数
     */
    public function getCols()
    {
        $name = 'cols';
        return isset($this->config[$name]) ? $this->config[$name] : 1;
    }

    /**
     * 参照テーブル別名を取得する
     *
     * @return string 参照テーブル別名
     */
    public function getAlias()
    {
        return $this->config['alias'];
    }

    /**
     * フォーム要素のタイプを取得する
     *
     * @return string フォーム要素のタイプ
     */
    public function getType()
    {
        if ($this->crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_SCH) {
            // 検索時の入力フォーム変換
            switch ($this->config['type']) {
            // テキストエリアは、テキストフィールドに変換
            case 'textarea': return 'text';
            // ラジオボタンは、チェックボックスに変換
            case 'radio': return 'checkbox';
            }
        }
        return $this->config['type'];
    }

    /**
     * フォーム要素名を取得する
     *
     * @return string フォーム要素名
     */
    public function getName()
    {
        return isset($this->config['name']) ? $this->config['name'] : null;
    }

    /**
     * 要素の表示用セパレータ文字列を取得する
     *
     * @return string 要素の表示用セパレータ文字列
     */
    public function getSeparator()
    {
        return isset($this->config['separator']) ? $this->config['separator'] : null;
    }

    /**
     * オプションを取得する
     *
     * @return array オプション
     */
    public function getOptions()
    {
        $options = isset($this->config['options']) ? $this->config['options'] : array();
        switch ($this->getType()) {
        case 'select':
            if ($this->crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_SCH) {
                // 検索フォームの場合は、空の要素が全て削除され、先頭にブランクオプションを追加する
                foreach ($options as $name => $value) {
                    if (($value === '') || ($value === null)) {
                        unset($options[$name]);
                    }
                }
                return array_merge(array('ALL' => ''), $options);
            }
            break;
/*
        case 'checkbox':
            // チェックボックスは、1つしか選択できない（1カラム=選択値なので）
            $key = key($options);
            if ($key !== null) {
                return array($key => $options[$key]);
            }
            break;
*/
        }
        return $options;
    }

    /**
     * 属性を取得する
     *
     * @return array 属性
     */
    public function getAttributes()
    {
        return isset($this->config['attributes']) ? $this->config['attributes'] : array();
    }

    /**
     * 新規入力時のデフォルト値を取得する
     *
     * @return string 新規入力時のデフォルト値
     */
    public function getDefaultValue()
    {
        return isset($this->config['default']) ? $this->config['default'] : null;
    }

    /**
     * ソート順を取得する
     *
     * @return int ソート順
     */
    public function getSort()
    {
        $name = 'sort_' . $this->crud_type;
        if (isset($this->config[$name])) {
            return (int)$this->config[$name];
        } else if (isset($this->config['sort'])) {
            return (int)$this->config['sort'];
        } else {
            // default
            static $i = 1000;
            return $i++;
        }
    }

    /**
     * バリデーションを取得する
     *
     * @return array バリデーション
     */
    public function getValidation()
    {
        return isset($this->config['validation']) ? $this->config['validation'] : array();
    }
}
