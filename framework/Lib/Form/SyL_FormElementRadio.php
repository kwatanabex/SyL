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
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ラジオボタンフォーム要素クラス
 *
 * 名前 : 要素部品名 : 要素名(name) : 値(value)
 *   1  :      n     :    1         :  n
 *
 * ○インスタンス作成方法
 * // 基底クラスからスタティックメソッドで取得
 * $SyL_FormElementText = SyL_FormElement::createElement( 'radio', 'gender', array( '1'=> '男', '2' => '女' ) );
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Form
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_FormElementRadio extends SyL_FormElementAbstract
{
    /**
     * 要素間の区切り文字
     *
     * @var string
     */
    private $separator = " \r\n";

    /**
     * 要素間の区切り文字をセット
     *
     * @param string 要素間の区切り文字
     */
    public function setSeparator($separetor)
    {
        $this->separator = $separetor;
    }

    /**
     * フォーム要素HTML出力（入力項目）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlTag()
    {
        $this->setAttribute($this->name, 'name');
        $this->setAttribute('radio', 'type');

        $i = 1;
        $options = array();
        foreach ($this->options as $display_name => $value) {
            $id = $this->prefix_id . $this->name . '_' . $i++;
            $this->setAttribute($id, 'id');
            $this->setAttribute($value, 'value');
            $checked = ((string)$value === $this->value) ? ' checked' : '';
            $options[] = sprintf('<input %s /><label for="%s">%s</label>', $this->getAttributeString() . $checked, self::encode($id), self::encode($display_name));
        }

        return implode($this->separator, $options);
    }

    /**
     * フォーム要素HTML出力（表示）
     *
     * @return string フォーム要素のHTML
     */
    protected function getHtmlView()
    {
        $label = '';
        foreach ($this->options as $display_name => $value) {
            if ((string)$value === $this->value) {
                $label = $display_name;
                break;
            }
        }
        return $this->getHtmlSpan($this->name, self::encode($label)) . $this->getHtmlHidden();
    }
}
