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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * CRUD 一覧ページクラス
 * 
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Crud
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id$
 * @link       http://syl.jp/
 */
class SyL_CrudPageLst extends SyL_CrudPageAbstract
{
    /**
     * 一覧表示情報を取得する
     *
     * @param int ページ数
     * @param array ソートカラム
     * @param int 1ページの件数
     * @param string CRUDタイプ
     * @return array 一覧表示情報
     */
    public function getList($page_count, $sorts=array(), $row_count=0, $crud_type=SyL_CrudConfigAbstract::CRUD_TYPE_LST)
    {
        // ソート
        if (count($sorts) == 0) {
            $sorts = $this->config->getDefaultSort();
        }
        // 件数
        $select_rows = $this->config->getSelectRowCount();
        if (!in_array($row_count, $select_rows)) {
            $row_count = $this->config->getDefaultRowCount();
        }

        $parameters = array();

        // プライマリパラメータ情報
        foreach ($this->getId() as $name => $value) {
            $parameters[$name] = array($value, false);
        }

        // バリデーションチェック用レコード
        $record = $this->config->getAccess()->createRecord(true);

        // 検索情報
        foreach ($this->getFormInstance(SyL_CrudConfigAbstract::CRUD_TYPE_SCH)->getElements() as $name => $element) {
            $value = $element->getValue();
            if (is_array($value)) {
                if (count($value) > 0) {
                    $parameters[$name] = array($value, false);
                }
            } else {
                if (($value !== null) && ($value !== '')) {
                    $text_flag = (($element instanceof SyL_FormElementText) || ($element instanceof SyL_FormElementTextarea));
                    $parameters[$name] = array($value, $text_flag);
                }
            }
            if (isset($parameters[$name])) {
                // バリデーションチェック用
                $record->{$name} = $parameters[$name][0];
            }
        }

        // エクスポートフラグ
        $export_flag = ($crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_EXP);

        list($headers, $dbrows, $pager) = $this->config->getAccess()->getList($page_count, $sorts, $parameters, $row_count, $export_flag);

        // オプション項目名の変換
        $options = array();
        foreach ($this->config->getElements() as $name => $element) {
            $tmp_options = $element->getOptions();
            if (count($tmp_options) > 0) {
                foreach ($tmp_options as $name1 => $value1) {
                    if (($value1 === null) || ($value1 === '')) {
                        unset($tmp_options[$name1]);
                    }
                }
                $options[$name] = $tmp_options;
            }
        }

        // 項目表示文字数
        $item_max_length = 0;
        if ($crud_type == SyL_CrudConfigAbstract::CRUD_TYPE_LST) {
            $item_max_length = $this->config->getItemMaxLength();
        }

        $rows = array();
        foreach ($dbrows as &$record) {
            $row = array();
            $primary = array();
            foreach ($record as $name => $value) {
                if ($headers[$name]['primary']) {
                    // 主キー情報
                    $primary[$name] = $value;
                }

                if (isset($options[$name])) {
                    // オプション名の変換
                    $tmp = array_search($value, $options[$name]);
                    if ($tmp !== false) {
                        $value = $tmp;
                    }
                }

                if ($item_max_length > 0) {
                    // 一覧表示の文字数制限
                    if (mb_strlen($value, self::$internal_encoding) > $item_max_length) {
                        $value = mb_substr($value, 0, $item_max_length, self::$internal_encoding) . '...';
                    }
                }
                $row[$name] = $value;
            }
            $row['__PRIMARY'] = self::encodeId($primary);
            $rows[] = $row;
        }

        $page_info = array();
        $page_info['title'] = $this->getName();
        if ($pager) {
            $page_info['select_rows'] = $select_rows;
            $page_info['row_count'] = $pager->getPageCount();
            $page_info['row_max'] = $pager->getSum();
            $page_info['page_current'] = $pager->getCurrentPage();
            $page_info['page_max'] = $pager->getTotalPage();

            $link_range = $this->config->getLinkRange();
            $range = floor($link_range / 2);
            $page_info['range'] = $pager->getRange($range);

            $page_info['background_row_color'] = $this->config->getBackgroundRowColor();
        }

        if ($this->config->enableCrud(SyL_CrudConfigAbstract::CRUD_TYPE_DEL)) {
            // 一覧URLは、削除用に使用
            try {
                $page_info['url_lst'] = $this->config->getUrlLst();
            } catch (SyL_CrudInvalidConfigException $e) {
                $page_info['url_vew'] = null;
            }
        } else {
            $page_info['url_lst'] = null;
        }
        try {
            $page_info['url_vew'] = $this->config->getUrlVew();
        } catch (SyL_CrudInvalidConfigException $e) {
            $page_info['url_vew'] = null;
        }
        try {
            $page_info['url_edt'] = $this->config->getUrlEdt();
        } catch (SyL_CrudInvalidConfigException $e) {
            $page_info['url_edt'] = null;
        }

        return array($headers, $rows, $page_info);
    }

