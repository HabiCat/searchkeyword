<?php
/**
 *
 * 后台管理基础类
 * 
 */
namespace app\common;

use Yii;
use app\common\CController;
use app\models\WAdmin;
use app\models\WMenu;
use yii\db\Query;

class AdminBaseController extends CController {

	public $layout = 'main';
	protected $_aid;
	protected $_account;
	protected $_urlFlag;
	public $_menus;

	public function init () {
		parent::init();

		$this->_sessionSet('account', 'test');
		$_account = $this->_sessionGet('account');
		$this->_sessionSet('aid', 2);
		$_account = $this->_sessionGet('aid');

		$this->_menus = $this->getBaseFilterMenus();
	}

	/**
	 * 设置Url规则
	 */
	protected function setUrlFlag() {
		$controller = $this->id;
		$action = $this->action->id;
		// if(preg_match('/iadmin/i', $controller)) {
		// 	$controller = str_replace('iadmin/', '', $controller);
		// }
		if(preg_match('/(\-|\/)/', $action)) {
			$action = str_replace('-', '_', $action);
		}

		return $controller . '_' . $action;	
	}

	/**
	 * 获取所有菜单项
	 * @return [type] [description]
	 */
	protected function getAllMenus() {
		$menu_object = WMenu::find()->where('type = 0')->all();

		$menus = array();	
		if(!empty($menu_object)) {
			foreach($menu_object as $menu) {
				foreach($menu as $key => $value) {
					$menus[$menu->id][$key] = $value;
				}	
			}
		}

		return $menus;
	} 

	/**
	 * 判断有几层上级
	 * @param  [type]  $id    [description]
	 * @param  integer $level [description]
	 * @return boolean        [description]
	 */
	protected function isTwoLayersOfSuper($id, $level = 1) {
		$all_menus = $this->getAllMenus();

		if(!empty($all_menus) && $id) {
			foreach($all_menus as $key => $menu) {
				if($menu['id'] == $id) {
					foreach($all_menus as $k => $v) {
						if($v['id'] == $menu['pid']) {
							$level = $this->isTwoLayersOfSuper($menu['pid'], $level + 1);			
							break;
						}
					}
				}
			}
		}

		return $level;
	}

	/**
	 * 获取后台基础菜单项
	 * @return [type] [description]
	 */
	protected function getBaseMenus() {
		$all_menus = $this->getAllMenus();

		$connection = Yii::$app->db;
		$sql = 'select g.group_options from w_admin_group as g where g.id = (select a.group_id from w_admin as a where a.id = ' . $this->_sessionGet('aid') . ')';
		$ids =  $connection->createCommand($sql)->queryOne();

		$sql = 'select m.menu_acl from w_menu as m where m.id in (' . $ids['group_options'] . ') order by m.id asc';
		$acls = $connection->createCommand($sql)->queryAll();

		$filter_acls = array();
		foreach($acls as $value) {
			array_push($filter_acls, $value['menu_acl']);
		}

		$menus = array();	
		if(!empty($all_menus)) {
			foreach($all_menus as $key => $menu) {
				if($this->isTwoLayersOfSuper($menu['id']) < 3) {
					if(in_array($menu['menu_acl'], $filter_acls)) {
						$menus[$menu['id']] = $menu;
					}
				}
			}	
		}

		return $menus;	
	}	

	/**
	 * 获取到最终的菜单
	 * @return [type] [description]
	 */
	protected function getBaseFilterMenus() {
		$menus = $this->getBaseMenus();
		
		$filter_menus = array();
		foreach($menus as $menu) {
			if($menu['pid'] == 0) {
				$filter_menus[$menu['id']] = $menu;
			} else {
				if(isset($filter_menus[$menu['pid']]['id'])) {
						$filter_menus[$menu['pid']]['childs'][] = $menu;
				} else {
					continue;
				}
			}
		}

		return $filter_menus;	
	}	


}