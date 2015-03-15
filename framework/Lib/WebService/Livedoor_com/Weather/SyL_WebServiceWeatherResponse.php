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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** Livedoor お天気Webサービスレスポンス結果レコード情報クラス */
require_once 'SyL_WebServiceWeatherResult.php';

/**
 * Livedoor お天気Webサービスレスポンスクラス
 *
 * 詳細は、Weather Hacks（気象データ配信サービス）参照
 *   http://weather.livedoor.com/weather_hacks/
 * お天気Webサービス
 *   http://weather.livedoor.com/weather_hacks/webservice.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherResponse extends SyL_WebServiceLivedoor_comResponse
{
    /**
     * コンストラクタ
     * 
     * @param string レスポンスステータスコード
     * @param string リクエスト結果XML
     * @param array レスポンスヘッダ配列
     */
    public function __construct($status_code, $result, array $headers)
    {
        $this->results[0] = new SyL_WebServiceWeatherResult();
        parent::__construct($status_code, $result, $headers);
    }

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
        // 予報を発表した地域
        case '/lwws/location':
            $location = new stdClass();
            if (isset($attributes['area'])) {
                $location->area = $attributes['area'];
            }
            if (isset($attributes['pref'])) {
                $location->pref = $attributes['pref'];
            }
            if (isset($attributes['city'])) {
                $location->city = $attributes['city'];
            }
            $this->results[0]->setLocation($location);
            break;
        // タイトル・見出し
        case '/lwws/title': $this->results[0]->setTitle($text); break;
        // リクエストされたデータの地域に該当するlivedoor 天気情報のURL
        case '/lwws/link': $this->results[0]->setLink($text); break;
        // 予報日（today,tomorrow,dayaftertomorrowの3種）
        case '/lwws/forecastday': $this->results[0]->setForecastday($text); break;
        // 曜日
        case '/lwws/day': $this->results[0]->setDay($text); break;
        // 予報日
        case '/lwws/forecastdate': $this->results[0]->setForecastdate($text); break;
        // 予報の発表日時
        case '/lwws/publictime': $this->results[0]->setPublictime($text); break;
        // 天気（晴れ、曇り、雨など）
        case '/lwws/telop': $this->results[0]->setTelop($text); break;
        // 天気概況文
        case '/lwws/description': $this->results[0]->setDescription($text); break;
        // 天気（晴れ、曇り、雨など）
        case '/lwws/image/title': $this->results[0]->getImage()->setTitle($text); break;
        // リクエストされたデータの地域に該当するlivedoor 天気情報のURL
        case '/lwws/image/link': $this->results[0]->getImage()->setLink($text); break;
        // 天気アイコンのURL
        case '/lwws/image/url': $this->results[0]->getImage()->setUrl($text); break;
        // 天気アイコンの幅
        case '/lwws/image/width': $this->results[0]->getImage()->setWidth($text); break;
        // 天気アイコンの高さ
        case '/lwws/image/height': $this->results[0]->getImage()->setHeight($text); break;
        // 最高気温
        case '/lwws/temperature/max/celsius':    $this->results[0]->getTemperature()->getMax()->setCelsius($text); break;
        case '/lwws/temperature/max/fahrenheit': $this->results[0]->getTemperature()->getMax()->setFahrenheit($text); break;
        // 最低気温
        case '/lwws/temperature/min/celsius':    $this->results[0]->getTemperature()->getMin()->setCelsius($text); break;
        case '/lwws/temperature/min/fahrenheit': $this->results[0]->getTemperature()->getMin()->setFahrenheit($text); break;

        // ピンポイント天気予報
        case '/lwws/pinpoint/location':
            $this->index++;
            $pinpoint = $this->results[0]->getPinpoint();
            $pinpoint[$this->index] = new SyL_WebServiceWeatherLocation();
            $this->results[0]->setPinpoint($pinpoint);
            break;
        // 市区町村名
        case '/lwws/pinpoint/location/title': 
            $pinpoint = $this->results[0]->getPinpoint();
            $pinpoint[$this->index]->setTitle($text);
            $this->results[0]->setPinpoint($pinpoint);
            break;
        // 対応するlivedoor 天気情報のURL
        case '/lwws/pinpoint/location/link':
            $pinpoint = $this->results[0]->getPinpoint();
            $pinpoint[$this->index]->setLink($text);
            $this->results[0]->setPinpoint($pinpoint);
            break;
        // ピンポイント天気予報の発表時間
        case '/lwws/pinpoint/location/publictime':
            $pinpoint = $this->results[0]->getPinpoint();
            $pinpoint[$this->index]->setPublictime($text);
            $this->results[0]->setPinpoint($pinpoint);
            break;
        // コピーライトの文言
        case '/lwws/copyright/title': $this->results[0]->getCopyright()->setTitle($text); break;
        // livedoor 天気情報のURL
        case '/lwws/copyright/link': $this->results[0]->getCopyright()->setLink($text); break;
        // livedoor 天気情報へのURL、アイコンなど
        case '/lwws/copyright/image/title':  $this->results[0]->getCopyright()->getImage()->setTitle($text); break;
        case '/lwws/copyright/image/link':   $this->results[0]->getCopyright()->getImage()->setLink($text); break;
        case '/lwws/copyright/image/url':    $this->results[0]->getCopyright()->getImage()->setUrl($text); break;
        case '/lwws/copyright/image/width':  $this->results[0]->getCopyright()->getImage()->setWidth($text); break;
        case '/lwws/copyright/image/height': $this->results[0]->getCopyright()->getImage()->setHeight($text); break;
        // livedoor 天気情報で使用している気象データの配信元
        case '/lwws/copyright/provider': 
            $provider = new SyL_WebServiceWeatherProvider();
            if (isset($attributes['name'])) {
                $provider->setName($attributes['name']);
            }
            if (isset($attributes['link'])) {
                $provider->setLink($attributes['link']);
            }
            $providers = $this->results[0]->getCopyright()->getProvider();
            $providers[] = $provider;
            $this->results[0]->getCopyright()->setProvider($providers);
            break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }
}
