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

/**
 * ファイルアップロードするファイルクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpFileUploadFile
{
    /**
     * アップロードファイル名
     *
     * @var string
     */
    private $name;
    /**
     * ファイルタイプ
     *
     * @var string
     */
    private $type;
    /**
     * 一時保存ファイル名
     *
     * @var string
     */
    private $tmp_name;
    /**
     * ファイルサイズ
     *
     * @var int
     */
    private $size;

    /**
     * コンストラクタ
     *
     * @param string アップロード要素名
     * @param string アップロードインデックス
     */
    public function __construct($name, $index=null)
    {
        $error_code = UPLOAD_ERR_OK;
        if ($index === null) {
            if (!isset($_FILES[$name])) {
                throw new SyL_FileNotFoundException('upload file not found (' . $name . ')');
            }
            $this->name = $_FILES[$name]['name'];
            $this->type = $_FILES[$name]['type'];
            $this->tmp_name = $_FILES[$name]['tmp_name'];
            $this->size  = $_FILES[$name]['size'];
            $error_code = $_FILES[$name]['error'];
        } else {
            if (!isset($_FILES[$name]['name'][$index])) {
                throw new SyL_FileNotFoundException('upload file not found (' . $name . '[' . $index . '])');
            }
            $this->name = $_FILES[$name]['name'][$index];
            $this->type = $_FILES[$name]['type'][$index];
            $this->tmp_name = $_FILES[$name]['tmp_name'][$index];
            $this->size  = $_FILES[$name]['size'][$index];
            $error_code = $_FILES[$name]['error'][$index];
        }

        if ($error_code != UPLOAD_ERR_OK) {
            throw new SyL_HttpFileUploadException(self::getErrorMessage($error_code, $name, $index), $error_code);
        }
    }

    /**
     * アップロードファイル名を取得する
     * 
     * @return string アップロードファイル名
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * ファイルタイプを取得する
     * 
     * @return string ファイルタイプ
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 一時保存ファイル名を取得する
     * 
     * @return string 一時保存ファイル名
     */
    public function getTmpName()
    {
        return $this->tmp_name;
    }

    /**
     * ファイルサイズを取得する
     * 
     * @return int ファイルサイズ
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * ファイルアップロード実行
     * 
     * @param string 保存ディレクトリ名
     * @param string 保存ファイル名
     * @throws SyL_PermissionDeniedException ディレクトリに書き込み権限が無い場合
     * @throws SyL_HttpFileUploadException move_uploaded_file実行時エラー
     */
    public function upload($save_dir, $save_file=null)
    {
        if ($save_file === null) {
            $save_file = $this->name;
        }

        if (!is_dir($save_dir)) {
            throw new SyL_FileNotFoundException("upload directory not found ({$save_dir})");
        }
        if (!is_writable($save_dir)) {
            throw new SyL_PermissionDeniedException("upload directory permission denied ({$save_dir})");
        }

        if (!preg_match('/(\/|\\\\)$/', $save_dir)) {
            $save_dir .= DIRECTORY_SEPARATOR;
        }

        $save_file = $save_dir . $save_file;
        if (!move_uploaded_file($this->tmp_name, $save_file)) {
            throw new SyL_HttpFileUploadException("`move_uploaded_file' failed. ({$save_file})", 9999);
        }
    }

    /**
     * エラーメッセージを取得する
     * 
     * @param string エラーNo
     * @param string アップロード要素名
     * @param mixed アップロードインデックス
     * @return string エラーメッセージ
     */
    private static function getErrorMessage($errorno, $name, $index)
    {
        if ($index !== null) {
            $name .= '[' . $index . ']';
        }
        switch ($errorno) {
        case UPLOAD_ERR_INI_SIZE:   return "`UPLOAD_ERR_INI_SIZE' error. upload size is more than php.ini file ({$name})"; break;
        case UPLOAD_ERR_FORM_SIZE:  return "`UPLOAD_ERR_FORM_SIZE' error. Upload size is more than html form init size ({$name})"; break;
        case UPLOAD_ERR_PARTIAL:    return "`UPLOAD_ERR_PARTIAL' error. Upload file can not upload a part of upload file ({$name})"; break;
        case UPLOAD_ERR_NO_FILE:    return "`UPLOAD_ERR_NO_FILE' error. Upload file is not exist ({$name})"; break;
        case UPLOAD_ERR_NO_TMP_DIR: return "`UPLOAD_ERR_NO_TMP_DIR' error. TMP_DIR undefined ({$name})"; break;
        case UPLOAD_ERR_CANT_WRITE: return "`UPLOAD_ERR_CANT_WRITE' error. failed write disk ({$name})"; break;
        case UPLOAD_ERR_EXTENSION:  return "`UPLOAD_ERR_EXTENSION' error. extended module error ({$name})"; break;
        default:                    return "undefined error. (no {$errorno}) ({$name})"; break;
        }
    }
}