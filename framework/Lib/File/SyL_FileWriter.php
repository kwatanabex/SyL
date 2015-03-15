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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ファイル操作クラス */
require_once 'SyL_FileAbstract.php';

/**
 * ファイル出力クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.File
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FileWriter extends SyL_FileAbstract
{
    /**
     * ファイルオープンモード
     * 
     * @var string
     */
    protected $mode = 'wb';

    /**
     * ファイルに出力する
     *
     * CSVファイルを指定した場合、自動的に行末に改行がｓ追加される
     *
     * @param mixed メッセージ
     *              通常は string CSV書き出し時は array
     * @throws SyL_InvalidParameterException CSVファイル指定時に、パラメータの型が array 以外の場合
     */
    public function write($message)
    {
        if ($this->csv) {
            if (!is_array($message)) {
                throw new SyL_InvalidParameterException('invalid write parameter format. case csv file, array only');
            }
            $message = $this->csv_enclosure . implode($this->csv_enclosure . $this->csv_delimiter . $this->csv_enclosure, $message) . $this->csv_enclosure . $this->eol;
        }

        flock ($this->fp, LOCK_EX);
        fseek($this->fp, 0, SEEK_END);
        fwrite($this->fp, $message);
        flock ($this->fp, LOCK_UN);
    }

    /**
     * ファイルに出力する（末尾に改行を付加する）
     *
     * @param mixed メッセージ
     *              通常は string CSV書き出し時は array
     */
    public function writeln($message)
    {
        if (is_string($message)) {
            $message .= $this->eol;
        }
        $this->write($message);
    }
}
