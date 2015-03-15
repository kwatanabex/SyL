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
 * キャッシュクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Cache
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_CacheEntityAbstract extends SyL_CacheAbstract
{
    /**
     * SHA1済みのキャッシュのキー
     *
     * @var string
     */
    private $key = '';

    /**
     * コンストラクタ
     *
     * @param string キャッシュキー
     */
    public function __construct($key)
    {
        parent::__construct();
        $this->key = sha1($key);
    }

    /**
     * キャッシュのキーを取得
     *
     * 取得するキーは、sha1 化され接頭辞と接尾辞が付加される。
     *
     * @return string キャッシュキー
     */
    public function getKey()
    {
        return $this->prefix . $this->key . $this->suffix;
    }

    /**
     * キャッシュの更新時間を更新する
     *
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheException キャッシュ更新時例外
     */
    public abstract function updateCacheTime();

    /**
     * キャッシュを読み込む
     *
     * @return mixed キャッシュデータ
     * @throws SyL_CacheNotFoundException キャッシュデータが存在しない場合
     * @throws SyL_CacheInvalidHashException キャッシュのハッシュ値が一致しない場合
     * @throws SyL_CacheException キャッシュ読み込み時例外
     */
    public abstract function read();

    /**
     * キャッシュを保存する
     *
     * @param mixed キャッシュデータ
     * @throws SyL_CacheException キャッシュ保存時例外
     */
    public abstract function write($data);

    /**
     * キャッシュを削除する
     *
     * @throws SyL_CacheException キャッシュ削除時例外
     */
    public abstract function remove();

}
