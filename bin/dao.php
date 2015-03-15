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
 * @package   SyL.Core
 * @author    Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2011 k.watanabe
 * @license   http://www.opensource.org/licenses/lgpl-license.php
 * @version   CVS: $Id: $
 * @link      http://syl.jp/
 * -----------------------------------------------------------------------------
 */

error_reporting(E_ALL|E_STRICT);

if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Tokyo');
}

$syl_dir = dirname(dirname(__FILE__));

require_once $syl_dir . '/framework/SyL.php';

$config = array(
  'type'        => 'cmd',
  'action_key'  => 'action',
  'project_dir' => $syl_dir . '/opt/dao',
  'app_name'    => 'dao',
  'cache'       => false,
  'log'         => SYL_LOG_NONE
);

SyL_EventDispatcher::startup($config)->run();
