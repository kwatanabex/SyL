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

/** Livedoor お天気Webサービスレスポンス結果配信元情報クラス */
require_once 'SyL_WebServiceWeatherProvider.php';

/**
 * Livedoor お天気Webサービスレスポンス結果コピーライト情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherCopyright extends SyL_WebServiceResultAbstract
{
    /**
     * コピーライトの文言
     *
     * @var string
     */
     private $title = null;
    /**
     * livedoor 天気情報のURL
     *
     * @var string
     */
     private $link = null;
    /**
     * livedoor 天気情報へのURL、アイコンなど
     *
     * @var SyL_WebServiceWeatherImage
     */
     private $image = null;
    /**
     * livedoor 天気情報で使用している気象データの配信元
     *
     * @var array
     */
     private $provider = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->image = new SyL_WebServiceWeatherImage();
    }

    /**
     * コピーライトの文言を取得する
     *
     * @return string コピーライトの文言
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * コピーライトの文言をセットする
     *
     * @param string コピーライトの文言
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * livedoor 天気情報のURLを取得する
     *
     * @return string livedoor 天気情報のURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * livedoor 天気情報のURLをセットする
     *
     * @param string livedoor 天気情報のURL
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * livedoor 天気情報へのURL、アイコンなどを取得する
     *
     * @return SyL_WebServiceWeatherImage livedoor 天気情報へのURL、アイコンなど
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * livedoor 天気情報へのURL、アイコンなどをセットする
     *
     * @param SyL_WebServiceWeatherImage livedoor 天気情報へのURL、アイコンなど
     */
    public function setImage(SyL_WebServiceWeatherImage $image)
    {
        $this->image = $image;
    }

    /**
     * livedoor 天気情報で使用している気象データの配信元を取得する
     *
     * @return array livedoor 天気情報で使用している気象データの配信元
     */
    public function getProvider()
    {
        return $this->provider;
    }
    /**
     * livedoor 天気情報で使用している気象データの配信元をセットする
     *
     * @param array livedoor 天気情報で使用している気象データの配信元
     */
    public function setProvider(array $provider)
    {
        $this->provider = $provider;
    }
}
