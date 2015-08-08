<?php
namespace app\controllers;

use Yii;

class SearchController extends \app\common\CController {
	public $esClient;
	public $index = 'word';
	public $type = 'post_2';

	public function init() {
		// require_once(ROOT_PATH . '/api/sphinxapi.php');
		require_once(ROOT_PATH . '/api/elasticsearch-php/vendor/autoload.php'); 
		$this->createEsClient('localhost', 9200);
	}

	public function actionIndex() {
		$cl = new \api\SphinxClient ();
		$cl->SetServer ('127.0.0.1', 9312);
		//以下设置用于返回数组形式的结果
		$cl->SetArrayResult (true);
		$cl->setMatchMode(SPH_MATCH_ANY);//设置匹配模式，
		// $cl->setMatchMode(SPH_MATCH_EXTENDED2);//设置匹配模式，
		$cl->setMatchMode(SPH_MATCH_ALL);
		$cl->setMaxQueryTime(5);//设置最大查询时间
		// $cl->setRankingMode(SPH_RANK_PROXIMITY_BM25);

		$res = $cl->Query('apple', "*");
		$this->dump($res);
	}

	public function actionEs() {
		// $this->esClient->indices()->delete(['index' => 'post_index']);
		$this->createIndex($this->index, $this->type);
	 	//$page = $this->_getParam('page') ? $this->_getParam('page') : 1;
	 // 	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	 //    $params = array();  
		
		// $json = '{
		//     "query" : {
		//         "match" : {
		//             "subject" : "test"
		//         }
		//     },
		//     "highlight" : {
		//         "pre_tags" : ["<tag1>", "<tag2>"],
		//         "post_tags" : ["</tag1>", "</tag2>"],
		//         "fields" : {
		//             "subject" : {}
		//         }
		//     }
		// }';
		
		
		// $json = '{
		// 	"query": {
		// 		"bool": {
		// 		  "should": [
		// 		    { "match": { "subject":  "中国" }},
		// 		    { "match": { "keywords": "中国" }}
		// 		  ]
		// 		}
		// 	},
		//     "highlight" : {
		//         "pre_tags" : ["<tag1>", "<tag2>"],
		//         "post_tags" : ["</tag1>", "</tag2>"],
		//         "fields" : {
		//             "subject" : {},
		//             "keywords": {}
		//         }
		//     }
		// }';
	 //    $params['index'] = 'word_1'; 

	 //    $params['type'] = 'post_1';      
	 //    $params['body'] = $json;

	 //    $params['from'] = $page;
	 //    $params['size'] = 10;    

		// $rtn = $this->esClient->search($params);
	 //    $this->dump($rtn);
	 // return $this->render('es');
	}

	public function createIndexMapping($index, $type, $fields) {
		$indexParams['index']  = $index;

		// Index Settings
		$indexParams['body']['settings']['number_of_shards']   = 3;
		$indexParams['body']['settings']['number_of_replicas'] = 2;

		// Example Index Mapping
		$myTypeMapping = array(
			'_all' => array(
				'indexAnalyzer' => 'ik',
				'searchAnalyzer' => 'ik',
				'term_vector' => 'no',
				'store' => 'false',
			),
		    '_source' => array(
		        'enabled' => true
		    ),
		    // 'properties' => array(
		    //     'first_name' => array(
		            // 'type' => 'string',
		            // 'store' => 'no',
		            // 'term_vector' => 'with_positions_offsets',
		            // 'indexAnalyzer' => 'ik',
		            // 'searchAnalyzer' => 'ik',
		            // 'include_in_all' => 'true',
		            // 'boost' => 8,
		    //     ),
		    // )
		);

		if(!empty($fields)) {
			foreach($fields as $key => $val) {
				$myTypeMapping['properties'][$key] = $val;
			}
		}

		$indexParams['body']['mappings'][$type] = $myTypeMapping;

		// Create the index
		$client->indices()->create($indexParams);
	}

	public function putIndexMapping($index, $type, $fields) {
		$params['index'] = $index;
		$params['type']  = $type;

		// Adding a new type to an existing index
		$myTypeMapping = array(
		    '_source' => array(
		        'enabled' => true
		    ),
		);
		$params['body'][$type] = $myTypeMapping;
		if(!empty($fields)) {
			foreach($fields as $key => $val) {
				$myTypeMapping['properties'][$key] = $val;
			}
		}

		// Update the index mapping
		$this->esClient->indices()->putMapping($params);		
	}

