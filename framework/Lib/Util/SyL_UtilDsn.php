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
 * @subpackage SyL.Lib.Util
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * データソース文字列クラス
 *
 * データソース文字列のフォーマット例
 *   scheme://user:pass@host:port/path?option1=value1&option2=value2&...#fragment
 *   scheme://user@host/path
 *   scheme://host/path
 *   scheme://host
 *   scheme:///path
 *   /path
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Util
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_UtilDsn
{
    /**
     * データソース文字列を分解する
     *
     * @param string データソース文字列
     * @return array array(scheme, username, password, hostname, port, path, query, fragment);
     */
    public static function parse($dsn)
    {
        $scheme = null;
        $path   = null;
        $user   = null;
        $pass   = null;
        $host   = null;
        $port   = null;
        $query  = array();
        $fragment = null;

        if (preg_match('|^(.+)://(/.*)$|', $dsn, $matches)) {
            $scheme = $matches[1];
            $tmp = explode('?', $matches[2], 2);
            if (isset($tmp[1])) {
                $path = $tmp[0];
                $tmp = explode('#', $tmp[1], 2);
                parse_str($tmp[0], $query);
                if (isset($tmp[1])) {
                    $fragment = $tmp[1];
                    $tmp[0] = $tmp[1];
                }
            } else {
                $tmp = explode('#', $tmp[0], 2);
                $path = $tmp[0];
                if (isset($tmp[1])) {
                    $fragment = $tmp[1];
                }
            }
        } else if (strpos($dsn, '://') !== false) {
            $dsns = parse_url($dsn);
            if (isset($dsns['scheme'])) {
                $scheme = $dsns['scheme'];
            }
            if (isset($dsns['path'])) {
                $path = $dsns['path'];
            }
            if (isset($dsns['user'])) {
                $user = $dsns['user'];
            }
            if (isset($dsns['pass'])) {
                $pass = $dsns['pass'];
            }
            if (isset($dsns['host'])) {
                $host = $dsns['host'];
            }
            if (isset($dsns['port'])) {
                $port = $dsns['port'];
            }
            if (isset($dsns['fragment'])) {
                $fragment = $dsns['fragment'];
            }
            if (isset($dsns['query'])) {
                parse_str($dsns['query'], $query);
            }
        } else {
            $scheme = $dsn;
        }

        return array($scheme, $user, $pass, $host, $port, $path, $query, $fragment);
    }
}
