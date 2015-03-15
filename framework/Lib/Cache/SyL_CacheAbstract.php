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
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** キャッシュ例外クラス */
require_once 'SyL_CacheException.php';

/**
 * キャッシュ操作基底クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_CacheAbstract
{
    /**
     * キャッシュの接頭辞
     *
     * @var string
     */
    protected $prefix = '';
    /**
     * キャッシュの接尾辞
     *
     * @var string
     */
    protected $suffix = '';
    /**
     * キャッシュの有効期間
     * 単位は秒[s]
     *
     * @var int
     */
    protected $life_time = 3600; // 1hour
    /**
     * キャッシュの確認CRCを付加するか
     *
     * @var bool
     */
    protected $is_crc = true;
    /**
     * キャッシュをシリアル化するか
     *
     * @var bool
     */
    protected $is_serialize = true;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * キャッシュの接頭辞をセット
     *
     * @param string キャッシュ名の接頭辞
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * キャッシュの接尾辞をセット
     *
     * @param string キャッシュ名の接尾辞
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * キャッシュ有効時間をセット
     *
     * @param int キャッシュ有効時間（秒[s]）
     */
    public function setLifeTime($life_time)
    {
        $this->life_time = $life_time;
    }

    /**
     * CRCを追加判定フラグをセットする
     * 
     * @param bool CRCを追加判定フラグ
     */
    public function useCrc($is_crc=true)
    {
        $this->is_crc = (bool)$is_crc;
    }

    /**
     * CRCを追加したデータを取得する
     * 
     * @param string データ
     * @return string ハッシュ値
     */
    protected function getCrc($data)
    {
        return sprintf('%+032d', crc32($data));
    }

    /**
     * シリアル化判定フラグをセットする
     * 
     * @param bool シリアル化判定フラグ
     */
    public function useSerialize($is_serialize=true)
    {
        $this->is_serialize = (bool)$is_serialize;
    }
}
