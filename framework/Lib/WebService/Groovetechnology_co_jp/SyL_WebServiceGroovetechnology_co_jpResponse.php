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
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * Groove Technolorgy レスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceGroovetechnology_co_jpResponse extends SyL_WebServiceResponseAbstract
{
    /**
     * APIのバージョン
     *
     * @var string
     */
     private $version = null;

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        switch ($current_path) {
        case '/lwws':
            if (isset($attributes['version'])) {
                $this->version = $attributes['version'];
            }
            break;
        }
    }

    /**
     * APIのバージョン
     *
     * @return string APIのバージョン
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * レスポンス内容のチェック
     *
     * @throws SyL_WebServiceResultException 取得結果にエラーがある場合
     */
    public function validate()
    {
        $headers = $this->getHeaders();
        if (isset($headers['X-GT-Error'])) {
            throw new SyL_WebServiceResultException("response header error `X-GT-Error'. error code: " . $headers['X-GT-Error'][0]);
        }
    }
}