    /**
     * ファイルを出力する
     *
     * @param array ソートカラム
     * @param bool ヘッダ出力フラグ
     * @param string 出力文字コード
     */
    public function export($sorts, $header_flag, $charset)
    {
        list($headers, $rows, $page_info) = $this->getList(null, $sorts, 0, SyL_CrudConfigAbstract::CRUD_TYPE_EXP);

        $filename = self::getExportFileName(false);

        set_time_limit(0);
        while (ob_get_level()) {
            ob_end_clean();
        }
        ignore_user_abort(false);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Connection: close');

        $convert_string = array_fill(0, count($headers), $charset);
        $eol = "\n";

        if ($header_flag) {
            $headers_out = array();
            foreach ($headers as $herader) {
                $headers_out[] = $herader['name'];
            }
            $headers_out = array_map(array(__CLASS__, 'convertCsvArray'), $headers_out, $convert_string);
            echo implode(',', $headers_out) . $eol;
            flush();
        }

        foreach ($rows as $row) {
            $row_out = array();
            foreach ($row as $name => $value) {
                if (substr($name, 0, 2) == '__') {
                    continue;
                }
                $row_out[] = $value;
            }
            $row_out = array_map(array(__CLASS__, 'convertCsvArray'), $row_out, $convert_string);
            echo implode(',', $row_out) . $eol;
            flush();
        }
    }

    /**
     * CSVファイルの項目に変換する
     *
     * @param string 出力項目
     * @param string 変換文字コード
     * @return string CSVファイルの項目
     */
    private static function convertCsvArray($value, $charset)
    {
        $value = str_replace('"', '""', $value);
        $value = '"' . $value . '"';
        if ($charset) {
            $value = mb_convert_encoding($value, $charset, self::$internal_encoding);
        }
        return $value;
    }

