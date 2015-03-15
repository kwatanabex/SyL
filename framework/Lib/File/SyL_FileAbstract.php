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
 * @subpackage SyL.Lib.File
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** ファイル操作例外クラス */
require_once 'SyL_FileException.php';

/**
 * ファイル操作クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.File
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
abstract class SyL_FileAbstract
{
    /**
     * ファイル名
     * 
     * @var string
     */
    protected $resource_name;
    /**
     * ファイルポインタ
     * 
     * @var resource
     */
    protected $fp = null;
    /**
     * ファイルオープンモード
     * 
     * @var string
     */
    protected $mode;
    /**
     * 改行コード
     * 
     * @var string
     */
    protected $eol = PHP_EOL;
    /**
     * スクリプト側のエンコーディング
     * 
     * @var string
     */
    protected $input_encoding = '';
    /**
     * ファイル側のエンコーディング
     * 
     * @var string
     */
    protected $output_encoding = '';
    /**
     * CSVファイルフラグ
     * 
     * @var bool
     */
    protected $csv = false;
    /**
     * CSVファイルの区切り文字
     * 
     * @var string
     */
    protected $csv_delimiter = ',';
    /**
     * CSVファイルの囲い子
     * 
     * @var string
     */
    protected $csv_enclosure = '';

    /**
     * コンストラクタ
     *
     * @param string ファイル名
     */
    public function __construct($resource_name)
    {
        $this->resource_name = $resource_name;
    }

    /**
     * ファイル内容を取得する
     *
     * @param string ファイル名
     * @return string ファイル内容
     * @throws SyL_FileException ファイル取得処理で warning が発生した場合
     */
    public static function readContents($resource_name)
    {
        try {
            return file_get_contents($resource_name);
        } catch (Exception $e) {
            throw new SyL_FileException($e->getMessage());
        }
    }

    /**
     * ファイルに書き込む
     *
     * @param string ファイル名
     * @param string 書き込むファイル内容
     * @throws SyL_FileException ファイル書き込み処理で warning が発生した場合
     */
    public static function writeContents($resource_name, $data)
    {
        try {
            file_put_contents($resource_name, $data, LOCK_EX);
        } catch (Exception $e) {
            throw new SyL_FileException($e->getMessage());
        }
    }

    /**
     * データをファイルに追記で書き込む
     *
     * @param string ファイル名
     * @param string 書き込むデータ内容
     * @throws SyL_FileException ファイル書き込み処理で warning が発生した場合
     */
    public static function appendContents($resource_name, $data)
    {
        try {
            file_put_contents($resource_name, $data, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            throw new SyL_FileException($e->getMessage());
        }
    }

    /**
     * ファイルを開く
     *
     * エンコーディング変換フィルタを適用する場合は、
     * 事前にエンコーディングをセットする。
     *
     * @throws SyL_FileException ファイルを開く処理で warning が発生した場合
     */
    public function open()
    {
        try {
            $this->fp = fopen($this->resource_name, $this->mode);
        } catch (Exception $e) {
            throw new SyL_FileException($e->getMessage());
        }

        if ($this->output_encoding) {
            include_once dirname(__FILE__) . '/../StreamFilter/SyL_StreamFilterFunction.php';

            $parameters = array('mb_convert_encoding');
            $parameters[] = $this->output_encoding;
            if ($this->input_encoding) {
                $parameters[] = $this->input_encoding;
            }
            stream_filter_append($this->fp, 'SyL.Lib.StreamFilter.Function', STREAM_FILTER_ALL, $parameters);
        }
    }

    /**
     * ファイルを閉じる
     */
    public function close()
    {
        if (is_resource($this->fp)) {
            fclose($this->fp);
        }
        $this->fp = null;
    }

    /**
     * CSVファイルを使用する
     *
     * @param string 区切り文字
     * @param string 囲い子
     */
    public function useCsv($delimiter=',', $enclosure='')
    {
        $this->csv = true;
        $this->csv_delimiter = $delimiter;
        $this->csv_enclosure = $enclosure;
    }

    /**
     * ファイルサイズを取得する
     *
     * @return int ファイルサイズ
     * @throws SyL_FileNotFoundException ファイルが存在しない場合
     */
    public function getSize()
    {
        if (is_file($this->resource_name)) {
            return filesize($this->resource_name);
        }
        throw new SyL_FileNotFoundException("file not found ({$this->resource_name})");
    }

    /**
     * ファイルに関する情報を取得する
     *
     * @return array ファイルに関する情報
     * @throws SyL_FileNotFoundException ファイルが存在しない場合
     */
    public function getStat()
    {
        clearstatcache();
        if (is_resource($this->fp)) {
            return fstat($this->fp);
        } else if (is_file($this->resource_name)) {
            return stat($this->resource_name);
        } else {
            throw new SyL_FileNotFoundException("file not found ({$this->resource_name})");
        }
    }

    /**
     * ファイルのパーミッションを取得する
     *
     * @return string パーミッション (8進数 例: 0644)
     * @throws SyL_FileNotFoundException ファイルが存在しない場合
     */
    public function getPermission()
    {
        clearstatcache();
        if (is_file($this->resource_name)) {
            return substr(sprintf('%o', fileperms($this->resource_name)), -4);
        }
        throw new SyL_FileNotFoundException("file not found ({$this->resource_name})");
    }

    /**
     * ファイルのパーミッションをセットする
     *
     * @param int パーミッション (8進数 例: 0666)
     * @throws SyL_InvalidParameterException パラメータが int でない場合
     * @throws SyL_FileNotFoundException ファイルが存在しない場合
     */
    public function changePermission($permission)
    {
        if (!is_int($permission)) {
            throw new SyL_InvalidParameterException("invalid permission parameter. not int ({$permission})");
        }
        if (is_file($this->resource_name)) {
            chmod($this->resource_name, $permission);
        } else {
            throw new SyL_FileNotFoundException("file not found ({$this->resource_name})");
        }
    }

    /**
     * 使用する改行をセットする
     *
     * @param string 改行
     */
    public function setEol($eol)
    {
        $this->eol = $eol;
    }

    /**
     * 入力エンコーディングをセットする
     *
     * ※ファイルをオープンする前にセットする
     *
     * @param string 入力エンコーディング
     */
    public function setInputEncoding($input_encoding)
    {
        $this->input_encoding = $input_encoding;
    }

    /**
     * 出力エンコーディングをセットする
     *
     * ※ファイルをオープンする前にセットする
     *
     * @param string 出力エンコーディング
     */
    public function setOutputEncoding($output_encoding)
    {
        $this->output_encoding = $output_encoding;
    }

    /**
     * バッファを設定する
     *
     * @param int バッファサイズ
     * @throws SyL_FileException 有効なファイルポインタが無い場合
     */
    public function setWriteBuffer($buffer)
    {
        if (is_resource($this->fp)) {
            stream_set_write_buffer($this->fp, $buffer);
        } else {
            throw new SyL_FileException("`setFileBuffer' method failed. use after `open' method");
        }
    }

    /**
     * マスク値を設定する
     *
     * @param int マスク値
     */
    public function setUmask($umask=0000)
    {
        if (!is_int($umask)) {
            throw new SyL_InvalidParameterException("invalid umask parameter. not int ({$umask})");
        }
        umask($umask);
    }
}
