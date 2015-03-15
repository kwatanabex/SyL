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
 * @subpackage SyL.Lib.Xml
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * XMLパーサークラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Xml
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_XmlParserAbstract
{
    /**
     * XMLデータ／XMLファイルのパス／XML URL
     *
     * @var string
     */
    private $resource = '';
    /**
     * リソースのファイル名からXMLを取得するフラグ
     *
     * @var bool
     */
    private $resource_flag = false;
    /**
     * XML文字コード
     *
     * @var string
     */
    private $input_encoding = 'UTF-8';
    /**
     * データ取得時の文字コード
     *
     * @var string
     */
    private $output_encoding = '';
    /**
     * XMLReaderオブジェクト
     *
     * @var XMLReader
     */
    private $xml_reader = null;
    /**
     * ネームスペース
     *
     * @var array
     */
    private $namespaces = array();

    /**
     * XMLデータをセット
     *
     * @param string XMLデータ
     */
    public function setData($data)
    {
        $this->resource = $data;
        $this->resource_flag = false;
    }

    /**
     * XMLリソースをセット
     *
     * @param string XMLファイル／URL
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        $this->resource_flag = true;
    }

    /**
     * XMLのエンコードをセット
     *
     * @param string エンコード名
     */
    public function setInputEncoding($input_encoding)
    {
        $this->input_encoding = $input_encoding;
    }

    /**
     * クライアントエンコードをセット
     *
     * @param string エンコード名
     */
    public function setOutputEncoding($output_encoding)
    {
        $this->output_encoding = $output_encoding;
    }

    /**
     * XMLReaderオブジェクトを取得する
     */
    public function getXMLReader()
    {
        return $this->xml_reader;
    }

    /**
     * 接頭辞に対応したネームスペースを取得する
     *
     * @param string 接頭辞
     * @return string ネームスペース
     */
    public function getNamespace($prefix)
    {
        return isset($this->namespaces[$prefix]) ? $this->namespaces[$prefix] : null;
    }

    /**
     * XML解析実行
     */
    public function parse()
    {
        $this->xml_reader = new XMLReader(); 
        if ($this->resource_flag) {
            $this->xml_reader->open($this->resource, $this->input_encoding); 
        } else {
            $this->xml_reader->xml($this->resource, $this->input_encoding); 
        }
        try {
            $this->parseRecursive($this->xml_reader);
        } catch (Exception $e) {
            $this->xml_reader->close();
            $this->xml_reader = null;
            throw $e;
        }
        $this->xml_reader->close();
        $this->xml_reader = null;
    }

    /**
     * XML再帰解析処理
     *
     * @param XMLReader XMLReaderオブジェクト
     * @param string XML階層
     * @param array 属性配列
     * @return string テキスト
     */
    private function parseRecursive(XMLReader $xml_reader, $parent_path='', array $attributes=array())
    {
        $text = '';
        $first = true;
        while ($xml_reader->read()) {
            switch ($xml_reader->nodeType) { 
            case XMLReader::END_ELEMENT:
                return $text; 
            case XMLReader::ELEMENT:
                if ($xml_reader->prefix && !isset($namespaces[$xml_reader->prefix])) {
                    $this->namespaces[$xml_reader->prefix] = $xml_reader->lookupNamespace($xml_reader->prefix);
                }
                if ($parent_path && $first) {
                    $this->parseElement($parent_path, $attributes, '');
                    $first = false;
                }

                $name  = $xml_reader->name;
                $current_path = $parent_path . '/' . $name;

                if ($xml_reader->isEmptyElement) {
                    $this->parseElement($current_path, $this->getAttributes($xml_reader), null);
                } else {
                    $attributes = $this->getAttributes($xml_reader);
                    $text = $this->parseRecursive($xml_reader, $current_path, $attributes);
                    if ($text !== null) {
                        $this->parseElement($current_path, $attributes, $text);
                    }
                }
                $text = null;
                break; 
            case XMLReader::TEXT: 
            case XMLReader::CDATA:
                $text .= $xml_reader->value; 
                break;
            }
        }
        return $text;
    }

    /**
     * 要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    private function parseElement($path, array $attributes, $text)
    {
        $this->doElement($path, array_map(array($this, 'convertEncoding'), $attributes), $this->convertEncoding($text));
    }

    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected abstract function doElement($current_path, array $attributes, $text);

    /**
     * 属性を取得
     *
     * @param XMLReader XMLReaderオブジェクト
     * @return array 属性配列
     */
    private function getAttributes(XMLReader $xml_reader)
    {
        $attributes = array();
        while ($xml_reader->moveToNextAttribute()) {
            $attributes[$xml_reader->name] = $xml_reader->value; 
        }
        return $attributes;
    }

    /**
     * 文字コード変換
     *
     * @param string 文字コード変換前文字列
     * @return string 文字コード変換後文字列
     */
    protected function convertEncoding($value)
    {
        if ($this->output_encoding && ($this->output_encoding != $this->input_encoding)) {
            return mb_convert_encoding($value, $this->output_encoding, $this->input_encoding);
        } else {
            return $value;
        }
    }
}
