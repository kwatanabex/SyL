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

/** AtomPub要素オブジェクト変換クラス */
require_once SYL_FRAMEWORK_DIR . '/Lib/Atom/SyL_AtomConverter.php';

/**
 * AtomPub ビュークラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ViewAtom extends SyL_ViewAbstract
{
    /**
     * コンストラクタ
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_ContextAbstract $context, SyL_Data $data)
    {
        parent::__construct($context, $data);

        $atom = $this->context->getViewParameter('atom');
        $content_type = $this->context->getViewParameter('content-type');

        if ($atom instanceof SyL_AtomElementService) {
            if ($content_type) {
                $this->setContentType($content_type);
            } else {
                $this->setContentType('application/atomsvc+xml; charset=' . $atom->getEncoding());
            }
        } else if ($atom instanceof SyL_AtomElementFeed) {
            if ($content_type) {
                $this->setContentType($content_type);
            } else {
                $this->setContentType('application/atom+xml; charset=' . $atom->getEncoding());
            }
        } else if ($atom instanceof SyL_AtomElementEntry) {
            if ($content_type) {
                $this->setContentType($content_type);
            } else {
                $this->setContentType('application/atom+xml; type=entry; charset=' . $atom->getEncoding());
            }
        } else {
            throw new SyL_InvalidParameterException('view parameter not set at SyL_AtomElementRootInterface interface');
        }
    }

    /**
     * 表示レンダリング実行
     */
    protected function renderDisplay()
    {
        $atom = $this->context->getViewParameter('atom');
        echo SyL_AtomConverter::toXml($atom);
    }
}
