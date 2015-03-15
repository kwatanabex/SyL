<?php
require_once dirname(__FILE__) . '/../Access/{$ACCESS_CLASS_NAME}.php';

class {$CRUD_CLASS_NAME} extends SyL_CrudConfigAbstract
{
protected function createAccess()
{
    return new {$ACCESS_CLASS_NAME}($this);
}

protected $base_url = '';
protected $name = '{$CRUD_NAME}';
protected $description = '';
protected $enable = array(
  self::CRUD_TYPE_LST => true,
  self::CRUD_TYPE_EXP => true,
  self::CRUD_TYPE_IMP => true,
  self::CRUD_TYPE_NEW => true,
  self::CRUD_TYPE_VEW => true,
  self::CRUD_TYPE_EDT => true,
  self::CRUD_TYPE_DEL => true,
  self::CRUD_TYPE_RSS => true,
  self::CRUD_TYPE_ATM => true,
);
protected $list_config = array(
  'default_sort' => array(),
  'select_row_count' => array(10, 20, 50, 100),
  'default_row_count' => 20,
  'link_range'   => 9,
  'item_max_length' => 30,
);
protected $related_link = array();
protected $input_config = array(
  'pages' => array(
    '1' => array('header' => '', 'footer' => '', 'type' => self::FORM_TYPE_INPUT),
    '2' => array('header' => '', 'footer' => '', 'type' => self::FORM_TYPE_CONFIRM),
    '3' => array('header' => '', 'footer' => '', 'type' => self::FORM_TYPE_COMPLETE)
  ),
  'forwards' => array(
    array('from' => '1', 'to' => '2'),
    array('from' => '2', 'to' => '1'),
    array('from' => '2', 'to' => '3'),
  )
);
protected $rss_config = array(
  'row_count'    => 20,
  'default_sort' => array(),
  'item_format'  => array()
);
protected $atom_config = array(
  'row_count'    => 20,
  'default_sort' => array(),
  'item_format'  => array()
);

protected $element_config = {$CRUD_ELEMENTS};

}
