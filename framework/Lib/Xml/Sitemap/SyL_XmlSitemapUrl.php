<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @subpackage SyL.Lib.Xml.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス（パラメータ固定） */
require_once dirname(__FILE__) . '/../../SyL_FixedProperty.php';

/**
 * サイトマップURLクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Xml.Sitemap
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_XmlSitemapUrl extends SyL_FixedProperty
{
    /**
     * プロパティ配列
     * 
     * @var array
     */
    protected $properties = array(
      'loc'        => null,
      'lastmod'    => null,
      'changefreq' => null,
      'priority'   => null
    );

    /**
     * プロパティを検証する
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    protected function validate($name, $value)
    {
        switch ($name) {
        case 'loc':
            if (!$value) {
                throw new SyL_InvalidParameterException("validate failed ({$name})");
            }
            break;

        case 'lastmod':
            if ($value && !($value instanceof DateTime)) {
                $value = new DateTime($value);
            }
            break;

        case 'changefreq':
            if ($value) {
                switch ($value) {
                case 'always':
                case 'hourly':
                case 'daily':
                case 'weekly':
                case 'monthly':
                case 'yearly':
                case 'never':
                    break;
                default:
                    throw new SyL_InvalidParameterException("validate failed ({$name})");
                }
            }
            break;

        case 'priority':
            if ($value && !preg_match('/^(1\.0|0\.[0-9])$/', $value)) {
                throw new SyL_InvalidParameterException("validate failed ({$name})");
            }
            break;

        }

        return $value;
    }
}
