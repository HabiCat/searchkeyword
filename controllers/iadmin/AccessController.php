<?php

namespace app\controllers\iadmin;

use Yii;

class AccessController extends \app\common\CController {
	protected $adminModel;

	public function init() {
		parent::init();
		$this->adminModel = new \app\models\WAdmin;
	}

	public function actionLogin() {
		if($this->_sessionGet('accountID')) {
			\app\common\XUtils::message('success', '您已经登录，无需重复登录', \Yii::$app->urlManager->createUrl(['iadmin/admin/index']));
		}

		if(Yii::$app->request->isPost) {
			$getPost = $this->_getPost('WAdmin');
			if(!empty($getPost)) {
				if(!trim($getPost['username'])) {
					exit(json_encode(['status' => -1, 'msg' => '请填写用户名']));
				} 
				if(!trim($getPost['password'])) {
					exit(json_encode(['status' => -1, 'msg' => '请填写密码']));
				}
				if(!trim($getPost['verifycode'])) {
					exit(json_encode(['status' => -1, 'msg' => '请填写验证码']));	
				}
			} else {
				exit(json_encode(['status' => -1, 'msg' => '请填写登录信息']));
			}

			if($this->_sessionGet('__captcha/site/captcha') != $getPost['verifycode']) {
				exit(json_encode(['status' => -1, 'msg' => '验证码错误']));
			}

			$userinfo = $this->adminModel->getSingleAdminInfo(['username' => $getPost['username'], 'password' => md5($getPost['password'])]);
			if(!empty($userinfo)) {
				$this->_sessionSet('accountID', $userinfo->id);
				$this->_sessionSet('accountName', $userinfo->username);
                $this->_sessionSet('accountTime', time());
				if(isset($getPost['reme'])) {
					$this->_cookiesSet('accountInfo', serialize($userinfo));
				}
				exit(json_encode(['status' => 1, 'msg' => '登陆成功']));
			} else {
				exit(json_encode(['status' => -1, 'msg' => '用户名或密码错误']));
			}

		}

		return $this->renderPartial('login',[
			'model' => $this->adminModel,
		]);
	}

	public function actionLogout() {
        $this->_sessionRemove('accountID');
        $this->_sessionRemove('accountName');
        $this->_sessionRemove('accountTime');	
        $this->_session->destroy();

        \app\common\XUtils::message('success', '成功退出', \Yii::$app->urlManager->createUrl(['iadmin/access/login']));
	}
}

