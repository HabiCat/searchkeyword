<?php
/**
 *
 * 后台用户管理
 * 
 */

namespace app\controllers\iadmin;

use app\common\AdminBaseController;

class AdminController extends AdminBaseController {

	/**
	 * 用户列表
	 * @return [type] [description]
	 */
	public function actionIndex() {
		
		$model = new \app\models\WAdmin;
		$currentPage = $this->_getPost('page') ? $this->_getPost('page') : 1;
		$pageSize = $this->_getPost('pageSize') ? $this->_getPost('pageSize') : 10;

		$where = 1;
		if($this->_getPost('searchName')) {
			$where = $this->buildQuery(['username' => $this->_getPost('searchName')], 'and');
		}
		
		$data = $model->getAdminList(($currentPage - 1) * $pageSize, $pageSize, $where);

		foreach($data['data'] as $key => $value) {
			$data['data'][$key]['last_login_time'] = date('Y-m-d H:i:s', $value['last_login_time']);
		}

		$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $data['count']['n']));
		if(\Yii::$app->request->isGet) {
			return $this->render('index', array(
				'datalist' => $data['data'],
				'pager' => $pager,
			));
		} else {
			exit(json_encode(array('datalist' => $data['data'], 'pager' => \yii\widgets\LinkPager::widget([
                'pagination' => $pager,
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
            ]))));
		}
	}

	/**
	 * 创建用户
	 * @return [type] [description]
	 */
	public function actionCreate() {
		
		$adminModel = new \app\models\WAdmin;
		$adminGroupModel = new \app\models\WAdminGroup;

		if(\Yii::$app->request->isPost) {
			if($this->buildInsert($adminModel, $this->_getPost('WAdmin'))) {
				\app\common\XUtils::message('success', '用户信息添加成功！');
			} 
		}

		return $this->render('create', [
			'model' => $adminModel,
			'groupList' => $adminGroupModel->getDropDownList($adminGroupModel->getBaseAdminGroupList()),
		]);
	}

	/**
	 * 更新用户信息
	 * @return [type] [description]
	 */
	public function actionEdit() {
		$aid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		$adminModel = \app\models\WAdmin::findOne($aid);
		$adminGroupModel = new \app\models\WAdminGroup;
		if($adminModel) {
			if($_SESSION['accountID'] != 1 && $aid == 1) {
				\app\common\XUtils::message('error', '无权修改', \Yii::$app->urlManager->createUrl(['iadmin/admin/index']));
			}

			if(\Yii::$app->request->isPost) {
				$getPost = $this->_getPost('WAdmin');
				$getPost['id'] = $aid;
				$filterData = $adminModel->writeDataValidate($getPost);
				if(!empty($filterData)) {
					if($adminModel->updateAdminInfo($filterData)) {
						\app\common\XUtils::message('success', '用户信息更新成功！', \Yii::$app->urlManager->createUrl(['iadmin/admin/edit', 'id' => $aid]));
					}
				}
			}
		
			$data = $adminModel->getSingleAdminInfoByID($aid);
			$adminModel->password = '';

			return $this->render('edit', [
				'model' => $adminModel,
				'groupList' => $adminGroupModel->getDropDownList($adminGroupModel->getBaseAdminGroupList()),
			]);					
		}
		\app\common\XUtils::message('error', '用户不存在', \Yii::$app->urlManager->createUrl(['iadmin/admin/index']));
	}

	/**
	 * 有问题 待解决
	 * @return [type] [description]
	 */
	public function actionDelete() {
		$adminModel = new \app\models\WAdmin;
		$backUrl = \Yii::$app->urlManager->createUrl('iadmin/admin/index');
		if(\Yii::$app->request->isGet) {
			$ids = $this->_getParam('id');
			if(!$adminModel->isExist(['id' => $ids], 'id')) {
				$this->redirect($backUrl);
			}

		} elseif(\Yii::$app->request->isPost) {
			$ids = $this->_getPost('ids');
			$ids = implode(',', $ids);
		}

		if(in_array(1, (array) $ids)) {
			\app\common\XUtils::message('error', '超级管理员不能被删除', $backUrl);
		}
		
		if($adminModel->deleteRecord('id in (' . $ids .')')) {
			\app\common\XUtils::message('success', '用户信息删除成功！', $backUrl);
		} 

		\app\common\XUtils::message('success', '用户信息删除失败，请重试！', $backUrl);
	}


}