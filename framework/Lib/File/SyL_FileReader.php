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
 * @subpackage SyL.Lib.File
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ファイル操作クラス */
require_once 'SyL_FileAbstract.php';

/**
 * ファイル読み込みクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.File
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FileReader extends SyL_FileAbstract
{
    /**
     * ファイルオープンモード
     * 
     * @var string
     */
    protected $mode = 'rb';

    /**
     * ファイルから1行ごとに読み込む
     *
     * @return string 入力データ or bool(false) 読み込み終了
     */
    public function read()
    {
        if ($this->csv) {
            if ($this->csv_enclosure) {
                return fgetcsv($this->fp, 0, $this->csv_delimiter, $this->csv_enclosure);
            } else {
                return fgetcsv($this->fp, 0, $this->csv_delimiter);
            }
        } else {
            return fgets($this->fp);
        }
    }

}
