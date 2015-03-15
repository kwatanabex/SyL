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
 * @package   SyL
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id$
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

if (!extension_loaded('mbstring')) {
    throw new Exception("`mbstring' extension not loaded");
}

ob_start();

/** バージョン定義 */
define('SYL_VERSION', '2.0.0-svn');

/** ベースディレクトリの定義 */
define('SYL_DIR', dirname(__FILE__) . '/..');
define('SYL_FRAMEWORK_DIR', SYL_DIR . '/framework');

/** エンコード */
if (!defined('SYL_ENCODE_INTERNAL')) define('SYL_ENCODE_INTERNAL', 'UTF-8');

/** アプリケーションタイプクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_AppType.php';
/** ロギングクラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Logger/SyL_Logger.php';
/** フレームワーク起動クラス */
require_once SYL_FRAMEWORK_DIR . '/Core/SyL_EventDispatcher.php';
