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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** OAuthシグニチャメソッドクラス */
require_once 'SyL_OAuthSignatureMethodAbstract.php';

/**
 * OAuth HMAC-SHA1シグニチャメソッドクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_OAuthSignatureMethodHMACSHA1 extends SyL_OAuthSignatureMethodAbstract
{
    /**
     * シグニチャメソッド名
     * 
     * @var string
     */
    protected $method_name = 'HMAC-SHA1';

    /**
     * シグニチャを生成する
     * 
     * @return string シグニチャ
     */
    public function create(SyL_OAuthClientRequest $request)
    {
        $consumer = $request->getConsumer();
        $signature_key = SyL_UtilConverter::encodeUrlToRfc3986($consumer->consumer_secret) . '&' . SyL_UtilConverter::encodeUrlToRfc3986($consumer->token_secret);

        $signature_parameter = '';
        $signature_parameters = $request->getParameters();
        uksort($signature_parameters, 'strnatcmp');
        foreach ($signature_parameters as $name => $values) {
            usort($values, 'strnatcmp');
            foreach ($values as $value) {
                if ($signature_parameter) {
                    $signature_parameter .= '&';
                }
                $signature_parameter .= SyL_UtilConverter::encodeUrlToRfc3986($name) . '=' . SyL_UtilConverter::encodeUrlToRfc3986($value);
            }
        }

        $schema = $request->getClient()->isHttps() ? 'https' : 'http';
        $host = $request->getClient()->getHost();
        $path = $request->getPath();

        $signature_base_strings = array();
        $signature_base_strings[] = $request->getMethod();
        $signature_base_strings[] = sprintf('%s://%s%s', $schema, $host, $path);
        $signature_base_strings[] = $signature_parameter;

        $signature_base_string = implode('&', array_map(array('SyL_UtilConverter', 'encodeUrlToRfc3986'), $signature_base_strings));
        return base64_encode(hash_hmac('sha1', $signature_base_string, $signature_key, true));
    }
}