	public function createIndex($index, $type) {
		$res = \app\models\WPost::find()->all();
		foreach($res as $key => $value) {
			$params = array();
			$params['body'] = [
				'id' => $value->id,
				'subject' => $value->subject,
				'keywords' => $value->keywords,
				'url_code' => $value->url_code,
			];
			$params['index'] = $index;
			$params['type'] = $type;
			$params['id'] = $key;
			$this->esClient->index($params);
		}

		echo 'create index done';
	}

	public function createEsClient($host, $port) {
		$this->esClient = new \Elasticsearch\Client();  // ['hosts' => $host . ':' . $port]
	}

	public function esInsert($index, $type, $inputData) {
		$inputData['index'] = $index;
		$inputData['type']  = $type;
		$index_document     = $this->esClient->bulk($inputData);
		if($index_document) {  
		 	return 1; 
		} else {  
			return 0;  
		}		
	}

	public function existsIndex($index, $type) {
		$checkArray = array();
		$checkArray['index'] = $index;
		$checkArray['type'] = $type;
		$checkArray['id'] = 1;    
		if( $this->esClient->exists($checkArray)) { 
		 	return true; 
		} else { 
			return false; 
		}
	}

	public function searchIndex($index, $type, $json){
		$searchArray = array();
		$searchArray['index'] = $index;
		$searchArray['type']  = $type;
		$searchArray['body'] = $json;
		if(!($this->existsIndex($index, $type))) { 
		 	return false; 
		} else {
			$retArray = $this->esClient->search($searchArray);
			if($retArray['hits']['total'] == 0) { 
				return false; 
			} else { 
				return $retArray ;
			} 
	    }
	}

	public function actionGetKeyWords() {
		$page = $this->_getParam('page') ? $this->_getParam('page') : 1;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.ciku5.com/words?citype=1&p=" . $page);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //如果把这行注释掉的话，就会直接输出
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);	

		if($result) {
			$result = mb_convert_encoding($result, 'utf-8', mb_detect_encoding($result));		
			if(preg_match_all('/<td class="red first"><div class="box"><a(.*?)>(.*?)<\/a><\/div><\/td>/', $result, $matches)) {
				if(!empty($matches)) {
					$mh  =  curl_multi_init ();
					$ch = array();
					foreach($matches[2] as $key => $keywords) {
						$curl_array[$key] = curl_init("http://suggestion.baidu.com/su?wd=" . $keywords . "&json=1&p=3");
						curl_setopt($curl_array[$key], CURLOPT_HEADER, false);
						curl_setopt($curl_array[$key], CURLOPT_RETURNTRANSFER, true); //如果把这行注释掉的话，就会直接输出
						curl_setopt($curl_array[$key], CURLOPT_CONNECTTIMEOUT, 10);
						curl_setopt($curl_array[$key], CURLOPT_TIMEOUT, 30);

						curl_multi_add_handle ($mh , $curl_array[$key]);
					}
					$running = NULL; 
			        do { 
			            usleep(10000); 
			            curl_multi_exec($mh, $running); 
			        } while($running > 0);

			        $res = array(); 
			        $filename = ROOT_PATH . '/controllers/data.txt';
			        $fp = fopen($filename, 'a');
		        	$node = '';			        
			        foreach($matches[2] as $i => $n) {
			            $res[$i] = curl_multi_getcontent($curl_array[$i]); 
			            $res[$i] = mb_convert_encoding($res[$i], 'utf-8', 'gbk');
	            		$res[$i] = preg_replace('/window\.baidu\.sug\((.*?)\);/i', '$1', $res[$i]);
	            		$res[$i] = json_decode($res[$i]);

						if(property_exists($res[$i], 's')) {
							$array = $res[$i]->s;
							foreach($array as $j => $k) {
								$node = "\"" . $k . "\",\"" . $n . "\",\"" . time() . "\"\r\n";	
						     	if (fwrite($fp, $node) === FALSE) {
				        			echo  "不能写入到文件  data.txt " ;
				    			}	
							}
						}
			        }

			        fclose($fp); 
			        
			        foreach($matches[2] as $i => $n){ 
			            curl_multi_remove_handle($mh, $curl_array[$i]); 
			        } 

			        curl_multi_close($mh);

				}
			}
		}
	}
}