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

/** キャッシュ操作基底クラス */
require_once 'SyL_CacheAbstract.php';

/**
 * キャッシュ管理クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_CacheManagerAbstract extends SyL_CacheAbstract
{
    /**
     * ガベージコレクションを実行する確率
     *
     * @var int
     */
    private $gc = 0;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        if (($this->gc > 0) && ($this->gc >= mt_rand(1, 100))) {
            $this->clean();
        }
    }

    /**
     * キャッシュオブジェクトを作成する
     *
     * @param string キャッシュキー
     * @return SyL_CacheEntityAbstract キャッシュオブジェクト
     */
    public abstract function create($key);

    /**
     * オブジェクト破棄時にガベージコレクションを指定した確率で起動させる
     *
     * @param int 起動確率（0 - 100）
     * @throws SyL_InvalidParameterException 引数が0～100以外の数値の場合
     */
    public function setGc($gc)
    {
        if (is_numeric($gc) && (($gc >= 0) && ($gc <= 100))) {
            $this->gc = $gc;
        } else {
            throw new SyL_InvalidParameterException("Invalid parameter. 0..100 valid ({$gc})");
        }
    }

    /**
     * 期限切れキャッシュを削除する
     */
    public abstract function clean();

    /**
     * キャッシュを全て削除する
     */
    public abstract function cleanAll();
}
