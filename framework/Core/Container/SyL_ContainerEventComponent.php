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
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** コンポーネントに対する操作定義クラス */
require_once 'SyL_ContainerEventComponentOperation.php';

/**
 * コンテナに格納するコンポーネント定義クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Container
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ContainerEventComponent
{
    /**
     * クラス名
     * 
     * @var string
     */
    private $class = '';
    /**
     * ファイル名
     * 
     * @var string
     */
    private $file = '';
    /**
     * 優先順位
     * 
     * @var float
     */
    private $priority = 9;
    /**
     * インスタンス作成イベント
     * 
     * @var string
     */
    private $event = '';
    /**
     * コンストラクタインジェクションフラグ
     * 
     * @var bool
     */
    private $constructor = false;
    /**
     * ファイルが存在しないと例外を発生させるフラグ
     * 
     * @var bool
     */
    private $force = '';
    /**
     * 実行メソッドの配列
     * 
     * @var array
     */
    private $operations = array();
    /**
     * カレント実行メソッドのイベント
     * 
     * @var string
     */
    private $current_operation_event = '';
    /**
     * カレント実行メソッドのインデックス
     * 
     * @var int
     */
    private $current_operation_index = 0;

    /**
     * コンストラクタ
     *
     * @param string クラス名
     * @param string ファイル名
     * @param float 優先順位
     * @param string インスタンス作成イベント
     * @param bool コンストラクタインジェクションフラグ
     * @param bool ファイルが存在しないと例外を発生させるフラグ
     */
    public function __construct($class, $file, $priority, $event, $constructor, $force)
    {
        $this->class       = $class;
        $this->file        = $file;
        $this->priority    = $priority;
        $this->event       = $event;
        $this->constructor = $constructor;
        $this->force       = $force;
        // インスタンス作成メソッド
        $this->operations[$this->event][] = new SyL_ContainerEventComponentOperation('constructor', null, false);
        $this->current_operation_event = $event;
        $this->current_operation_index = 0;
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        switch ($name) {
        case 'class':       return $this->class;
        case 'file':        return $this->file;
        case 'priority':    return $this->priority;
        case 'constructor': return $this->constructor;
        case 'force':       return $this->force;
        default: throw new SyL_InvalidParameterException("invalid property name ({$name})");
        }
    }

    /**
     * 操作定義オブジェクトをセットする
     * 
     * @param SyL_ContainerComponentOperation 操作定義オブジェクト
     * @param string イベント名
     */
    public function addOperation(SyL_ContainerEventComponentOperation $operation, $event=null)
    {
        if (!$event) {
            $event = $this->event;
        }
        if ($operation->type == 'constructor') {
            if ($event != $this->event) {
                throw new SyL_ContainerComponentException('component constructor event is different from component self event');
            }
            // コンストラクタインジェクションの場合、元のコンストラクタは削除
            $this->constructor = false;
            $this->operations[$event][0] = $operation;
        } else {
            $this->operations[$event][] = $operation;
        }
        $this->current_operation_event = $event;
        $this->current_operation_index = count($this->operations[$event]) - 1;
    }

    /**
     * カレントの操作定義オブジェクトを取得する
     *
     * @return SyL_ContainerComponentOperation カレントの操作定義オブジェクト
     */
    public function getCurrentOperation()
    {
        return $this->operations[$this->current_operation_event][$this->current_operation_index];
    }

    /**
     * 指定したイベントに対する操作定義オブジェクト取得する
     * 
     * @return array 指定したイベントに対する操作定義オブジェクト
     */
    public function getOperations($event)
    {
        if (isset($this->operations[$event])) {
            return $this->operations[$event];
        } else {
            return array();
        }
    }
    
    /**
     * アクションイベントの実行順序を適正化し、かつアクション関連メソッドを追加する
     * 
     * @param string 検証実行メソッド名
     * @param string アクション実行前メソッド名
     * @param string アクション実行後メソッド名
     */
    public function buildAction($validate_method_name, $pre_execute_method_name, $post_execute_method_name)
    {
        // execute メソッドを一時的に保持
        $execute_method = array_splice($this->operations[$this->current_operation_event], 1, 1);

        // validate メソッド追加
        $operation = new SyL_ContainerEventComponentOperation('method', $validate_method_name, false);
        $operation->addParameter('component', 'context');
        $operation->addParameter('component', 'data');
        $this->operations[$this->current_operation_event][] = $operation;

        // preExecute メソッド追加
        $operation = new SyL_ContainerEventComponentOperation('method', $pre_execute_method_name, false);
        $operation->addParameter('component', 'context');
        $operation->addParameter('component', 'data');
        $this->operations[$this->current_operation_event][] = $operation;

        // execute メソッド
        $this->operations[$this->current_operation_event] = array_merge($this->operations[$this->current_operation_event], $execute_method);

        // postExecute メソッド追加
        $operation = new SyL_ContainerEventComponentOperation('method', $post_execute_method_name, false);
        $operation->addParameter('component', 'context');
        $operation->addParameter('component', 'data');
        $this->operations[$this->current_operation_event][] = $operation;
    }
}
