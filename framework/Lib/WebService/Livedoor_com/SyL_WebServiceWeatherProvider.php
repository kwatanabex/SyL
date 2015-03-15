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

/**
 * Livedoor お天気Webサービスレスポンス結果配信元情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherProvider extends SyL_WebServiceResultAbstract
{
    /**
     * 配信元
     *
     * @var string
     */
     private $name = null;
    /**
     * リンク先
     *
     * @var string
     */
     private $link = null;

    /**
     * 配信元を取得する
     *
     * @return string 配信元
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * 配信元をセットする
     *
     * @param string 配信元
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * リンク先を取得する
     *
     * @return string リンク先
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * リンク先をセットする
     *
     * @param string リンク先
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

}
