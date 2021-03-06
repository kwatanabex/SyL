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

/**
 * レイアウトビュークラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.View
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ViewLayoutAbstract extends SyL_ViewAbstract
{
    /**
     * レイアウト設定ファイル配列
     * 
     * @var array
     */
    protected $config = array();

    /**
     * コンストラクタ
     *
     * @param SyL_ContextAbstract フィールド情報管理オブジェクト
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_ContextAbstract $context, SyL_Data $data)
    {
        parent::__construct($context, $data);

        $config = SyL_ConfigFileAbstract::createInstance('layouts');
        $config->setRouter($context->getRouter());
        $config->parse();
        $this->config = $config->getConfig();

        $content_type = $context->getViewParameter('content-type');
        if ($content_type) {
            $this->setContentType($content_type);
        } else {
            $this->setContentType('text/html; charset=' . SYL_ENCODE_INTERNAL);
        }
    }

    /**
     * テンプレートファイルを取得
     *
     * @return string テンプレートファイル
     */
    public function getTemplateFile()
    {
        return $this->config['file'];
    }

    /**
     * 部分テンプレートファイルを取得
     * 
     * @param string 部分テンプレート名
     * @return string 部分テンプレートファイル
     * @throws SyL_InvalidParameterException layouts.xml で設定されている partial name と一致しない場合
     */
    public function getPartialFile($name)
    {
        if (isset($this->config['partial'][$name])) {
            return $this->config['partial'][$name];
        } else {
            throw new SyL_InvalidParameterException("partial name not found ({$name})");
        }
    }

    /**
     * コンテンツファイルを表示
     */
    public function getContentFile()
    {
        return parent::getTemplateFile();
    }

    /**
     * レイアウトパラメータを取得する
     *
     * @param string レイアウトパラメータ名
     * @return string レイアウトパラメータ値
     */
    public function getLayoutParameter($name)
    {
        return isset($this->config['parameters'][$name]) ? $this->config['parameters'][$name] : null;
    }

}
