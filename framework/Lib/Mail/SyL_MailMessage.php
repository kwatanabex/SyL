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
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * メールメッセージクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_MailMessage
{
    /**
     * 送信元
     * 
     * @var string
     */
    private $from = array();
    /**
     * 返信先メールアドレス
     * 
     * @var string
     */
    private $reply_to = '';
    /**
     * 送信先
     * 
     * @var array
     */
    private $to = array();
    /**
     * CCメールアドレス
     * 
     * @var array
     */
    private $cc = array();
    /**
     * BCCメールアドレス
     * 
     * @var array
     */
    private $bcc = array();
    /**
     * 件名
     * 
     * @var string
     */
    private $subject = '';
    /**
     * MIMEバージョン
     * 
     * @var string
     */
    private $mime_version = '1.0';
    /**
     * 本文
     *
     * [body]              - 本文
     * [name]              - 添付ファイル名
     * [file]              - 添付ファイル
     * [type]              - Content-Type
     * [transfer_encoding] - Content-Transfer-Encoding
     * 
     * @var array
     */
    private $attachments = array();
    /**
     * エンコード方式
     *
     * ※現在Base64のみ対応
     * 
     * B: base64
     * Q: Quoted-Printable
     * 
     * @var string
     */
    private $transfer_encoding = 'B';
    /**
     * X-Mailerヘッダー
     * 
     * @var string
     */
    private $x_mailer = 'SyL Framework Mail Sender/1.0';
    /**
     * その他ヘッダ
     * 
     * @var array
     */
    private $headers = array();
    /**
     * メール内容のエンコード
     * 
     * @var string
     */
    private $mail_encoding = 'ISO-2022-JP';
    /**
     * プログラム側のエンコード
     * 
     * @var string
     */
    private $internal_encoding = null;
    /**
     * 言語
     * 
     * @var string
     */
    private $language = null;
    /**
     * 改行
     * 
     * @var string
     */
    private $eol = "\r\n";

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->language = mb_language();
        $this->internal_encoding = mb_internal_encoding();
    }

    /**
     * 送信元アドレスをセット
     *
     * @param string 送信元アドレス
     * @param string 送信元名
     */
    public function setFrom($from, $name='')
    {
        $this->from = array($from, $name);
        if ($this->reply_to == '') {
            $this->reply_to = $from;
        }
    }

    /**
     * 送信元アドレスを取得
     *
     * @return array 送信元アドレス
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * 送信先アドレスをセット
     * 
     * @param mixed 送信先アドレス
     * @param string 送信先名
     */
    public function addTo($to, $name='')
    {
        $this->to[] = array($to, $name);
    }

    /**
     * 送信先アドレスを取得
     *
     * @return array 送信元アドレス
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * 送信先アドレス(CC)をセット
     * 
     * @param mixed 送信先アドレス(CC)
     * @param string 送信先名(CC)
     */
    public function addCc($cc, $name='')
    {
        $this->cc[] = array($cc, $name);
    }

    /**
     * 送信先アドレス(CC)を取得
     *
     * @return array 送信元アドレス(CC)
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * 送信先アドレス(BCC)をセット
     * 
     * @param mixed 送信先アドレス(BCC)
     * @param string 送信先名(BCC)
     */
    public function addBcc($bcc, $name='')
    {
        $this->bcc[] = array($bcc, $name);
    }

    /**
     * 送信先アドレス(BCC)を取得
     *
     * @return array 送信元アドレス(BCC)
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * 件名をセット
     * 
     * @param string 件名
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * 件名を取得
     * 
     * @return string 件名
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * 本文をセット
     * 
     * @param string 本文
     */
    public function setBody($body)
    {
        $this->attachments[] = array(
          'body'              => $body,
          'type'              => 'text/plain; charset="' . $this->mail_encoding . '"',
          'transfer_encoding' => '7bit',
          'name'              => null,
          'file'              => null
        );
    }

    /**
     * 本文を取得
     * 
     * @param string 本文
     */
    public function getBody()
    {
        return $this->attachments;
    }

    /**
     * メールエンコードをセットする
     *
     * @param string メールエンコード
     */
    public function setMailEncoding($mail_encoding)
    {
        $this->mail_encoding = $mail_encoding;
    }

    /**
     * 内部パラメータエンコードをセットする
     *
     * @param string 内部パラメータエンコード
     */
    public function setInternalEncoding($internal_encoding)
    {
        $this->internal_encoding = $internal_encoding;
    }

    /**
     * 言語をセットする
     *
     * @param string 言語
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * その他のメールヘッダをセット
     * 
     * @param string ヘッダのキー
     * @param string ヘッダの値
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * その他のメールヘッダを取得
     * 
     * @return array その他のメールヘッダ
     */
    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * 改行をセットする
     * 
     * @param string 改行
     */
    public function setEol($eol)
    {
        $this->eol = $eol;
    }

    /**
     * 添付ファイルをセット
     * 
     * @param string ファイルパス（ファイル名まで含む）
     * @param string コンテンツタイプ
     */
    public function addFile($path, $content_type='application/octet-stream')
    {
        // ファイル名取得
        $name = basename($path);
        // 添付ファイル取得
        $contents = file_get_contents($path);
        switch ($this->transfer_encoding) {
        case 'B':
            $contents = chunk_split(base64_encode($contents));
            $transfer_encoding = 'Base64';
            break;
        default:
            throw new SyL_MailParameterException("Content-Transfer-Encoding not supported ({$this->transfer_encoding})");
            break;
        }

        // 添付ファイル情報セット
        $this->attachments[] = array(
          'body'              => null,
          'type'              => $content_type,
          'transfer_encoding' => $transfer_encoding,
          'name'              => $this->convertEncoding($name, true),
          'file'              => $contents
        );
    }

    /**
     * RCPT_TO用メールアドレスリストを取得する
     * 
     * @return array RCPT_TO用メールアドレスリスト
     */
    public function getRcptTo()
    {
        $rcptto = array();
        foreach ($this->to as $to) {
            if (array_search($to[0], $rcptto) === false) {
                $rcptto[] = $to[0];
            }
        }
        foreach ($this->cc as $cc) {
            if (array_search($cc[0], $rcptto) === false) {
                $rcptto[] = $cc[0];
            }
        }
        foreach ($this->bcc as $bcc) {
            if (array_search($bcc[0], $rcptto) === false) {
                $rcptto[] = $bcc[0];
            }
        }
        return $rcptto;
    }

    /**
     * パラメータをメール送信用文字列にエンコードする
     *
     * @param string エンコード対象文字列
     * @param bool MIMEエンコードフラグ
     * @return エンコード後文字列
     */
    public function convertEncoding($parameter, $mime_encode=false)
    {
        $parameter = mb_convert_encoding(mb_convert_kana($parameter, 'KV', $this->internal_encoding), $this->mail_encoding, $this->internal_encoding);
        if ($mime_encode) {
            $language = mb_language();
            mb_language($this->language);
            $internal_encoding = mb_internal_encoding();
            mb_internal_encoding($this->mail_encoding);
            $parameter = mb_encode_mimeheader($parameter, $this->mail_encoding, $this->transfer_encoding, $this->eol);
            mb_internal_encoding($internal_encoding);
            mb_language($language);
        }
        return $parameter;
    }

    /**
     * メールアドレスをメール送信用文字列にエンコーディング
     * 
     * @param string メールアドレス
     * @param string メールアドレスの名前
     * @return string エンコーディングのメールアドレス
     */
    public function convertEncodingAddress($address, $name='')
    {
        if ($name) {
            return '"' . $this->convertEncoding($name, true) . '" <' . $address . '>';
        } else {
            return $address;
        }
    }

    /**
     * メール送信用文字列をパラメータにデコードする
     *
     * @param string デコード対象文字列
     * @param bool MIMEエンコードフラグ
     * @return デコード後文字列
     */
    public function convertDecoding($parameter, $mime_encode=false)
    {
        if ($mime_encode) {
            $language = mb_language();
            mb_language($this->language);
            $internal_encoding = mb_internal_encoding();
            mb_internal_encoding($this->mail_encoding);
            $parameter = mb_decode_mimeheader($parameter);
            mb_internal_encoding($internal_encoding);
            mb_language($language);
        }
        return mb_convert_encoding($parameter, $this->internal_encoding, $this->mail_encoding);
    }

    /**
     * メール送信用文字列をメールアドレスにデコーディング
     * 
     * @param string メールアドレス
     * @return array メールアドレスとメールアドレスの名前の配列
     */
    public function convertDecodingAddress($address)
    {
        if (preg_match('/^(.*)[ ]*<(.+@.+)>$/', $address, $matches)) {
            switch (count($matches)) {
            case 3:
                $matches[1] = trim($matches[1]);
                if (preg_match('/^"(.+)"$/', $matches[1], $matches1)) {
                    $matches[1] = $matches1[1];
                }
                return array($matches[2], $this->convertDecoding(trim($matches[1]), true));
            case 2: return array($matches[2], '');
            }
        } else {
            return array($address, '');
        }
    }

    /**
     * メールメッセージを取得
     *
     * @param bool メール関数用のメッセージ
     * @param bool SMTP用本文エスケープ
     * @return string メールヘッダを含む全文取得
     */
    public function getMessage($mail_function=false, $body_escape=false)
    {
        // From必須
        if (count($this->from) != 2) {
            throw new SyL_MailParameterException('From Address not found');
        }
        // To必須
        if (count($this->to) == 0) {
            throw new SyL_MailParameterException('To Address not found');
        }

        // メールヘッダ
        $data  = '';
        $data .= "From: " . $this->convertEncodingAddress($this->from[0], $this->from[1]) . $this->eol;
        if (!$mail_function) {
            $data .= "Subject: "  . $this->convertEncoding($this->subject, true) . $this->eol;
            for ($i=0; $i<count($this->to); $i++) {
                $data .= "To: " . $this->convertEncodingAddress($this->to[$i][0], $this->to[$i][1]) . $this->eol;
            }
        }
        for ($i=0; $i<count($this->cc); $i++) {
            $data .= "Cc: " . $this->convertEncodingAddress($this->cc[$i][0], $this->cc[$i][1]) . $this->eol;
        }
        for ($i=0; $i<count($this->bcc); $i++) {
            $data .= "Bcc: " . $this->convertEncodingAddress($this->bcc[$i][0], $this->bcc[$i][1]) . $this->eol;
        }
        $data .= "Reply-To: " . $this->reply_to . $this->eol;
        $data .= "X-Mailer: " . $this->x_mailer . $this->eol;
        $data .= "MIME-Version: " . $this->mime_version . $this->eol;
        foreach ($this->headers as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $data .= "{$name}: {$value}" . $this->eol;
        }

        switch (count($this->attachments)) {
        case 0:
            break;
        case 1:
            $data .= $this->getAttachmentData($this->attachments[0], $body_escape);
            break;
        default:
            // バウンダリ
            $boundary = '--' . md5(uniqid(rand()));

            $data .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"" . $this->eol;
            $data .= $this->eol;
            for ($i=0; $i<count($this->attachments); $i++) {
                $data .= '--' . $boundary . $this->eol;
                $data .= $this->getAttachmentData($this->attachments[$i], $body_escape);
            }
            $data .= '--' . $boundary . "--" . $this->eol;
        }
        return $data;
    }

    /**
     * メールメッセージをセット
     *
     * @param string メールメッセージ
     */
    public function setMessage($data)
    {
        $datas = explode($this->eol . $this->eol, $data, 2);
        // ヘッダを配列に変換
        $headers = $this->getHeaders($datas[0]);

        // メールエンコーディング取得
        if (isset($headers['content-type'])) {
            if (preg_match('/charset="?([^"]+)"?/is', $headers['content-type'][0], $matches)) {
                $this->mail_encoding = trim($matches[1]);
            }
        }

        // メールヘッダ取得
        foreach ($headers as $name => $header) {
            switch ($name) {
            case 'from':     $this->from     = $this->convertDecodingAddress($header[0]); break;
            case 'to':       $this->to[]     = $this->convertDecodingAddress($header[0]); break;
            case 'reply-to': $this->reply_to = $this->convertDecodingAddress($header[0]); break;
            case 'subject':  $this->subject  = $this->convertDecoding($header[0], true);  break;
            case 'date':       $this->headers['Date']       = $header[0]; break;
            case 'message-id': $this->headers['Message-ID'] = $header[0]; break;
            case 'mime-version': $this->mime_version = $header[0]; break;
            case 'x-mailer':     $this->x_mailer     = $header[0]; break;
            case 'content-transfer-encoding':
                switch ($header[0]) {
                case 'base64':           $this->transfer_encoding = 'B'; break;
                case 'quoted-printable': $this->transfer_encoding = 'Q'; break;
                }
                break;
            }
        }

        // メッセージ取得
        if (isset($datas[1])) {
            if (!isset($headers['content-type']) || (strpos($headers['content-type'][0], 'text/') !== false)) {
                // テキストパート
                $this->attachments[] = array(
                  'body'              => $datas[1],
                  'type'              => $headers['content-type'][0],
                  'transfer_encoding' => isset($headers['content-transfer-encoding'][0]) ? $headers['content-transfer-encoding'][0] : ''
                );
            } else if (strpos($headers['content-type'][0], 'multipart/') !== false) {
                // マルチパート
                if (preg_match('/boundary="?([^"]+)"? ?/', $headers['content-type'][0], $matches)) {
                    $boundary = "--{$matches[1]}";
                    // マルチパートの終了以下を削除
                    $pos = strpos($datas[1], "{$this->eol}{$boundary}--{$this->eol}");
                    if ($pos !== false) {
                        $datas[1] = substr($datas[1], 0, $pos);
                    }
                    // マルチパート分割
                    $messages = explode("{$this->eol}{$boundary}{$this->eol}", $datas[1]);

                    // 先頭部分削除
                    array_shift($messages);
                    foreach ($messages as $message) {
                        $messages2 = explode($this->eol . $this->eol, $message, 2);
                        switch (count($messages2)) {
                        case 2:
                            // ヘッダあり
                            $headers_sub = $this->getHeaders($messages2[0]);
                            if (isset($headers_sub['content-type'])) {
                                if (preg_match('/charset="?([^"]+)"?/is', $headers_sub['content-type'][0], $matches)) {
                                    $this->mail_encoding = trim($matches[1]);
                                }
                            }
                            $this->transfer_encoding = '7bit';
                            if (isset($headers_sub['content-transfer-encoding'])) {
                                switch ($headers_sub['content-transfer-encoding'][0]) {
                                case 'base64':
                                    $this->transfer_encoding = 'B';
                                    $messages2[1] = base64_decode($messages2[1]);
                                    break;
                                }
                            }
                            $filename = null;
                            if (isset($headers_sub['content-disposition'])) {
                                if (preg_match('/filename="?([^"]+)"?/is', $headers_sub['content-disposition'][0], $matches)) {
                                    $filename = $this->convertDecoding(trim($matches[1]), true);
                                }
                            }

                            if ($this->transfer_encoding == '7bit') {
                                // テキスト系
                                $this->attachments[] = array(
                                  'body'              => $this->convertDecoding($messages2[1]),
                                  'type'              => isset($headers_sub['content-type'][0]) ? $headers_sub['content-type'][0] : '',
                                  'transfer_encoding' => isset($headers_sub['content-transfer-encoding'][0]) ? $headers_sub['content-transfer-encoding'][0] : '',
                                  'name'              => null,
                                  'file'              => null
                                );
                            } else {
                                // その他添付系
                                $this->attachments[] = array(
                                  'body'              => null,
                                  'type'              => isset($headers_sub['content-type'][0]) ? $headers_sub['content-type'][0] : '',
                                  'transfer_encoding' => isset($headers_sub['content-transfer-encoding'][0]) ? $headers_sub['content-transfer-encoding'][0] : '',
                                  'name'              => $filename,
                                  'file'              => $messages2[1]
                                );
                            }
                            break;
                        case 1:
                            // ヘッダなし。強制的に本文に
                            $this->attachments[] = array(
                              'body'              => $messages2[0],
                              'type'              => null,
                              'transfer_encoding' => null,
                              'name'              => null,
                              'file'              => null
                            );
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * 本文、添付生データを取得する
     *
     * @param array 元データ配列
     * @param bool SMTP用本文エスケープ
     * @return string 本文、添付生データ
     */
    private function getAttachmentData(&$attachment, $body_escape)
    {
        $data  = '';
        $data .= "Content-Type: {$attachment['type']}" . $this->eol;
        $data .= "Content-Transfer-Encoding: {$attachment['transfer_encoding']}" . $this->eol;
        if (isset($attachment['body'])) {
            // テキスト
            $body = $body_escape ? preg_replace('/(\r\n|\n|\r)\.(\r\n|\n|\r)/s', '$1..$2', $attachment['body']) : $attachment['body'];
            $data .= $this->eol;
            $data .= $this->convertEncoding($body) . $this->eol;
        } else {
            // 添付
            $data .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"" . $this->eol;
            $data .= $this->eol;
            $data .= $attachment['file'] . $this->eol;
        }
        return $data;
    }

    /**
     * メールヘッダを配列として取得する
     * ※ヘッダのキーはすべて小文字になる
     *
     * @param string メールヘッダ
     * @return array メールヘッダの配列
     */
    private function getHeaders($header)
    {
        $tmp = '';
        $headers = array();
        foreach (explode($this->eol, $header) as $line) {
            if (preg_match('/^[ |\t]/', $line, $matches)) {
                $tmp .= $this->eol . $line;
            } else {
                if ($tmp) {
                    if (preg_match('/^([^\:]+)\:(.+)$/is', $tmp, $matches)) {
                        $headers[strtolower($matches[1])][] = trim($matches[2]);
                    }
                }
                $tmp = $line;
            }
        }
        if ($tmp) {
            if (preg_match('/^([^\:]+)\:(.+)$/is', $tmp, $matches)) {
                $headers[strtolower($matches[1])][] = trim($matches[2]);
            }
        }
        return $headers;
    }
}
