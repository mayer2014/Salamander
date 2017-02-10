<?php
/**
 * User: salamander
 * Date: 2016/11/26
 * Time: 15:09
 */
ini_set('memory_limit', '256M');
set_time_limit(20);
ini_set('session.name', 'WHOISSESSION');
// session存入redis
ini_set('session.save_handler', 'user');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');
ini_set('session.cookie_lifetime', 24 * 60 * 60);
session_set_save_handler(new \App\Library\Session\Redis());
// 过期时间2个小时
ini_set('session.gc_maxlifetime', 24 * 60 * 60);
ini_set('date.timezone','Asia/Shanghai');
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
session_cache_limiter(false);
session_start();
// 修改 X-Powered-By信息
header('X-Powered-By: Salamander');