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
 * @subpackage SyL.Lib.StreamFilter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2009 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ストリームフィルタ抽象クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.StreamFilter
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2009 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_StreamFilterAbstract extends php_user_filter
{
    /**
     * フィルタベース名
     * 
     * @var string
     */
    protected $filter_base_name = '';
    /**
     * フィルタ実行用コールバック関数
     * 
     * @var mixed
     */
    protected $funcname = null;

    /**
     * フィルタ実行イベント
     *
     * @param resource bucketポインタ
     * @param resource bucket brigade 
     * @param int  データ長
     * @param bool ストリームが閉じようとしているとき
     * @return int PSFS_PASS_ON / PSFS_FEED_ME / PSFS_ERR_FATAL
     * @see http://www.php.net/manual/en/function.stream-filter-register.php
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = $this->apply($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    /**
     * フィルタ初期化イベント
     *
     * @return bool 成功した場合 true, 失敗した場合 false
     * @see http://www.php.net/manual/en/function.stream-filter-register.php
     */
    public function onCreate()
    {
        if (preg_match('/^' . preg_quote($this->filter_base_name) . '\.?(.*)$/', $this->filtername, $matches)) {
            $this->params = array();
            if (isset($matches[1]) && $matches[1]) {
                $params = explode('?', $matches[1], 2);
                $this->funcname = $params[0];
                if (isset($params[1])) {
                    $this->params = array_merge($this->params, explode('&', $params[1]));
                }
            } else {
                if (count($this->params) > 0) {
                    $this->funcname = array_shift($this->params);
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * フィルタ実行用イベント
     *
     * @param string フィルタ適用対象データ
     * @return string フィルタ適用後データ
     */
    protected function apply($value)
    {
        return call_user_func_array($this->funcname, array_merge(array($value), $this->params));
    }
}

