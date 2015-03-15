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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** レイアウトビュークラス */
require_once 'SyL_ViewLayoutAbstract.php';

/**
 * Smartyレイアウトビュークラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ViewLayoutSmartyAbstract extends SyL_ViewLayoutAbstract
{
    /**
     * Smartyオブジェクト
     * 
     * @var Smarty
     */
    private $smarty = null;
    /**
     * Smartyバージョンが3以上
     * 
     * @var bool
     */
    private $version_higher_3 = false;

    /**
     * コンストラクタ
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_ContextAbstract $context, SyL_Data $data)
    {
        parent::__construct($context, $data);

        $this->smarty = $this->createSmarty();
        if (preg_match('/(\d+\.\d+\.\d+)/', $this->smarty->_version, $matches)) {
            $this->version_higher_3 = version_compare($matches[1], '3.0.0', '>=');
        } else {
            throw new SyL_InvalidParameterException('invalid Smarty class');
        }

        $this->smarty->template_dir = $this->getTemplateDir() . DIRECTORY_SEPARATOR;
        $this->smarty->compile_dir = SYL_PROJECT_DIR . '/var/templates/' . SYL_APP_NAME . '/';

        if ($this->isVersionHigher3()) {
            $this->smarty->assignByRef('view', $this);
            $this->smarty->assignByRef('v', $this);
        } else {
            $this->smarty->assign_by_ref('view', $this);
            $this->smarty->assign_by_ref('v', $this);
        }
    }

    /**
     * Smarty オブジェクトを作成する
     *
     * @return Smarty Smartyオブジェクト
     */
    protected abstract function createSmarty();

    /**
     * Smartyのバージョンが3以上か判定する
     *
     * @return bool Smartyのバージョンが3以上か
     */
    protected function isVersionHigher3()
    {
        return $this->version_higher_3;
    }

    /**
     * 部分テンプレートファイルを取得
     * 
     * @param string 部分テンプレート名
     * @return string 部分テンプレートファイル
     */
    public function getPartialFile($name)
    {
        return substr(parent::getPartialFile($name), 1);
    }

    /**
     * コンテンツファイルを表示
     */
    public function getContentFile()
    {
        return substr(parent::getContentFile(), 1);
    }

    /**
     * 表示レンダリング実行
     */
    protected function renderDisplay()
    {
        $this->smarty->display(substr($this->getTemplateFile(), 1));
    }
}
