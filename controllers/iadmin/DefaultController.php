<?php
/**
 *
 * 后台默认控制器
 * 
 */

namespace app\controllers\iadmin;

use app\common\AdminBaseController;
use app\common\XUtils;
use yii\helpers\ArrayHelper;
use app\models\WAdmin;

class DefaultController extends AdminBaseController {

	public function init() {
		parent::init();
	}

	public function actionIndex() {

		//print_r((new WAdmin)->attributes());
		// $pagination = new Pagination(['totalCount' => 10, 'pageSize'=>5]);

		// return $this->renderPartial('index', array(
		// 	'menus' => $this->getBaseFilterMenus(),
		// 	'pagination' => $pagination,
		// ));
	}
}