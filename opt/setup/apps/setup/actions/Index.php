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
 * setup エントリポイントクラス
 *
 * @package    SyL.Apps
 * @subpackage SyL.Apps.Setup
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class Index extends AppAction
{
    /**
     * アクション実行メソッド
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データ管理オブジェクト
     */
    public function executeSetup(SyL_ContextAbstract $context, SyL_Data $data)
    {
        $action = null;

        $setup_type = $data->geta('s', 0);

        switch ($setup_type) {
        case 'project':
        case 'pro':
            include_once 'Project.php';
            $action = new Project();
            break;
        case 'application':
        case 'app':
            include_once 'Application.php';
            $action = new Application();
            break;
        case 'controller':
        case 'con':
            include_once 'Controller.php';
            $action = new Controller();
            break;
        case 'action':
        case 'act':
            include_once 'Action.php';
            $action = new Action();
            break;
        case 'template':
        case 'tem':
            include_once 'Template.php';
            $action = new Template();
            break;
        }

        if ($action instanceof AppAction) {
            $action->executeSetup($context, $data);
        } else {
            throw new HelpException('help');
        }
    }

}
