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
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** フォームページ遷移設定XMLファイル読み込みクラス */
require_once 'SyL_FormManagerConfigXmlReader.php';

/**
 * フォームページ遷移設定XMLファイルクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormManagerConfigXml extends SyL_FormManagerConfigAbstract
{
    /**
     * フォームページ遷移設定リソースをパースし取得する
     */
    public function parse()
    {
        $reader = new SyL_FormManagerConfigXmlReader();
        $reader->setResource($this->filename);
        $reader->parse();
        $pages = $reader->getPages();
        $forwards = $reader->getForwards();
        $reader = null;
        
        foreach ($forwards as $name => $forward) {
            $this->config[$name] = array();
            $this->config[$name]['from.id']   = $forward['from'];
            $this->config[$name]['from.type'] = $pages[$forward['from']]['type'];
            $this->config[$name]['to.id']     = $forward['to'];
            $this->config[$name]['to.type']   = $pages[$forward['to']]['type'];
        }

        foreach ($pages as $id => $page) {
            if ($page['file']) {
                $this->config_files[$id] = $page['file'];
            }
        }
    }
}
