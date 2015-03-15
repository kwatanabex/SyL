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
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ファイルアップロード例外クラス */
require_once 'SyL_HttpFileUploadException.php';
/** ファイルアップロードするファイルクラス */
require_once 'SyL_HttpFileUploadFile.php';

/**
 * ファイルアップロードクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpFileUpload
{
    /**
     * アップロードファイル
     *
     * @var array
     */
    private $upload_files = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        if (ini_get('file_uploads') == '0') {
            throw new SyL_InvalidConfigException("invalid file upload. disable `file_uploads' directive in php.ini");
        }

        $tmp_dir = ini_get('upload_tmp_dir');
        if (!$tmp_dir) {
            $tmp_dir = sys_get_temp_dir();
        }
        if (!is_writable($tmp_dir)) {
            throw new SyL_InvalidConfigException("not writable temporary directory ({$tmp_dir})");
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $content_size  = $_SERVER['CONTENT_LENGTH'];
            $post_max_size = ini_get('post_max_size');

            if (preg_match('/(\d+)(K|M|G)/', $post_max_size, $matches)) {
                switch ($matches[2]) {
                case 'K': $post_max_size = $matches[1] * 1024; break;
                case 'M': $post_max_size = $matches[1] * 1024 * 1024; break;
                case 'G': $post_max_size = $matches[1] * 1024 * 1024 * 1024; break;
                }
            }

            if ($content_size > $post_max_size) {
                throw new SyL_InvalidConfigException("posted data exceeds the `post_max_size' directive in php.ini (content-length:{$content_size}Byte > post_max_size:{$post_max_size}Byte)");
            }
        }

        foreach ($_FILES as $name => $file) {
            $this->upload_files[$name] = array();
            if (is_array($_FILES[$name]['error'])) {
                if (is_array($_FILES[$name]['error'][0])) {
                    throw new SyL_NotImplementedException('array upload not supported more than 2 level depth');
                }
                foreach (array_keys($_FILES[$name]['error']) as $index) {
                    $this->upload_files[$name][] = new SyL_HttpFileUploadFile($name, $index);
                }
            } else {
                $this->upload_files[$name][] = new SyL_HttpFileUploadFile($name);
            }
        }
    }

    /**
     * アップロードファイル情報を取得する
     * 
     * @param string アップロード要素名
     * @return SyL_HttpFileUploadFile アップロードファイル情報
     */
    public function getFileInfo($name)
    {
        $file_info = $this->getFileInfoArray($name);
        return $file_info[0];
    }

    /**
     * 配列としてアップロードされたファイル情報を取得する
     * 
     * @param string アップロード要素名
     * @return array アップロードファイル情報の配列
     */
    public function getFileInfoArray($name)
    {
        if (!isset($this->upload_files[$name])) {
            throw new SyL_FileNotFoundException('upload file not found (' . $name . ')');
        }
        return $this->upload_files[$name];
    }
}
