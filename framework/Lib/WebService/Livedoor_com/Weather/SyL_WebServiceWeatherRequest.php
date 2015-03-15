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
 * Livedoor お天気Webサービスリクエストクラス
 *
 * 詳細は、Weather Hacks（気象データ配信サービス）参照
 *   http://weather.livedoor.com/weather_hacks/
 * お天気Webサービス
 *   http://weather.livedoor.com/weather_hacks/webservice.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherRequest extends SyL_WebServiceLivedoor_comRequest
{
    /**
     * WEBサービスサーバーのホスト名
     *
     * @var string
     */
    protected $host = 'weather.livedoor.com';
    /**
     * 実行URLパス
     *
     * @var string
     */
    protected $path = '/forecast/webservice/rest/v1';

    /**
     * 地域別に定義されたID番号をセットする
     *
     * @param string 地域別に定義されたID番号
     */
    public function setCity($value)
    {
        if (preg_match('/^\d+$/', $value)) {
            parent::set('city', $value);
        } else {
            throw new SyL_InvalidParameterException("Invalid parameter `city' ({$value})");
        }
    }

    /**
     * リクエストする予報日をセットする
     *
     * @param string リクエストする予報日
     */
    public function setDay($value)
    {
        switch ($value) {
        case 'today':
        case 'tomorrow':
        case 'dayaftertomorrow':
            parent::set('day', $value);
            break;
        default:
            throw new SyL_InvalidParameterException("Invalid parameter `day' ({$value})");
        }
    }
}
