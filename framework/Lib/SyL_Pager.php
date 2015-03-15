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
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ページングクラス
 *
 * @package    SyL.Lib
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_Pager
{
    /**
     * 総件数
     * 
     * @var int
     */
    private $sum = 0;
    /**
     * 1ページの件数
     * 
     * @var int
     */
    private $page_count = 1;
    /**
     * 現在ページ数
     * 
     * @var int
     */
    private $current_page = 1;
    /**
     * 遷移先ベースURL
     * 
     * @var string
     */
    private $url = '';
    /**
     * 遷移先ベースURL、パラメータ名（ページ数パラメータ）
     * 
     * @var string
     */
    private $parameter_name = 'page';
    /**
     * 遷移先ベースURL、パラメータ（ページ数以外）
     * 
     * @var array
     */
    private $parameters = array();

    /**
     * コンストラクタ
     *
     * @param int 1ページの件数
     * @throws SyL_InvalidParameterException 1ページの件数が無効な場合
     */
    public function __construct($page_count)
    {
        if (!is_numeric($page_count) || ($page_count <= 0)) {
            throw new SyL_InvalidParameterException("invalid parameter ({$page_count})");
        }

        $this->page_count = (int)$page_count;
        $this->url = $_SERVER['PHP_SELF'];
    }

    /**
     * 1ページの件数を取得する
     *
     * @return int 1ページの件数
     */
    public function getPageCount()
    {
        return $this->page_count;
    }

    /**
     * 総件数をセットする
     *
     * @param int 総件数
     * @throws SyL_InvalidParameterException 総件数が無効な場合
     */
    public function setSum($sum)
    {
        if (!is_numeric($sum) || ($sum < 0)) {
            throw new SyL_InvalidParameterException("invalid parameter ({$sum})");
        }

        $this->sum = (int)$sum;

        // 総ページ数以上のページが指定されたら1ページ目へ
        if (($this->sum <= 0) || ($this->current_page > $this->getTotalPage())) {
            $this->current_page = 1;
        }
    }

    /**
     * 総件数を取得する
     *
     * @return int 総件数
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * 現在のページ数をセット
     *
     * @param int 現在のページ数
     * @throws SyL_InvalidParameterException 現在のページ数が無効な場合
     */
    public function setCurrentPage($current_page)
    {
        if (!is_numeric($current_page) || ($current_page < 1)) {
            throw new SyL_InvalidParameterException("invalid parameter ({$current_page})");
        }

        $this->current_page = (int)$current_page;
    }

    /**
     * 現在のページ数を取得する
     *
     * @return int 現在ページ数
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * 次のページ数を取得する
     *
     * @return int 次ページ数
     * @throws SyL_OutOfRangeException 最大ページ数を超えた場合
     */
    public function getNextPage()
    {
        if ($this->current_page >= $this->getTotalPage()) {
            throw new SyL_OutOfRangeException('current page is max page');
        } else {
            return $this->current_page + 1;
        }
    }

    /**
     * 前のページ数を取得する
     *
     * @return int 前ページ数
     * @throws SyL_OutOfRangeException 最小ページ数を超えた場合
     */
    public function getPrevPage()
    {
        if ($this->isFirstPage()) {
            throw new SyL_OutOfRangeException('current page is 1 page');
        } else {
            return $this->current_page - 1;
        }
    }

    /**
     * ページ総数を取得する
     *
     * 総件数、または 1 ページ件数が 0 以下なら 0 が返却される
     *
     * @return int ページ総数
     */
    public function getTotalPage()
    {
        return (($this->sum <= 0) || ($this->page_count <= 0)) ? 1 : (int)ceil($this->sum / $this->page_count);
    }

    /**
     * 最初のページ判定する
     *
     * @param bool true: 最初のページ、false: 最初以外のページ
     */
    public function isFirstPage()
    {
        return ($this->current_page <= 1);
    }

    /**
     * 最終ページ判定
     *
     * 総件数、1ページ件数がセットされていないと、false となる
     *
     * @param bool true: 最終ページ、false: 最終以外のページ
     */
    public function isLastPage()
    {
        return ($this->current_page == $this->getTotalPage());
    }

    /**
     * データ取得対象の最初のレコードを取得する
     *
     * @return int データ取得対象の最初のレコード
     * @throws SyL_OutOfRangeException 最大ページ数を超えている場合
     */
    public function getStartRecord()
    {
        if ($this->current_page > $this->getTotalPage()) {
            throw new SyL_OutOfRangeException('current page is max page');
        }
        return ($this->getPageCount() * ($this->current_page - 1)) + 1;
    }

    /**
     * データ取得対象の最後のレコードを取得する
     *
     * @return int データ取得対象の最後のレコード
     * @throws SyL_OutOfRangeException 最大ページ数を超えている場合
     */
    public function getEndRecord()
    {
        $total_page = $this->getTotalPage();
        if ($this->current_page > $total_page) {
            throw new SyL_OutOfRangeException('current page is max page');
        }
        if ($this->current_page == $total_page) {
            return $this->getSum();
        } else {
            return $this->getPageCount() * $this->current_page;
        }
    }

    /**
     * 遷移先ベースURLをセットする
     *
     * @param string 遷移先ベースURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 遷移先ページパラメータ名をセットする
     *
     * @param string 遷移先ページパラメータ名
     */
    public function setPageParameterName($parameter_name)
    {
        $this->parameter_name = $parameter_name;
    }

    /**
     * 遷移先パラメータをセットする
     *
     * @param string 遷移先URL
     */
    public function setParameter($name, $value)
    {
        $this->parameters[] = array($name, $value);
    }

    /**
     * クエリパラメータを取得する
     *
     * @param int ページ数
     * @return string クエリパラメータ
     */
    private function buildParameters($page)
    {
        $parameters = $this->parameter_name . '=' . $page;
        foreach ($this->parameters as $parameter) {
            $parameters .= '&' . $parameter[0] . '=' . urlencode($parameter[1]);
        }
        return $parameters;
    }

    /**
     * 次ページリンクを取得する
     *
     * @param string リンクメッセージ（リンクあり時）
     * @param string リンクメッセージ（リンク無し時）
     * @return string 次ページリンクタグ
     */
    public function createLinkNext($active_message, $noactive_message='')
    {
        $total_page = $this->getTotalPage();
        // 総レコード数0、次ページがない、または現在MAXページ目状態
        if (($this->sum <= 0) || ($total_page == 1) || ($this->current_page == $total_page)) {
            return $noactive_message ? $noactive_message : $active_message;
        // 総ページ数以上のページが指定されたら1ページ目リンクへ
        } else if ($this->current_page > $total_page) {
            return sprintf('<a href="%s?%s">%s</a>', $this->url, $this->buildParameters(2), $active_message);
        } else {
            return sprintf('<a href="%s?%s">%s</a>', $this->url, $this->buildParameters($this->current_page + 1), $active_message);
        }
    }

    /**
     * 前ページリンクを取得する
     *
     * @param string リンクメッセージ（リンクあり時）
     * @param string リンクメッセージ（リンク無し時）
     * @return string 前ページリンクタグ
     */
    public function createLinkPrev($active_message, $noactive_message='')
    {
        $total_page = $this->getTotalPage();
        // 総レコード数0、次ページがない、または現在1ページ目状態
        if (($this->sum <= 0) || ($total_page == 1) || ($this->current_page == 1)) {
            return $noactive_message ? $noactive_message : $active_message;
        // 総ページ数以上のページが指定されたら1ページ目リンクへ
        } else if ($this->current_page > $total_page) {
            return $noactive_message ? $noactive_message : $active_message;
        } else {
            return sprintf('<a href="%s?%s">%s</a>', $this->url, $this->buildParameters($this->current_page - 1), $active_message);
        }
    }

    /**
     * 表示幅のページリンクを取得する
     *
     * @param int ページリンク表示幅（0を指定すると全ページ）
     * @return array ページリンク配列
     */
    public function createLinkRange($range=9)
    {
        list($start, $end) = $this->getRange($range);

        $pages = array();
        for ($i=$start; $i<=$end; $i++) {
            if ($i == $this->current_page) {
                $pages[] = '<strong>' . $i . '</strong>';
            } else {
                $pages[] = sprintf('<a href="%s?%s">%s</a>', $this->url, $this->buildParameters($i), $i);
            }
        }

        return $pages;
    }

    /**
     * 表示幅のSelectタグのオプションを取得する
     *
     * @param int ページリンク表示幅（0を指定すると全ページ）
     * @return array 指定範囲ページSelectタグのオプション
     */
    public function createSelectRange($range=19)
    {
        list($start, $end) = $this->getRange($range);

        $options = array();
        for ($i=$start; $i<=$end; $i++) {
            $active = ($i == $this->current_page) ? 'selected' : '';
            $options[] = sprintf('<option value="%s?%s" %s>%s</option>', $this->url, $this->buildParameters($i), $active, $i);
        }

        return $options;
    }

    /**
     * 表示幅の最初と最後のページを取得する
     *
     * 表示幅とは、カレントページの前後のページ幅
     * 引数は、前後の片側の幅を指定。
     *
     * ex1) current_page = 1, range = 5
     *    result: array(1, 6)
     * ex2) current_page = 10, range = 5
     *    result: array(5, 15)
     *
     * @param int ページリンク表示幅（0を指定すると全ページ）
     * @return array 指定範囲ページの最初と最後のページ
     */
    public function getRange($range)
    {
        $start = 1;
        $end   = 1;
        $total_page = $this->getTotalPage();
        if ($range > 0) {
            $start = $this->current_page - $range;
            if ($start <= 0) {
                $start = 1;
            }
            $end = $this->current_page + $range;
            if ($end > $total_page) {
                $end = $total_page;
            }
        } else {
            $end = $total_page;
        }
        return array($start, $end);
    }
}
