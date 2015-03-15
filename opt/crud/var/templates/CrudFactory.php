<?php
if (defined('SYL_FRAMEWORK_DIR')) {
    require_once SYL_FRAMEWORK_DIR . '/Lib/Crud/SyL_CrudConfigAbstract.php';
    require_once SYL_FRAMEWORK_DIR . '/Lib/Crud/SyL_CrudDbDaoAccessAbstract.php';
} else {
    require_once '{$SYL_FRAMEWORK_DIR}/Lib/Crud/SyL_CrudConfigAbstract.php';
    require_once '{$SYL_FRAMEWORK_DIR}/Lib/Crud/SyL_CrudDbDaoAccessAbstract.php';
}

class CrudFactory
{
    public static function createInstance($name, $crud_type, $filename='')
    {
        $classname = 'CrudConfig' . ucfirst($name);
        $file  = dirname(__FILE__) . '/Config/';
        $file .= $filename ? $filename : $classname . '.php';

        if (is_file($file)) {
            include_once $file;
        } else {
            throw new SyL_CrudNotFoundException("include file not found ({$file})");
        }

        if (!class_exists($classname)) {
            throw new SyL_CrudNotFoundException("class not loaded ({$classname})");
        }

        return new $classname($crud_type);
    }
}
