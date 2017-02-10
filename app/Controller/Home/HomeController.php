<?php
/**
 * User: salamander
 * Date: 2016/10/11
 * Time: 8:46
 */

namespace App\Controller\Home;

use App\Controller\BaseController;
use App\Library\InputFilter;
use App\Library\Upload;
use App\Service\User;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Tool\Validator;
use App\Tool\Sender;
use App\Tool\Chkcode;

class HomeController extends BaseController
{
    public function index(Request $request, Response $response) {
        return $this->container->renderer->render($response, 'home.html');
    }




    // 上传图片示例
    public function uploadImage(Request $request, Response $response) {
        $upload = new Upload([
            'maxSize' => 20 * 1024 * 1024,
            'exts' =>  ['jpg', 'gif', 'png', 'jpeg'],
            'rootPath' => ROOT . '/Uploads/Pictures/',
            'saveName'   =>    array('uniqid', '')
        ]);
        $info = $upload->upload();
        $uploadRes = null;
        if($info) {
            echo json_encode(set_api_array(0, '上传成功！'));
        } else {
            echo json_encode(set_api_array(1, $upload->getError()));
        }
    }
}