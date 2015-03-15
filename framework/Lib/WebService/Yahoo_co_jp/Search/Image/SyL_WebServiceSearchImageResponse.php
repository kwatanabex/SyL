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
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** Yahoo! Japan 検索レスポンスクラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResponse.php';
/** Yahoo! Japan 画像検索レスポンス結果レコード情報クラス */
require_once 'SyL_WebServiceSearchImageResult.php';

/**
 * Yahoo! Japan 画像検索レスポンスクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * 画像検索
 *   http://developer.yahoo.co.jp/webapi/search/imagesearch/v1/imagesearch.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchImageResponse extends SyL_WebServiceSearchResponse
{
    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        switch ($current_path) {
        case '/ResultSet/Result':
            $this->index++;
            $this->results[$this->index] = new SyL_WebServiceSearchImageResult();
            $this->results[$this->index]->setThumbnail(new SyL_WebServiceSearchResultThumbnail());
            break;
        // 画像ファイルのタイトル
        case '/ResultSet/Result/Title': $this->results[$this->index]->setTitle($text); break;
        // 画像ファイルに関連するテキストサマリー
        case '/ResultSet/Result/Summary': $this->results[$this->index]->setSummary($text); break;
        // 画像ファイルのURL
        case '/ResultSet/Result/Url': $this->results[$this->index]->setUrl($text); break;
        // 画像ファイルのリンクURL
        case '/ResultSet/Result/ClickUrl': $this->results[$this->index]->setClickUrl($text); break;
        // 画像へのリンクを含むページのURL
        case '/ResultSet/Result/RefererUrl': $this->results[$this->index]->setRefererUrl($text); break;
        // ファイルサイズ
        case '/ResultSet/Result/FileSize': $this->results[$this->index]->setFileSize($text); break;
        // bmp、gif、jpegまたはpngのいずれか
        case '/ResultSet/Result/FileFormat': $this->results[$this->index]->setFileFormat($text); break;
        // 画像の高さ
        case '/ResultSet/Result/Height': $this->results[$this->index]->setHeight($text); break;
        // 画像の幅
        case '/ResultSet/Result/Width': $this->results[$this->index]->setWidth($text); break;
        // サムネイル画像のURL
        case '/ResultSet/Result/Thumbnail/Url': $this->results[$this->index]->getThumbnail()->setUrl($text); break;
        // サムネイル画像の高さ
        case '/ResultSet/Result/Thumbnail/Height': $this->results[$this->index]->getThumbnail()->setHeight($text); break;
        // サムネイル画像の幅
        case '/ResultSet/Result/Thumbnail/Width': $this->results[$this->index]->getThumbnail()->setWidth($text); break;
        // 画像ファイルの提供者
        case '/ResultSet/Result/Publisher': $this->results[$this->index]->setPublisher($text); break;
        // このメディアオブジェクトの制限事項
        case '/ResultSet/Result/Restrictions':  $this->results[$this->index]->setRestrictions($text); break;
        // オーナーのコピーライト
        case '/ResultSet/Result/Copyright': $this->results[$this->index]->setCopyright($text); break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }
}
