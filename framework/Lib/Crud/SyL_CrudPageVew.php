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
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * CRUD 新規登録ページクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudPageVew extends SyL_CrudPageAbstract
{
    /**
     * コンストラクタ
     *
     * @param SyL_CrudConfigAbstract CRUD設定オブジェクト
     */
    protected function __construct(SyL_CrudConfigAbstract $config)
    {
        parent::__construct($config);
    }

    /**
     * 関連リンク情報を取得する
     *
     * @return array 関連リンク情報
     */
    public function getRelatedLinks()
    {
        return $this->config->getRelatedLinks();
    }

    /**
     * フォーム表示オブジェクトを取得する
     *
     * @return SyL_CrudForm フォーム表示オブジェクト
     */
    public function getFormView()
    {
        $record = $this->config->getAccess()->getRecord($this->getId());
        $form = $this->config->createForm();
        foreach ($record as $name => $value) {
            $form->getElement($name)->setValue($value);
        }
        return $form->getView();
    }
}

