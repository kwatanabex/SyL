<?php
// SyL_DB connection string setting
//define('DAO_ACCESS_CONNECTION_STRING', '');

abstract class CrudAccessAbstract extends SyL_CrudDbDaoAccessAbstract
{
    public function getConnection()
    {
        if (!defined('DAO_ACCESS_CONNECTION_STRING')) {
            throw new SyL_DbException('SyL_DB connection string not setting');
        }
        return SyL_DbAbstract::getInstance(DAO_ACCESS_CONNECTION_STRING);
    }

    protected function getDaoDirectory()
    {
        return dirname(__FILE__) . '/../../Dao/Entity';
    }
}
