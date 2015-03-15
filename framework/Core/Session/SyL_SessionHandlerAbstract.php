<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - Web Application Framework for PHP
 *
 * PHP version 5 (>= 5.2.10)
 *
 * Copyright (C) 2006-2009 k.watanabe
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
 * @subpackage SyL.Core.Session
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** セッションクラス */
requore_once 'SyL_SessionAbstract.php';

/**
 * セッションハンドラクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Session
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id:$
 * @link      http://syl.jp/
 */
abstract class SyL_SessionHandlerAbstract extends SyL_SessionAbstract
{
    /**
     * セッションハンドラ名
     *
     * @var string
     */
    protected $save_handler = 'user';

    /**
     * セッションを開始直前の処理
     */
    protected function startBefore()
    {
        parent::startBefore();

        session_set_save_handler(
          array($this, 'open'),
          array($this, 'close'),
          array($this, 'read'),
          array($this, 'write'),
          array($this, 'destroy'),
          array($this, 'gc')
        );
    }

    /**
     * セッション開始イベント
     *
     * @param string セッション保存パス
     * @param string セッション名
     * @return bool true
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * セッション読み込みイベント
     *
     * @param string セッションID
     * @return string セッションデータ
     */
    public abstract function read($session_id);

    /**
     * セッション書き込みイベント
     *
     * @param string セッションID
     * @param string セッションデータ
     * @return bool true
     */
    public abstract function write($session_id, $session_data);

    /**
     * セッション終了イベント
     *
     * @return bool true
     */
    public function close()
    {
        return true;
    }

    /**
     * セッション削除イベント
     *
     * @param string セッションID
     * @return bool true
     */
    public function destroy($session_id)
    {
        return true;
    }

    /**
     * ガベージコレクタイベント
     *
     * @param int セッション保持時間
     * @return bool true
     */
    public function gc($life_time)
    {
        return true;
    }
}
