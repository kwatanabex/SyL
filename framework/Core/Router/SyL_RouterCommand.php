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
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * コマンドパラメータからフレームワーク遷移情報を取得するクラス
 *
 * コマンドパラメータは、
 *   コマンドラインだと、foo.php -action Test.Index
 *   WEBだと、foo.php?action=Test.Index
 * となる。
 *
 * アクションメソッドまで含めると、
 *   Test.Index@method
 * のような記述になる。
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Router
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RouterCommand extends SyL_RouterAbstract
{
    /**
     * テンプレート実行フラグ
     *
     * @var bool
     */
    protected $enable_template = false;

    /**
     * 遷移情報オブジェクトのプロパティを作成
     *
     * @param SyL_Data データオブジェクト
     * @return array array(アクションファイル, アクションメソッド, テンプレートファイル)
     */
    protected function createActionInfo(SyL_Data $data)
    {
        $action_file = $data->get(1);
        if (($action_file === null) || ($action_file === '')) {
            $action_file = '/' . $this->action_default_file;
        }

        // 拡張子チェック
        if (!preg_match('/^(.+)(' . preg_quote($this->action_file_ext) . ')$/', $action_file)) {
            throw new SyL_RouterNotFoundException("invalid extension ({$this->action_file_ext})");
        }
        return array($action_file, null);
    }
}