    /**
     * エクスポートファイル名を取得する
     *
     * @param bool zipファイルフラグ
     * @return string CSVファイルの項目
     */
    private function getExportFileName($zip_flag)
    {
        $filename = $this->getName() . '_' . date('YmdHis') . '.csv';
        if ($zip_flag) {
            $filename .= '.zip';
        }
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($ua, 'MSIE') !== false) {
            $filename = urlencode($filename);
        }
        return $filename;
    }

    /**
     * ファイルポインタからファイルを出力する
     *
     * @param array ソート項目
     * @param bool ヘッダ出力フラグ
     * @param string 出力文字コード
     * @param bool zipファイルフラグ
     */
    public function exportStream(array $sorts, $header_flag, $charset, $zip_flag)
    {
        if (count($sorts) == 0) {
            $sorts = $this->config->getDefaultSort();
        }

        // 検索情報
        $parameters = array();
        foreach ($this->getFormInstance(SyL_CrudConfigAbstract::CRUD_TYPE_EXP)->getElements() as $name => $element) {
            $value = $element->getValue();
            if (is_array($value)) {
                if (count($value) > 0) {
                    $parameters[$name] = array($value, false);
                }
            } else {
                if (($value !== null) && ($value !== '')) {
                    if ($element instanceof SyL_FormElementText) {
                        $parameters[$name] = array($value, true);
                    } else {
                        $parameters[$name] = array($value, false);
                    }
                }
            }
        }

        set_time_limit(0);

        // 一時ファイル
        $temp_file = tempnam(sys_get_temp_dir(), 'bd_crud_export_');
        // 一時ファイル削除用
        register_shutdown_function(create_function('', 'if (file_exists("' . $temp_file . '")) { unlink("' . $temp_file . '"); }'));

        $stream = fopen($temp_file, 'wb+');
        stream_set_write_buffer($stream, 0);
        if ($charset) {
            // 関数適用ストリームフィルタクラス
            include_once dirname(__FILE__) . '/../StreamFilter/SyL_StreamFilterFunction.php';
            $fltr = stream_filter_append($stream, 'SyL.Lib.StreamFilter.Function.mb_convert_encoding?' . $charset . '&' . self::$internal_encoding, STREAM_FILTER_WRITE, -1);
            $this->config->getAccess()->writeListCsv($stream, $sorts, $parameters);
            stream_filter_remove($fltr);
        } else {
            $this->config->getAccess()->writeListCsv($stream, $sorts, $parameters);
        }

        $content_type = 'application/octet-stream';
        if ($zip_flag) {
            fclose($stream);
            $stream = null;

            $temp_file_zip = $temp_file . '.zip';
            // 一時ファイル削除用
            register_shutdown_function(create_function('', 'if (file_exists("' . $temp_file_zip . '")) { unlink("' . $temp_file_zip . '"); }'));

            $zip = new PharData($temp_file_zip, 0, null, Phar::ZIP);
            $zip->addFile($temp_file, 'export.csv');
            $zip->compressFiles(Phar::GZ);

            $stream = fopen($temp_file_zip, 'rb+');
            $content_type = 'application/zip';
        }

        $filename = self::getExportFileName($zip_flag);
        $stat = fstat($stream);
        $size = $stat['size'];

        while (ob_get_level()) {
            ob_end_clean();
        }
        ignore_user_abort(false);

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $size);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Connection: close');

        rewind($stream);
        if (!$header_flag) {
            fgets($stream);
        }
        while (!feof($stream)) {
            echo fread($stream, 8192);
            flush();
        }
        fclose($stream);
        $stream = null;
    }

    /**
     * CSVファイルをインポートする
     *
     * @param string CSVファイル名
     * @param bool ヘッダ含むフラグ
     * @param string 入力文字コード
     */
    public function import($filename, $header_include_flag, $charset)
    {
        $form_view = $this->getImportFormView();
        if (!$form_view) {
            return;
        }

        $names = $form_view->getElementNames();
        $count = count($names);

        set_time_limit(0);
        while (ob_get_level()) {
          ob_end_clean();
        }
        ignore_user_abort(true);

        // ロケール設定
        $current_locale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, self::$internal_locale);

        $fp = fopen($filename, 'rb');
        $fltr = null;
        if ($charset) {
            // 関数適用ストリームフィルタクラス
            include_once dirname(__FILE__) . '/../StreamFilter/SyL_StreamFilterFunction.php';
            stream_filter_append($fp, 'SyL.Lib.StreamFilter.Function.mb_convert_encoding?' . self::$internal_encoding . '&' . $charset, STREAM_FILTER_READ);
        }

        $access = $this->config->getAccess();

        // ヘッダ含む判定
        $default_values = array();
        if ($header_include_flag) {
            $record = $access->createRecord();
            $items = fgetcsv($fp);
            foreach ($items as $item) {
                if (!$record->is($item)) {
                    fclose($fp);
                    setlocale(LC_ALL, $current_locale);
                    throw new SyL_CrudValidateException(array("invalid header item ({$item})"));
                }
            }
            $names = $items;
            $count = count($names);

            // CRUD設定のデフォルト値
            foreach ($this->config->getElements() as $name => $element) {
                if (in_array($name, $names)) {
                    // カラムが指定されてる場合は、デフォルト値を使用しない
                    continue;
                }
                $default_value = $element->getDefaultValue();
                if ($default_value !== null) {
                    $default_values[$name] = $default_value;
                }
            }
        }

        $line = 0;
        $error_messages = array();

        // エラー状況チェック
        $check_lines  = 100;
        $check_errors = 10;

        $access->beginTransaction();
        try {
            while (($items = fgetcsv($fp)) !== false) {
                $line++;

                // 要素数チェック
                if (count($items) == $count) {
                    try {
                        $record = $access->createRecord($header_include_flag);
                        foreach ($items as $i => $item) {
                            $record->{$names[$i]} = $item;
                        }
                        foreach ($default_values as $name => $default_value) {
                            $record->{$name} = $default_value;
                        }
                        $access->validate($record);
                        $access->insert($record);
                    } catch (SyL_DbDaoValidateException $e) {
                        foreach ($e->getMessages() as $message) {
                            $error_messages[] = 'line.' . $line . ' : ' . $message;
                        }
                    }
                } else {
                    $error_messages[] = 'line.' . $line . ' : csv item count not match (expected:[' . $count . '] actual:[' . count($items) . '])';
                }

                if (connection_status() != CONNECTION_NORMAL) {
                    // リクエストが切断された場合
                    throw new SyL_InvalidOperationException('client request disconnected');
                }

                if (count($error_messages) >= $check_errors) {
                    // 一定の件数でエラー状況をチェック
                    throw new SyL_CrudValidateException($error_messages);
                }

                if (($line % $check_lines) == 0) {
                    if (count($error_messages) > 0) {
                        // 一定の件数でエラー状況をチェック
                        throw new SyL_CrudValidateException($error_messages);
                    }
                    // ignore_user_abort用の出力
                    echo "\n";
                    flush();
                }
            }
        } catch (Exception $e) {
            fclose($fp);
            setlocale(LC_ALL, $current_locale);
            $access->rollBack();
            throw $e;
        }
        setlocale(LC_ALL, $current_locale);
        fclose($fp);

        if (count($error_messages) > 0) {
            $access->rollBack();
            throw new SyL_CrudValidateException($error_messages);
        }

        $access->commit();
    }

    /**
     * フォームオブジェクトを取得する
     *
     * @param string CRUDタイプ
     * @return SyL_CrudForm フォームオブジェクト
     */
    private function getFormInstance($crud_type)
    {
        static $forms = array();
        if (!isset($forms[$crud_type])) {
            $forms[$crud_type] = $this->config->createForm($crud_type);
        }
        return $forms[$crud_type];
    }

    /**
     * 検索用フォーム表示オブジェクトの配列を取得する
     *
     * @return array フォーム表示オブジェクトの配列
     */
    public function getSearchFormView()
    {
        return $this->getFormInstance(SyL_CrudConfigAbstract::CRUD_TYPE_SCH)->getView();
    }

    /**
     * インポート用フォーム表示オブジェクトの配列を取得する
     *
     * @return array フォーム表示オブジェクトの配列
     */
    public function getImportFormView()
    {
        if ($this->config->enableCrud(SyL_CrudConfigAbstract::CRUD_TYPE_IMP)) {
            return $this->getFormInstance(SyL_CrudConfigAbstract::CRUD_TYPE_IMP)->getView();
        }
        return null;
    }

    /**
     * レコード情報を削除する
     */
    public function deleteRecord()
    {
        $this->config->getAccess()->delete(array_values($this->getId()));
    }
}

