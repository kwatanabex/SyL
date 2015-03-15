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
 * @package    SyL.Core
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * リスエストクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RequestCmd extends SyL_RequestAbstract
{
    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        parent::__construct();

        $script_file = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
        SyL_Logger::info("execute: {$script_file}");
    }

    /**
     * 外部パラメータを取得する
     *
     * @return array 外部パラメータ
     */
    protected function getInputs()
    {
        // パラメータ取得
        $args = $_SERVER['argv'];

        $i = 0;
        $tmp_arg = null;
        $parameters = array();
        foreach ($args as $arg) {
            if ((substr($arg, 0, 2) == '--') && (strlen($arg) > 2)) {
                $tmp_args = explode('=', substr($arg, 2), 2);
                $tmp = (count($tmp_args) > 1) ? $tmp_args[1] : null;
                if (isset($parameters[$tmp_args[0]])) {
                    $parameters[$tmp_args[0]][] = $tmp;
                } else {
                    $parameters[$tmp_args[0]] = array($tmp);
                }
                $tmp_arg = null;
            } else if (($arg[0] == '-') && (strlen($arg) > 1)) {
                $tmp_arg = substr($arg, 1);
                if (isset($parameters[$tmp_arg])) {
                    $parameters[$tmp_arg][] = null;
                } else {
                    $parameters[$tmp_arg] = array(null);
                }
            } else {
                if ($tmp_arg !== null) {
                    $cnt = count($parameters[$tmp_arg])-1;
                    $parameters[$tmp_arg][$cnt] = $arg;
                    $tmp_arg = null;
                } else {
                    $parameters[$i++] = $arg;
                }
            }
        }

        return $parameters;
    }
}
