<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
 *
 * PHP version 5 (>= 5.2.x)
 *
 * Copyright (C) 2006-2010 k.watanabe
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
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Setup
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id: $
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * テンプレートファイル作成クラス
 *
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Setup
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class Template extends AppAction
{
    /**
     * テンプレート作成処理
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function executeSetup(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $project_dir   = $data->geta('d', 0);
        $app_name      = $data->geta('n', 0);
        $template_file = $data->geta('t', 0);

        $cmd = $context->getConsole();

        $this->checkProjectDir($cmd, $project_dir);
        $this->checkApplicationDir($cmd, $project_dir, $app_name);

        if (!$template_file) {
            throw new ErrorException("template file (-t) not found");
        }

        $template_files = array($template_file);
        $this->createTemplateDir($cmd, $project_dir, $app_name, $template_files);
    }

}
