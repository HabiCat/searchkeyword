<?php
namespace app\controllers;

use Yii;
use app\common\DCensor;

class PostController extends \app\common\CController {
	public function actionIndex() {

		$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
		$page = isset($_POST['page']) ? ($_POST['page'] ? $_POST['page'] : 1) : 1;
		$keywords = '';
		isset($getPost['searchName']) && $keywords = strip_tags($getPost['searchName']);
		$pageSize = 10;
		$start = ($page - 1) * $pageSize;
		$postModel = new \app\models\WPost;

		$this->cl->SetArrayResult (true);
		$this->cl->setMatchMode(SPH_MATCH_ANY);
		$this->cl->setMaxQueryTime(5);

		$this->cl->SetSortMode(SPH_SORT_ATTR_DESC, "createtime");
		$this->cl->SetLimits($start, $pageSize);
		$this->cl->SetFilterRange('createtime', 1, time());
		$res = $this->cl->Query($keywords, "post,post_increment");

		$ids = array();
		$where = '';
		if(isset($res['matches'])) {
			foreach($res['matches'] as $value) {
				array_push($ids, $value['id']);
			}

			$ids = implode(',', $ids);
			$where = 'id in (' . $ids . ')';
		}		
		
		$data = $postModel->getPostListByPage(0, $pageSize, $where);
		$datalist = array();
		if(!empty($data)) {
			$opts = array('before_match' => '<font color="red">', 'after_match' => '</font>');
			
			foreach($data['list'] as $key => $val) {
				$o = array();
				foreach($val as $k => $v) {
					if($k == 'subject' || $k == 'keywords') {
						$o[$k] = $v;
					}	
					$datalist[$key][$k] = $v;
				}

				if(count($o) == 2) {
					$datalist[$key]['excerpts'] = $this->cl->BuildExcerpts($o, 'post', $keywords, $opts);
				}
			}		
		}

		$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $res['total']));
		
		return $this->render('index', [
			'model' => $postModel,
			'data' => $datalist,
			'pager' => $pager,
			'searchName' => $keywords,
		]);
	}

	public function actionCreate() {
		$postModel = new \app\models\WPost;

		if(Yii::$app->request->isPost) {
			$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
			$getPost['keywords'] = preg_replace('/[\'"，“ \|]*/', '', strip_tags($getPost['keywords']));

			$postModel->attributes = $getPost;
			if($postModel->save()) {
				exit(json_encode(['status' => 1, 'msg' => '添加成功']));
			} else {
				exit(json_encode(['status' => -1, 'msg' => $postModel->getErrors()]));
			}
		}

		return $this->render('create', [
			'model' => $postModel,
		]);
	}

	public function actionUpdate() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		$postModel = \app\models\WPost::findOne($id);
		if($postModel) {
			if(Yii::$app->request->isPost) {			
				$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
				$getPost['id'] = $id;
				$postModel->attributes = $getPost;
				if($postModel->save()) {
					exit(json_encode(['status' => 1, 'msg' => '修改成功']));
				} else {
					exit(json_encode(['status' => -1, 'msg' => $postModel->getErrors()]));
				}								
			}
		
			return $this->render('update', [
				'model' => $postModel,
				'id' => $id,
			]);					
		} else {
			exit('此记录不存在');
		}
	}

	public function actionDelete() {
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		if($id) {
			$postModel = new \app\models\WPost;
			if($postModel->deleteRecord('id=' . $id)) {
				$this->cl->updateAttributes('post, post_increment',array('createtime'),array($id=>array(0)));
				exit(json_encode(['status' => 1, 'msg' => '删除成功']));
			} else {
				exit(json_encode(['status' => -1, 'msg' => '删除失败']));
			}
		}
	}
	
	public function actionJumpUrl() {
		$encodeUrl = isset($_GET['url']) ? $_GET['url'] : '';
		$postModel = new \app\models\WPost;
		if(preg_match('/[a-zA-Z0-9]*/', $encodeUrl)) {
			$o = $postModel->getRealUrl($encodeUrl);
			if(is_object($o)) {
				header('Location: ' . $o->url);
			}
		} 
	}
	
}