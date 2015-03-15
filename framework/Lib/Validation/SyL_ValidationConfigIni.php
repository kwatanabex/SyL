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
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * 検証設定iniファイルクラス
 * 
 * ファイル例）
 *
 * [def1]
 * displayName="名前"
 *
 * validator1=require
 * validator1.message={name}は必須です
 * validator1.option.max=19
 * validator1.option.min=1
 * 
 * validator2=require
 * validator2.message={name}は必須です
 * validator2.option.max=19
 * validator2.option.min=1
 * 
 * [def2]
 * ...
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Validation
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_ValidationConfigIni extends SyL_ValidationConfigAbstract
{
    /**
     * 検証設定リソースをパースし取得する
     */
    public function parse()
    {
         foreach (parse_ini_file($this->filename, true) as $section => $sections) {
             $this->config[$section]      = array();
             $this->config_name[$section] = $section;
             foreach ($sections as $key => $value) {
                 if (preg_match('/^validator(\d+)$/', $key, $matches)) {
                     $i = (int)$matches[1] - 1;
                     $this->config[$section][$i]['validator'] = $value;
                 } else if (preg_match('/^validator(\d+)\.message$/', $key, $matches)) {
                     $i = (int)$matches[1] - 1;
                     $this->config[$section][$i]['message'] = $value;
                 } else if (preg_match('/^validator(\d+)\.option\.(.+)$/', $key, $matches)) {
                     $i = (int)$matches[1] - 1;
                     $this->config[$section][$i]['options'][$matches[2]] = $value;
                 } else if ($key == 'display') {
                     $this->config_name[$section] = $value;
                 }
             }
         }
    }
}
