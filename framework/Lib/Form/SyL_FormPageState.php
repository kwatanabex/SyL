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

/**
 * フォームページ遷移定義クラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormPageState
{
    /**
     * サブミットボタン表示名
     *
     * @var string
     */
    private $submit_display_name = null;
    /**
     * カレントページID
     *
     * @var int
     */
    private $current_id = null;
    /**
     * カレントページタイプ
     *
     * @var string
     */
    private $current_type = '';
    /**
     * 次のページID
     *
     * @var int
     */
    private $next_id = null;
    /**
     * 次のページタイプ
     *
     * @var string
     */
    private $next_type = '';

    /**
     * コンストラクタ
     *
     * @param string サブミットボタン表示名
     * @param int 遷移元ページID
     * @param string 遷移元ページタイプ
     * @param int 遷移先ページID
     * @param string 遷移先ページタイプ
     */
    public function __construct($submit_display_name, $current_id, $current_type, $next_id, $next_type)
    {
        $this->submit_display_name = $submit_display_name;
        $this->current_id = $current_id ? (int)$current_id : 0;
        $this->current_type = $current_type;
        $this->next_id = (int)$next_id;
        $this->next_type = $next_type;
    }

    /**
     * サブミットボタン表示名を取得する
     *
     * @return string サブミットボタン表示名
     */
    public function getSubmitDisplayName()
    {
        return $this->submit_display_name;
    }

    /**
     * カレントページIDを取得する
     *
     * @return int カレントページID
     */
    public function getCurrentId()
    {
        return $this->current_id;
    }

    /**
     * カレントページタイプを取得する
     *
     * @return string カレントページタイプ
     */
    public function getCurrentType()
    {
        return $this->current_type;
    }

    /**
     * 次のページIDを取得する
     *
     * @return int 次のページID
     */
    public function getNextId()
    {
        return $this->next_id;
    }

    /**
     * 次のページタイプを取得する
     *
     * @return string 次のページタイプ
     */
    public function getNextType()
    {
        return $this->next_type;
    }

    /**
     * ページIDからページタイプを取得する
     *
     * @param int ページID
     * @return string ページ名
     */
    public function getPageType($page_id)
    {
        if ($page_id == $this->current_id) {
            return $this->current_type;
        } else if ($page_id == $this->next_id) {
            return $this->next_type;
        } else {
            return null;
        }
    }
}