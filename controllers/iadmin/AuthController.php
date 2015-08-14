<?php
/**
 *
 * 后台权限管理
 * 
 */

namespace app\controllers\iadmin;

use app\common\AdminBaseController;

class AuthController extends AdminBaseController {

	public function init () {
		parent::init();
	}

	/**
	 * 查询有问题，只能查出部分数据
	 * @return [type] [description]
	 */
	public function actionIndex() {

		$adminGroupModel = new \app\models\WAdminGroup;
		$currentPage = $this->_getPost('page') ? $this->_getPost('page') : 1;
		$pageSize = $this->_getPost('pageSize') ? $this->_getPost('pageSize') : 10;

		$where = 'id <> 1';
		if($this->_getPost('searchName')) {
			$where .= ' and ' . $this->buildQuery(['group_name' => $this->_getPost('searchName')], 'and');
		}

		$data = $adminGroupModel->getAdminGroupList(($currentPage - 1) * $pageSize, $pageSize, $where);

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
                $html .= '<td>' . $value['group_name'] . '</td>';
                $html .= '<td class="center">';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl('iadmin/auth/assign', ['id' => $value['id']]) . '">编辑权限</a>&nbsp;&nbsp;';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl('iadmin/auth/create', ['id' => $value['id']]) . '">编辑</a>&nbsp;&nbsp;';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl('iadmin/auth/delete', ['id' => $value['id']]) . '">删除</a>';
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

	public function actionCreate() {
		$adminGroupModel = new \app\models\WAdminGroup;

		if(\Yii::$app->request->isPost) {
			if($this->buildInsert($adminGroupModel, $this->_getPost('WAdminGroup'))) {
				\app\common\XUtils::message('success', '用户组添加成功！', \Yii::$app->urlManager->createUrl('iadmin/auth/index'));
			} 
		}

		return $this->render('create', [
			'model' => $adminGroupModel,
		]);
	}

	public function actionEdit() {
		$gid = $this->_getParam('id');
		$adminGroupModel = \app\models\WAdminGroup::findOne($gid);

		if(\Yii::$app->request->isPost) {
			$getPost = $this->_getPost('WAdminGroup');
			if($getPost['id']) {
				if($this->buildInsert($adminGroupModel, $getPost)) {
					\app\common\XUtils::message('success', '用户组更新成功！', \Yii::$app->urlManager->createUrl(['iadmin/auth/edit', 'id' => $id]));
				}
			}	
		}

		if($adminGroupModel->isExist(['id' => $gid], 'id')) {
			return $this->render('edit', [
				'model' => $adminGroupModel,
			]);				
		}

		\app\models\XUtils::message('error', '无此用户组信息', \Yii::$app->urlManager->createUrl('iadmin/auth/index'));
	}

	/**
	 * 有问题 待解决
	 * @return [type] [description]
	 */
	public function actionDelete() {
		$adminModel = new \app\models\WAdminGroup;
		$backUrl = \Yii::$app->urlManager->createUrl('iadmin/auth/index');
		if(\Yii::$app->request->isGet) {
			$ids = $this->_getParam('id');
		} elseif(\Yii::$app->request->isPost) {
			$ids = $this->_getPost('ids');
			$ids = implode(',', $ids);
		}

		
		if($adminModel->deleteRecord('id in (' . $ids .')')) {
			\app\common\XUtils::message('success', '用户组删除成功！', $backUrl);
		} 

		\app\common\XUtils::message('error', '用户信息组失败，请重试！', $backUrl);
	}

	public function actionAssign() {

		$gid = $this->_getParam('id');
		if($gid) {
			$menuModel = new \app\models\WMenu;
			$adminGroupModel = new \app\models\WAdminGroup;

			$adminGroupPower = $adminGroupModel->getUserPower($gid);

			return $this->render('assign', [
				'id' => $gid,
				'menus' => $menuModel->getAllFormatMenus(),
				'groupName' => $adminGroupPower['group_name'],
				'adminGroupPower' => explode(',', $adminGroupPower['group_options']),
			]);
		}

		\app\common\XUtils::message('error', '不存在此用户组！');
	}

	public function actionEditPower() {
		$id = $this->_getPost('id');
		$backUrl = \Yii::$app->urlManager->createUrl(['iadmin/auth/assign', 'id' => $id]);
		$adminGroupModel = new \app\models\WAdminGroup;
		if($id > 0 && $adminGroupModel->isExist(['id' => $id], 'id')) {
			$powers = $this->_getPost('Power');
			if(!empty($powers)) {
				$array['group_options'] = implode(',', $powers);
			} else {
				\app\common\XUtils::message('error', '请选择权限', $backUrl);
			}

			$array['id'] = $id;
			if($this->buildUpdate($id, $adminGroupModel, $array)) {
				\app\common\XUtils::message('success', '用户组权限更新成功！', $backUrl);
			} else {
				\app\common\XUtils::message('error', '用户组权限更新失败！', $backUrl);
			}
		} 
	}

	public function actionIndexPowerOptions() {
		$menuModel = new \app\models\WMenu;
		$currentPage = $this->_getPost('page') ? $this->_getPost('page') : 1;
		$pageSize = $this->_getPost('pageSize') ? $this->_getPost('pageSize') : 10;
		// $currentPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

		$where = 1;
		if($this->_getPost('searchName')) {
			$where = ' and ' . $this->buildQuery(['menu_title' => $this->_getPost('searchName')], 'and');
		}

		//$data = $menuModel->getMenuArray(($currentPage - 1) * $pageSize, $pageSize, $where);
		$filterMenus = $menuModel->getMenuListOptions($menuModel->getAllMenus($where));
		$count = count($filterMenus);
		$data = array();
		if(!empty($filterMenus)) {
			$start = ($currentPage - 1) * $pageSize;
			$end = min(($currentPage - 1) + $pageSize, count($filterMenus));
			for($i = $start; $i < $end; $i++) {
				$data[$i] = $filterMenus[$i];
			}
		}
		
		$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $count));
		if(\Yii::$app->request->isGet) {
			return $this->render('indexpoweroptions', array(
				'datalist' => $data,
				'pager' => $pager,
			));
		} else {
			$html = '';
			foreach($data as $value) {
                $html .= '<tr class="odd gradeX">';
                $html .= '<td><input type="checkbox" name="ids[]" value="' . $value['id'] . '" />&nbsp;&nbsp;' . $value['id'] . '</td>';
                $html .= '<td>' . $value['menu_title'] . '</td>';
                $html .= '<td>' . $value['menu_url'] . '</td>';
                $html .= '<td>' . (!$value['type'] ? '菜单' : '操作项') . '</td>';
                 $html .= '<td>' . $value['sort'] . '</td>';
                $html .= '<td class="center">';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl('iadmin/auth/edit-power-options', ['id' => $value['id']]) . '">编辑</a>&nbsp;&nbsp;';
                $html .= '<a href="' . \Yii::$app->urlManager->createUrl('iadmin/auth/delete-power-options', ['id' => $value['id']]) . '">删除</a>';
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

	public function actionCreatePowerOptions() {
		$menuModel = new \app\models\WMenu;
		if(\Yii::$app->request->isPost) {
			$backUrl = \Yii::$app->urlManager->createUrl('iadmin/auth/create-power-options');
			$getPost = $this->_getPost('WMenu');
			if($this->isTwoLayersOfSuper($getPost['pid']) > 1 && $getPost['type'] == 0) {
				\app\common\XUtils::message('error', '暂不支持添加三级及以上菜单！', $backUrl);
			}

			$getPost['menu_acl'] = str_replace('/', '_', $getPost['menu_url']);

			if($this->buildInsert($menuModel, $getPost)) { 
				\app\common\XUtils::message('success', '菜单添加成功！', $backUrl);
			}
		}

		$groupList = $this->menusDropDownList($menuModel);
		$keys = array_keys($groupList);
		$keys = array_merge(array(0), $keys);

		$values = array_values($groupList);
		$values = array_merge(array('顶级分类'), $values);

		$tmpList = array();
		foreach($keys as $k => $v) {
			$tmpList[$v] = $values[$k];
		}

		return $this->render('createpoweroptions', [
			'model' => $menuModel,
			'groupList' => $tmpList,
		]);
	}

	public function actionEditPowerOptions() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		$menuModel = \app\models\WMenu::findOne($id);

		if($menuModel) {
			if(\Yii::$app->request->isPost) {
				$getPost = $this->_getPost('WMenu');
				if($getPost['id']) {
					if($this->parentTrue($getPost['id'], $getPost['pid'])) {
						if($this->isTwoLayersOfSuper($getPost['pid']) > 1) {
							\app\common\XUtils::message('error', '暂不支持添加三级及以上菜单！', \Yii::$app->urlManager->createUrl(['iadmin/auth/edit-power-options', 'id' => $id]));
						}

						if($this->buildUpdate($getPost['id'], $menuModel, $getPost)) {
							\app\common\XUtils::message('success', '菜单更新成功！', \Yii::$app->urlManager->createUrl(['iadmin/auth/edit-power-options', 'id' => $id]));
						}
					} else {
						\app\common\XUtils::message('error', '不能选择当前菜单或当前菜单下级菜单', \Yii::$app->urlManager->createUrl(['iadmin/auth/edit-power-options', 'id' => $id]));
					}
				}	
			}

			$groupList = $this->menusDropDownList($menuModel);
			$keys = array_keys($groupList);
			$keys = array_merge(array(0), $keys);

			$values = array_values($groupList);
			$values = array_merge(array('顶级分类'), $values);

			$tmpList = array();
			foreach($keys as $k => $v) {
				$tmpList[$v] = $values[$k];
			}

			return $this->render('editpoweroptions', [
				'model' => $menuModel,
				'groupList' => $tmpList,
			]);				
		}

		\app\models\XUtils::message('error', '无此菜单信息', \Yii::$app->urlManager->createUrl('iadmin/auth/index-power-options'));	
	}

	/**
	 * 有问题 待解决
	 * @return [type] [description]
	 */
	public function actionDeletePowerOptions() {
		$menuModel = new \app\models\WMenu;
		$backUrl = \Yii::$app->urlManager->createUrl('iadmin/auth/index-power-options');
		if(\Yii::$app->request->isGet) {
			$ids = $this->_getParam('id');
			if(!$menuModel->isExist(['id' => $ids], 'id')) {
				$this->redirect($backUrl);
			}

		} elseif(\Yii::$app->request->isPost) {
			$ids = $this->_getPost('ids');
			$ids = implode(',', $ids);
		}

		foreach((array) $ids as $key => $val) {
			$subCatalogArray = $menuModel->getMenuListOptions($menuModel->getAllMenus('type <> 1'), $val);
			if(!empty($subCatalogArray)) 
				\app\common\XUtils::message('error', 'ID为' . $val . '有下级菜单，不能删除', $backUrl);
		}

		if($menuModel->deleteRecord('id in (' . $ids .')')) {
			\app\common\XUtils::message('success', '菜单信息删除成功！', $backUrl);
		} 

		\app\common\XUtils::message('error', '用户信息删除失败，请重试！', $backUrl);
	}

	protected function menusDropDownList($model) {
		$allMenus = $model->getMenuListOptions($model->getAllMenus('type <> 1'));
		$groupList = array();
		foreach($allMenus as $value) {
			$groupList[$value['id']] = $value['menu_title'];
		}

		return $groupList;
	}


	public function parentTrue($id, $parentId) {
		$menuModel = new \app\models\WMenu;
		$subCatalogArray = $menuModel->getMenuListOptions($menuModel->getAllMenus('type <> 1'), $id);

		$parentArray = array();
		if($id == $parentId) {
			$parentArray[] = $parentId;
		} else {
			if(!empty($subCatalogArray)) {
				foreach($subCatalogArray as $key => $val) {
					$parentArray[] = $val['id'];
				}				
			}
		} 

		if(in_array($parentId, $parentArray)) {
			return false;
		}

		return true;
	}
}