<?php
/**
 * User: salamander
 * Date: 2016/10/26
 * Time: 9:51
 */

namespace App\Service;


class BaseService
{
    static $container = null;

    protected $db;

    public function __construct() {
        $this->db = self::$container->db;
    }

}