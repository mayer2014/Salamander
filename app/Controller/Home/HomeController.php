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

    public function sendSMS(Request $request, Response $response) {
        $tel = InputFilter::get('tel');
        $validator = new Validator(['tel'=> $tel]);
        $validator->setRules(['tel' => 'required|mobile'],
            ['tel' => '手机号']
        );
        if($validator->validate()) {
            $user = new User();
            if($user->exists($tel)) {
                $response = $response->withJson(set_api_array(1, '手机号已注册'));
            } else {
                $sender = new Sender();
                $chkcode = new Chkcode();
                $code = $chkcode->generate(range(0, 9), 4);
                $chkcode->set($code, 'phone_code');
                $content = "【一米发】您的验证码是{$code}。如非本人操作，请忽略本短信";
                if($sender->send($tel, $content)) {
                    $response = $response->withJson(set_api_array(0, 'ok'));
                } else {
                    $response = $response->withJson(set_api_array(1, '发送失败'));
                }
            }
        } else {
            $response = $response->withJson(set_api_array(1, $validator->getFirstErr()));
        }
        return $response;
    }

    // 上传图片
    public function showUploadImage(Request $request, Response $response) {
        return $this->container->renderer->render($response, 'upload_test.html');
    }

    // 上传图片
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