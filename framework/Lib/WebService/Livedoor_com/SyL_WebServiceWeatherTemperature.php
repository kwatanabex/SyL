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

/** Livedoor お天気Webサービスレスポンス結果最高気温情報クラス */
require_once 'SyL_WebServiceWeatherMax.php';
/** Livedoor お天気Webサービスレスポンス結果最低気温情報クラス */
require_once 'SyL_WebServiceWeatherMin.php';

/**
 * Livedoor お天気Webサービスレスポンス結果気温情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherTemperature extends SyL_WebServiceResultAbstract
{
    /**
     * 最高気温
     *
     * @var SyL_WebServiceWeatherMax
     */
     private $max = null;
    /**
     * 最低気温
     *
     * @var SyL_WebServiceWeatherMin
     */
     private $min = null;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->max = new SyL_WebServiceWeatherMax();
        $this->min = new SyL_WebServiceWeatherMin();
    }

    /**
     * 最高気温を取得する
     *
     * @return SyL_WebServiceWeatherMax 最高気温
     */
    public function getMax()
    {
        return $this->max;
    }
    /**
     * 最高気温をセットする
     *
     * @param SyL_WebServiceWeatherMax 最高気温
     */
    public function setMax(SyL_WebServiceWeatherMax $max)
    {
        $this->max = $max;
    }

    /**
     * 最低気温を取得する
     *
     * @return SyL_WebServiceWeatherMin 最低気温
     */
    public function getMin()
    {
        return $this->min;
    }
    /**
     * 最低気温をセットする
     *
     * @param SyL_WebServiceWeatherMin 最低気温
     */
    public function setMin(SyL_WebServiceWeatherMin $min)
    {
        $this->min = $min;
    }

}
