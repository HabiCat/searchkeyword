<?php
namespace app\controllers\iadmin;

use Yii;
use app\common\DCensor;
use app\common\XUtils;

class SettingController extends \app\common\AdminBaseController {
	public function index() {}

	public function actionFilterKw() {
		$filterkwPath = ROOT_PATH . '/data/filter_keywords.tmp';
		$kwRecord = \app\models\WSetting::find()->select('values')->where(array("keys" =>"filter_keywords"))->one();
		if($kwRecord) {
			$settingModel = \app\models\WSetting::findOne("filter_keywords");
		} else {
		 	$settingModel = new \app\models\WSetting;
		}

		if(Yii::$app->request->isPost) {
			if($_POST['WSetting']) {
				$_POST['WSetting']['values'] = strip_tags($_POST['WSetting']['values']);
				$_POST['WSetting']['values'] = preg_replace('/[\'"，“ \|]*/', '', $_POST['WSetting']['values']);
				$settingModel->attributes = $_POST['WSetting'];
				if($settingModel->save()) {
					file_put_contents($filterkwPath, $_POST['WSetting']['values']);
					XUtils::message('success', '更新成功');
				}	
			}
		}

		return $this->render('filterkw', array(
			'model' => $settingModel,
		));
	}
}