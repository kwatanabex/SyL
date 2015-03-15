<?php
require_once 'DaoAccessAbstract.php';
require_once dirname(__FILE__) . '/../Entity/{$ENTITY_CLASS_NAME}.php';

class {$CLASS_NAME} extends DaoAccessAbstract
{
protected $main_alias = 'a';
protected $class_names = array (
  'a' => '{$ENTITY_CLASS_NAME}'
);
protected $relations = array(
);
}
