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
 * @subpackage SyL.Lib.TableList
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * HTMLテーブルリストクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.TableList
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_TableList
{
    /**
     * ヘッダ情報
     * 
     * @var array
     */
    private $headers = array();
    /**
     * レコード情報
     * 
     * @var array
     */
    private $rows = array();
    /**
     * ページングオブジェクト
     * 
     * @var SyL_Pager
     */
    private $pager = null;
    /**
     * ページングレンジ
     * 
     * @var int
     */
    private $range = null;

    /**
     * ソート対象カラム
     * 
     * ['COLUMN_NAME'] => 'ASC', ...
     *
     * @var array
     */
    private $sorts = array();
    /**
     * 一意なカラム
     * 
     * @var array
     */
     private $primaries = array();
    /**
     * 新規有効フラグ
     * 
     * @var bool
     */
    private $enable_new = true;
    /**
     * 更新有効フラグ
     * 
     * @var bool
     */
    private $enable_update = true;
    /**
     * 削除有効フラグ
     * 
     * @var bool
     */
    private $enable_delete = true;

    /**
     * コンストラクタ
     *
     * @param array 表示テーブル情報
     * @param SyL_Pager ページングオブジェクト
     * @param int ページングレンジ
     */
    public function __construct(array $rows, SyL_Pager $pager=null, $range=null)
    {
        $this->rows  = $rows;
        $this->pager = $pager;
        $this->range = $range;
    }

    /**
     * ヘッダ情報をセットする
     * 
     * array(
     *   'name' => 'display_name',
     *   ...
     *
     * @param array ヘッダ情報
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * ヘッダ情報を取得する
     *
     * @return array ヘッダ情報
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * ページ総数を取得する
     * 
     * @return int ページ総数
     * @throws SyL_InvalidParameterException SyL_Pagerオブジェクトがセットされていない場合
     */
    public function getTotalPage()
    {
        if ($this->pager instanceof SyL_Pager) {
            return $this->pager->getTotalPage();
        }
        throw new SyL_InvalidParameterException("`SyL_Pager' object not setting");
    }

    /**
     * 現在のページ数を取得する
     * 
     * @return int 現在のページ数
     * @throws SyL_InvalidParameterException SyL_Pagerオブジェクトがセットされていない場合
     */
    public function getCurrentPage()
    {
        if ($this->pager instanceof SyL_Pager) {
            return $this->pager->getCurrentPage();
        }
        throw new SyL_InvalidParameterException("`SyL_Pager' object not setting");
    }

    /**
     * 表示幅の最初と最後のページを取得する
     * 
     * @param int ページリンク表示幅
     * @return array(最初のページ, 最後のページ)
     * @throws SyL_InvalidParameterException SyL_Pagerオブジェクトがセットされていない場合
     */
    public function getStartPage()
    {
        if ($this->pager instanceof SyL_Pager) {
            $range = $this->pager->getRange($this->range);
            return $range[0];
        }
        throw new SyL_InvalidParameterException("`SyL_Pager' object not setting");
    }

    /**
     * 表示幅の最初と最後のページを取得する
     * 
     * @param int ページリンク表示幅
     * @return array(最初のページ, 最後のページ)
     * @throws SyL_InvalidParameterException SyL_Pagerオブジェクトがセットされていない場合
     */
    public function getMaxPage()
    {
        if ($this->pager instanceof SyL_Pager) {
            $range = $this->pager->getRange($this->range);
            return $range[1];
        }
        throw new SyL_InvalidParameterException("`SyL_Pager' object not setting");
    }

    /**
     * 一意なカラムをセットする
     * 
     * @param string 一意なカラム
     */
    public function addPrimary($name)
    {
        $this->primaries[] = $name;
    }

    /**
     * 一意なカラムか判定する
     * 
     * @param string 一意なカラム
     * @return bool true: 一意なカラム、false: 一意なカラムでない
     */
    public function isPrimary($name)
    {
        return in_array($name, $this->primaries);
    }

    /**
     * リストを取得する
     * 
     * @return array リスト
     */
    public function getRows()
    {
        if (count($this->headers) > 0) {
            $rows = array();
            foreach ($this->rows as $row) {
                $tmp = array();
                foreach ($row as $name => $value) {
                    if (array_key_exists($name, $this->headers)) {
                        $tmp[$name] = $value;
                    }
                }
                $rows[] = $tmp;
            }
            return $rows;
        } else {
            return $this->rows;
        }
    }

    /**
     * レコード数を取得する
     * 
     * @return int レコード数
     */
    public function getRowCount()
    {
        return count($this->rows);
    }
}
