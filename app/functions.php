<?php
/**
 * User: salamander
 * Date: 2016/10/11
 * Time: 8:49
 * 自定义公众函数辅助库
 */
function set_api_array($errcode, $errmsg, $res = null) {
    return [
        'errcode' => $errcode,
        'errmsg' => $errmsg,
        'res' => $res
    ];
}

/**
 * 单例模式
 * @param $className string
 * @return mixed
 */
function singleton($className) {
    static $instances = array();
    static $alias = array(
        'tpl'=> '\\Template',
        'db'=> '\\App\\Library\\DB',
    );
    if(isset($alias[$className])) {
        $className = $alias[$className];
    }
    if (!isset($instances[$className])) {
        $object = new $className;
        if(method_exists($object, '__setup')) {
            $confs = require __DIR__ . '/config.php';
            $conf = null;
            if(strcmp($className, $alias['db']) === 0) {
                $conf = $confs['db'];
            }
            call_user_func(array($object, '__setup'), $conf);
        }
        $instances[$className] = $object;
    }
    return $instances[$className];
}

/**
 * 获取slim 的配置文件数组
 * @return array|mixed
 */
function get_config_arr() {
    static $arr = [];
    if(!$arr) {
        $arr = require __DIR__ . '/config.php';
    }
    return $arr;
}