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
 * Livedoor お天気Webサービスレスポンス結果最高気温情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherMax extends SyL_WebServiceResultAbstract
{
    /**
     * 摂氏
     *
     * @var string
     */
     private $celsius = null;
    /**
     * 華氏
     *
     * @var string
     */
     private $fahrenheit = null;

    /**
     * 摂氏を取得する
     *
     * @return string 摂氏
     */
    public function getCelsius()
    {
        return $this->celsius;
    }
    /**
     * 摂氏をセットする
     *
     * @param string 摂氏
     */
    public function setCelsius($celsius)
    {
        $this->celsius = $celsius;
    }

    /**
     * 華氏を取得する
     *
     * @return string 華氏
     */
    public function getFahrenheit()
    {
        return $this->fahrenheit;
    }
    /**
     * 華氏をセットする
     *
     * @param string 華氏
     */
    public function setFahrenheit($fahrenheit)
    {
        $this->fahrenheit = $fahrenheit;
    }

}
