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
		print_r($this->_cookiesGet('auth'));
		
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
			$html = '';
			foreach($data['data'] as $value) {
                $html .= '<tr class="odd gradeX">';
                $html .= '<td><input type="checkbox" name="ids[]" value="' . $value['id'] . '" />&nbsp;&nbsp;' . $value['id'] . '</td>';
                $html .= '<td>' . $value['username'] . '</td>';
                $html .= '<td>' . $value['group_name'] . '</td>';
                $html .= '<td class="center">' . $value['last_login_time'] . '</td>';
                $html .= '<td class="center">';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl(['iadmin/admin/edit','id' => $value['id']]) . '">编辑</a>';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl(['iadmin/admin/delete', 'id' => $value['id']]) . '">删除</a>';
                $html .= '</td> '; 
			}

			$pages = \yii\widgets\LinkPager::widget([
                'pagination' => $pager,
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
            ]);

			exit(json_encode(array('datalist' => $html, 'pager' => $pages)));
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
		$aid = $this->_getParam('id');
		$adminModel = \app\models\WAdmin::findOne($aid);
		$adminGroupModel = new \app\models\WAdminGroup;

		if(\Yii::$app->request->isPost) {
			$getPost = $this->_getPost('WAdmin');
			if($getPost['id']) {
				$filterData = $adminModel->writeDataValidate($getPost);
				if(!empty($filterData)) {
					if($adminModel->updateAdminInfo($filterData)) {
						\app\common\XUtils::message('success', '用户信息更新成功！', \Yii::$app->urlManager->createUrl(['iadmin/admin/edit', 'id' => $aid]));
					}
				}
			}	
		}

		if($adminModel->isExist(['id' => $aid], 'id')) {
			$data = $adminModel->getSingleAdminInfoByID($aid);
			$adminModel->password = '';
			if(!empty($data)) {
				return $this->render('edit', [
					'model' => $adminModel,
					'groupList' => $adminGroupModel->getDropDownList($adminGroupModel->getBaseAdminGroupList()),
				]);				
			}
		}
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

		
		if($adminModel->deleteRecord('id in (' . $ids .')')) {
			\app\common\XUtils::message('success', '用户信息删除成功！', $backUrl);
		} 

		\app\common\XUtils::message('success', '用户信息删除失败，请重试！', $backUrl);
	}


}