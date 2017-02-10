<?php
/**
 * User: salamander
 * Date: 2016/10/9
 * Time: 13:07
 */

namespace App\Controller;

use App\Service\Common;
use Interop\Container\ContainerInterface;



class BaseController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        // 兼容原来的数据库操作
        if(property_exists($this, 'db')) {
            $this->db = singleton('db');
        }
        $this->container = $container;
        // 公众模板变量
        if(Common::$loginUid) {
            $this->container->renderer->addAttribute('user_data', Common::$loginUserData);
        }
        $this->assignCSRF($container);
    }

    /**
     * csrf键值对
     * @param ContainerInterface $container
     */
    protected function assignCSRF(ContainerInterface $container) {
        // CSRF token name and value
        $csrfNameKey = $this->container->csrf->getTokenNameKey();
        $csrfValueKey = $this->container->csrf->getTokenValueKey();
        $csrfName = $this->container->csrf->getTokenName();
        $csrfValue = $this->container->csrf->getTokenValue();
        $csrfArr = [
            'keys' => [
                'name'  => $csrfNameKey,
                'value' => $csrfValueKey
            ],
            'name'  => $csrfName,
            'value' => $csrfValue
        ];
        $this->container->renderer->addAttribute('csrf', $csrfArr);
    }

}