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
 * @subpackage SyL.Core.Context
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/* セッションクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Session/SyL_SessionAbstract.php';
/* ユーザークラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_UserAbstract.php';

/**
 * WEBフレームワークフィールド情報管理クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Context
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ContextWeb extends SyL_ContextAbstract
{
    /**
     * デフォルトビュークラス
     *
     * @var string
     */
    protected $default_view_class = 'core:View.Default@SyL_';

    /**
     * コンストラクタ
     *
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_Data $data)
    {
        parent::__construct($data);

        $userclass = SyL_CustomClass::getUserClass();
        if ($userclass) {
            SyL_Loader::userLib($userclass);
        }
    }

    /**
     * セッションオブジェクトを取得する
     *
     * @return SyL_SessionAbstract セッションオブジェクト
     */
    public function getSession()
    {
        return SyL_SessionAbstract::getInstance();
    }

    /**
     * ユーザーオブジェクトを作成する
     *
     * @param string ユーザーID
     * @param string ユーザー名
     * @return SyL_UserAbstract ユーザーオブジェクト
     */
    public function createUser($userid, $username=null)
    {
        $user = SyL_UserAbstract::createInstance($userid, $username);
        $this->setUser($user);
        return $user;
    }

    /**
     * ユーザーオブジェクトを取得する
     *
     * @return SyL_UserAbstract ユーザーオブジェクト
     */
    public function getUser()
    {
        return $this->getSession()->get(SyL_UserAbstract::SESSION_KEY);
    }

    /**
     * ユーザーオブジェクトをセットする
     *
     * @param SyL_UserAbstract ユーザーオブジェクト
     */
    public function setUser(SyL_UserAbstract $user)
    {
        return $this->getSession()->set(SyL_UserAbstract::SESSION_KEY, $user);
    }

    /**
     * ダウンロードファイルをセット
     *
     * ※ダウンロードファイル名を指定しない場合は、Content-Disposition: inline になる
     * 
     * @param string ダウンロードファイルパス
     * @param string ダウンロードファイル名
     * @param string コンテンツタイプ
     */
    public function setDownloadFile($file, $filename='', $type='application/octet-stream')
    {
        $data = file_get_contents($file);
        if ($data === false) {
            trigger_error("[SyL error] Unable to get Download file ($file)", E_USER_ERROR);
        }
        $this->setDownloadData($data, $filename, $type);
    }

    /**
     * ダウンロードデータをセット
     * 
     * ※ダウンロードファイル名を指定しない場合は、Content-Disposition: inline になる
     *
     * @param string ダウンロードファイルパス
     * @param string ダウンロードファイル名
     * @param string コンテンツタイプ
     */
    public function setDownloadData($data, $filename='', $type='application/octet-stream')
    {
        $this->router->setViewType('download');
        $this->setParameter('_download_name', $filename);
        $this->setParameter('_download_type', $type);
        $this->setParameter('_download_data', $data);
    }

    /**
     * 出力画像ファイルをセット
     *
     * @param string 画像ファイルパス
     * @param string コンテンツタイプ
     */
    public function setDisplayImageFile($file, $type='')
    {
        if (!$type) {
            $size = getimagesize($file);
            if (!$size || !isset($size['mime'])) {
                trigger_error("[SyL error] Image mime unable to get from getimagesize function ({$file})", E_USER_ERROR);
            }
            $type = $size['mime'];
        }
        $data = @file_get_contents($file);
        if ($data === false) {
            trigger_error("[SyL error] Unable to get Image file ($file)", E_USER_ERROR);
        }
        $this->setDisplayImageData($data, $type);
    }

    /**
     * 出力画像データをセット
     *
     * @param string 画像バイナリデータ
     * @param string コンテンツタイプ
     */
    public function setDisplayImageData($data, $type='')
    {
        $this->router->setViewType('image');
        $this->setParameter('_image_type',  $type);
        $this->setParameter('_image_data',  $data);
    }

    /**
     * XMLオブジェクト（SyL_XmlWriter）をセット
     *
     * @param object XMLオブジェクト
     * @param string コンテンツタイプ
     */
    public function setDisplayXml($xml_object, $type='text/xml')
    {
        $this->router->setViewType('xml');
        $this->setParameter('_xml_type',   $type);
        $this->setParameter('_xml_object', $xml_object);
    }

    /**
     * RSS要素オブジェクトをセットする
     *
     * 自動的にRSSビューに切り替わる
     *
     * @param SyL_RssElementRss RSS要素オブジェクト
     */
    public function setViewRss(SyL_RssElementRss $rss)
    {
        $this->router->setViewClass('core:View.Rss@SyL');
        $this->view_parameters['rss'] = $rss;
    }

    /**
     * AtomPub要素オブジェクトをセットする
     *
     * 自動的にRSSビューに切り替わる
     *
     * @param SyL_AtomElementRootInterface AtomPubルート要素インターフェイス
     */
    public function setViewAtom(SyL_AtomElementRootInterface $atom)
    {
        $this->router->setViewClass('core:View.Atom@SyL');
        $this->view_parameters['atom'] = $atom;
    }

    /**
     * JSON出力用変数をセットする
     *
     * 自動的にJSONビューに切り替わる
     * 
     * @param mixed JSON用出力データ
     * @param string コンテンツタイプ
     */
    public function setViewJson($json, $content_type='application/json')
    {
        $this->router->setViewClass('core:View.Json@SyL');
        $this->view_parameters['json'] = $json;
        $this->view_parameters['content-type'] = $content_type;
    }

    /**
     * コンテンツタイプをセット
     *
     * @param string コンテンツタイプ
     */
    public function setViewContentType($content_type)
    {
        $this->view_parameters['content-type'] = $content_type;
    }
}
