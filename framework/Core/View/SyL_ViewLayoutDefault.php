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
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** レイアウトビュークラス */
require_once 'SyL_ViewLayoutAbstract.php';

/**
 * デフォルトレイアウトビュークラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ViewLayoutDefault extends SyL_ViewLayoutAbstract
{
    /**
     * 部分テンプレートファイルを取得
     * 
     * @param string 部分テンプレート名
     * @return string 部分テンプレートファイル
     * @throws SyL_InvalidParameterException layouts.xml で設定されている partial name と一致しない場合
     */
    public function getPartialFile($name)
    {
        return $this->getTemplateDir() . parent::getPartialFile($name);
    }

    /**
     * コンテンツファイルを表示
     */
    public function getContentFile()
    {
        return $this->getTemplateDir() . parent::getContentFile();
    }

    /**
     * 表示レンダリング実行
     */
    protected function renderDisplay()
    {
        $view = $this;
        $v = $this;
        include_once $this->getTemplateDir() . parent::getTemplateFile();
    }
}
