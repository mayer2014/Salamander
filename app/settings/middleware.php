<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->getContainer()['csrf'] = function () {
    return new \Slim\Csrf\Guard;
};

$app->add($app->getContainer()->get('csrf'));

// IP记录中间件
$app->add(new \App\Middleware\IPRecord());

// 用户是否登录中间件
$app->add(new \App\Middleware\UserLoginMiddleware());

// csrf保护中间件
//$app->add(new \App\Middleware\CsrfGuard());