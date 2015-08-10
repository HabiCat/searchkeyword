<?php
namespace app\controllers;

use Yii;

class PostController extends \app\common\CController {
	public $esClient;
	public $index = 'word';
	public $type = 'post_2';

	public function init() {
		// require_once(ROOT_PATH . '/api/sphinxapi.php');
		require_once(ROOT_PATH . '/api/elasticsearch-php/vendor/autoload.php'); 
		$this->createEsClient('localhost', 9200);
	}

	public function createEsClient($host, $port) {
		$this->esClient = new \Elasticsearch\Client();  // ['hosts' => $host . ':' . $port]
	}

	public function createIndex($index, $type, $fields) {
		$params = array();
		// $params['body'] = [
		// 	'id' => $id,
		// 	'subject' => $value->subject,
		// 	'keywords' => $value->keywords,
		// 	'url_code' => $value->url_code,
		// ];

		foreach($fields as $key => $val) {
			$params['body'][$key] = $val;
		}
		$params['index'] = $index;
		$params['type'] = $type;
		// $params['id'] = $key;
		$this->esClient->index($params);	
	}
	// public function actionIndex() {

	// 	$getPost = $this->_getPost('WPost');
	// 	$page = $this->_getPost('page') ? $this->_getPost('page') : 1;
	// 	$keywords = strip_tags($getPost['searchName']);
	// 	$pageSize = 10;
	// 	$start = ($page - 1) * $pageSize;
	// 	$postModel = new \app\models\WPost;

	// 	require_once(ROOT_PATH . '/api/SphinxClient.php');
	// 	$cl = new \api\SphinxClient();
	// 	$cl->SetServer ('127.0.0.1', 9312);
	// 	$cl->SetArrayResult (true);
	// 	$cl->setMatchMode(SPH_MATCH_ANY);
	// 	$cl->setMaxQueryTime(5);

	// 	$res = $cl->Query($keywords, "*");
	// 	$ids = array();
	// 	$where = 1;
	// 	if(isset($res['matches'])) {
	// 		foreach($res['matches'] as $value) {
	// 			array_push($ids, $value['id']);
	// 		}

	// 		$ids = implode(',', $ids);
	// 		$where = 'id in (' . $ids . ')';
	// 	}		
		
	// 	$data = $postModel->getPostListByPage($start, $pageSize, $where);

	// 	$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $data['n']));
		
	// 	return $this->render('index', [
	// 		'model' => $postModel,
	// 		'data' => $data['list'],
	// 		'pager' => $pager,
	// 		'searchName' => $keywords,
	// 	]);
	// }
	
	public function actionIndex() {

		$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
		$page = isset($_POST['page']) ? ($_POST['page'] ? $_POST['page'] : 1) : 1;
		$keywords = '';
		isset($getPost['searchName']) && $keywords = strip_tags($getPost['searchName']);
		$pageSize = 10;
		$start = ($page - 1) * $pageSize;
		$postModel = new \app\models\WPost;
	    $params = array();  
		
		$json = '{
		    "query" : {
		        "match" : {
		            "subject" : "' . $keywords . '"
		        }
		    },
		    "highlight" : {
		        "pre_tags" : ["<tag1>", "<tag2>"],
		        "post_tags" : ["</tag1>", "</tag2>"],
		        "fields" : {
		            "subject" : {}
		        }
		    }
		}';
	    $params['index'] = $this->index; 
	    $params['type'] = $this->type;      
	    $params['body'] = $json;
	    $params['from'] = $start;
	    $params['size'] = $pageSize;    

		$res = $this->esClient->search($params);
		$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $res['hits']['total']));
		
		return $this->render('index', [
			'model' => $postModel,
			'data' => $res['hits']['hits'],
			'pager' => $pager,
			'searchName' => $keywords,
		]);

	}


	public function actionCreate() {
		$postModel = new \app\models\WPost;

		if(Yii::$app->request->isPost) {
			$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
			$postModel->attributes = $getPost;
			if($postModel->save()) {
				$info = \app\models\WPost::find()->where('id=' . $postModel->id)->one();
				$this->createIndex($this->index, $this->type, [
					'id' => $info->id,
					'subject' => $info->subject,
					'keywords' => $info->keywords,
					'url_code' => $info->url_code,
				]);
				exit(json_encode(['status' => 1, 'msg' => '添加成功']));
			}
		}

		return $this->render('create', [
			'model' => $postModel,
		]);
	}

	public function actionUpdate() {
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		if(Yii::$app->request->isPost) {
			$getPost = isset($_POST['WPost']) ? $_POST['WPost'] : '';
			// $postModel->attributes =  $this->_getPost('WPost');
			$postModel->attributes = $getPost;
			if($postModel->save()) {					
				// $this->esClient->bulk($params);	
				exit(json_encode(['status' => 1, 'msg' => '修改成功']));
			}
		}
		if($id) {
			$postModel = \app\models\WPost::findOne($id);

			return $this->render('create', [
				'model' => $postModel,
			]);			
		}
	}
}