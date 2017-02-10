<?php
/**
 * User: salamander
 * Date: 2016/12/21
 * Time: 15:52
 */

namespace App\Middleware;


use App\Service\Common;
use App\Service\User;

class UserLoginMiddleware
{
    /**
     * 用户是否登录中间件
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $user = new User();
        if($user->hasLogin()) {
            Common::$loginUid = $user->data['uid'];
            Common::$loginUserData = $user->data;
        }
        return $next($request, $response);
    }
}