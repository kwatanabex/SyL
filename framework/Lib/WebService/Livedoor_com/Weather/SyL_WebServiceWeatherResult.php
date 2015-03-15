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

/** Livedoor お天気Webサービスレスポンス結果画像情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceWeatherImage.php';
/** Livedoor お天気Webサービスレスポンス結果気温情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceWeatherTemperature.php';
/** Livedoor お天気Webサービスレスポンス結果地域情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceWeatherLocation.php';
/** Livedoor お天気Webサービスレスポンス結果コピーライト情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceWeatherCopyright.php';

/**
 * Livedoor お天気Webサービスレスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherResult extends SyL_WebServiceResultAbstract
{
    /**
     * 予報を発表した地域
     *
     * @var stdClass
     */
    private $location = null;
    /**
     * タイトル・見出し
     *
     * @var string
     */
    private $title = null;
    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     *
     * @var string
     */
     private $link = null;
    /**
     * 予報日（today,tomorrow,dayaftertomorrowの3種）
     *
     * @var string
     */
     private $forecastday = null;
    /**
     * 曜日
     *
     * @var string
     */
     private $day = null;
    /**
     * 予報日
     *
     * @var string
     */
     private $forecastdate = null;
    /**
     * 予報の発表日時
     *
     * @var string
     */
     private $publictime = null;
    /**
     * 天気
     *
     * @var string
     */
     private $telop = null;
    /**
     * 天気概況文
     *
     * @var string
     */
     private $description = null;
    /**
     * 天気画像
     *
     * @var SyL_WebServiceWeatherImage
     */
     private $image = null;
    /**
     * 気温
     *
     * @var SyL_WebServiceWeatherTemperature
     */
     private $temperature = null;
    /**
     * ピンポイント情報
     *
     * @var array
     */
     private $pinpoint = array();
    /**
     * コピーライト
     *
     * @var SyL_WebServiceWeatherCopyright
     */
     private $copyright = null;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->image = new SyL_WebServiceWeatherImage();
        $this->temperature = new SyL_WebServiceWeatherTemperature();
        $this->copyright = new SyL_WebServiceWeatherCopyright();
    }

    /**
     * 予報を発表した地域を取得する
     *
     * @return stdClass 予報を発表した地域
     */
    public function getLocation()
    {
        return $this->location;
    }
    /**
     * 予報を発表した地域ルをセットする
     *
     * @param stdClass 予報を発表した地域
     */
    public function setLocation(stdClass $location)
    {
        $this->location = $location;
    }

    /**
     * タイトル・見出しを取得する
     *
     * @return string タイトル・見出し
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * タイトル・見出しをセットする
     *
     * @param string タイトル・見出し
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURLを取得する
     *
     * @return string リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURLをセットする
     *
     * @param string リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * 予報日（today,tomorrow,dayaftertomorrowの3種）を取得する
     *
     * @return string 予報日（today,tomorrow,dayaftertomorrowの3種）
     */
    public function getForecastday()
    {
        return $this->forecastday;
    }
    /**
     * 予報日（today,tomorrow,dayaftertomorrowの3種）をセットする
     *
     * @param string 予報日（today,tomorrow,dayaftertomorrowの3種）
     */
    public function setForecastday($forecastday)
    {
        $this->forecastday = $forecastday;
    }

    /**
     * 曜日を取得する
     *
     * @return string 曜日
     */
    public function getDay()
    {
        return $this->day;
    }
    /**
     * 曜日をセットする
     *
     * @param string 曜日
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * 予報日を取得する
     *
     * @return string 予報日
     */
    public function getForecastdate()
    {
        return $this->forecastdate;
    }
    /**
     * 予報日をセットする
     *
     * @param string 予報日
     */
    public function setForecastdate($forecastdate)
    {
        $this->forecastdate = $forecastdate;
    }

    /**
     * 予報の発表日時を取得する
     *
     * @return string 予報の発表日時
     */
    public function getPublictime()
    {
        return $this->publictime;
    }
    /**
     * 予報の発表日時をセットする
     *
     * @param string 予報の発表日時
     */
    public function setPublictime($publictime)
    {
        $this->publictime = $publictime;
    }

    /**
     * 天気を取得する
     *
     * @return string 天気
     */
    public function getTelop()
    {
        return $this->telop;
    }
    /**
     * 天気をセットする
     *
     * @param string 天気
     */
    public function setTelop($telop)
    {
        $this->telop = $telop;
    }

    /**
     * 天気概況文を取得する
     *
     * @return string 天気概況文
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * 天気概況文をセットする
     *
     * @param string 天気概況文
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 天気画像を取得する
     *
     * @return SyL_WebServiceWeatherImage 天気画像
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * 天気画像をセットする
     *
     * @param SyL_WebServiceWeatherImage 天気画像
     */
    public function setImage(SyL_WebServiceSearchResultCache $image)
    {
        $this->image = $image;
    }

    /**
     * 気温を取得する
     *
     * @return SyL_WebServiceWeatherTemperature 気温
     */
    public function getTemperature()
    {
        return $this->temperature;
    }
    /**
     * 気温をセットする
     *
     * @param SyL_WebServiceWeatherImage 気温
     */
    public function setTemperature(SyL_WebServiceWeatherTemperature $temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * ピンポイント情報を取得する
     *
     * @return array ピンポイント情報
     */
    public function getPinpoint()
    {
        return $this->pinpoint;
    }
    /**
     * ピンポイント情報をセットする
     *
     * @param array ピンポイント情報
     */
    public function setPinpoint(array $pinpoint)
    {
        $this->pinpoint = $pinpoint;
    }

    /**
     * コピーライトを取得する
     *
     * @return SyL_WebServiceWeatherCopyright コピーライト
     */
    public function getCopyright()
    {
        return $this->copyright;
    }
    /**
     * コピーライトをセットする
     *
     * @param SyL_WebServiceWeatherCopyright コピーライト
     */
    public function setCopyright(SyL_WebServiceWeatherCopyright $copyright)
    {
        $this->copyright = $copyright;
    }
}
