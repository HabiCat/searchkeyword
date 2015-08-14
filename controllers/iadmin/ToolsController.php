<?php

namespace app\controllers\iadmin;

use Yii;

class ToolsController extends \app\common\AdminBaseController {

	public function actionUploadDict() {
		$sougouDir = ROOT_PATH . '/upload/dict/sougou';
		if(Yii::$app->request->isPost) {
			ini_set('upload_tmp_dir', ROOT_PATH . '/upload/upload_tmp_dir/');
			$upload = new \app\common\UploadFile();
			$upload->allowExts = array('scel');
			$upload->saveRule = '';
			$upload->savePath = $sougouDir . '/';

        	if ( ! $upload->upload() ) {
            	exit('{"jsonrpc" : "2.0","error" : {"code": 102, "message": "' . $upload->getErrorMsg() . '"}, "id" : "id"}');
            } else {
            	file_put_contents($sougouDir . '/lasttime.tmp', time());
            	exit('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
            }
		}

		
		$dictFiles = array();
		if(file_exists($sougouDir)) {
			if(is_writable($sougouDir)) {
				$oDir = dir($sougouDir);
				while (($file = $oDir->read()) !== false) {
					if(strcasecmp($file, '.') != 0 && strcasecmp($file, '..') && is_file($sougouDir . '/' . $file)) {
						$dictFiles[] = $file;
					}
				}			
			} else {
				exit('目录不可写');
			}
		} 

		$count = count($dictFiles);
		$page = isset($_REQUEST['page']) ? ($_REQUEST['page'] ? $_REQUEST['page'] : 1) : 1; 
		$pageSize = 10;
		$start = ($page - 1) * $pageSize;

		$pager = new \yii\data\Pagination(array('defaultPageSize' => $pageSize,'totalCount' => $count));
		if($page > $pager->getPageCount() && $page != 1) {
			exit('超过最大页数');
		}

		$data = array();	
		if($count) {
			$end = min(($page - 1) + $pageSize, $count);
			for($i = $start; $i < $end; $i++) {
				if(preg_match('/\.scel/', $dictFiles[$i])) {
					$file = $sougouDir . '/' . $dictFiles[$i];
					$data[$i] = array(
						'filename' => mb_convert_encoding($dictFiles[$i], 'UTF-8', 'GBK,GB2312'),
						'filesize' => \app\common\XUtils::file_size_format(filesize($file)),
						'filetype' => filetype($file),
						'filetime' => date('Y-m-d H:i:s', filemtime($file)),
					);
				}
			}
		}

		if(Yii::$app->request->isPost)
			exit(json_encode(array('data' => $data, 'pager' => \yii\widgets\LinkPager::widget([
                'pagination' => $pager,
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
            ]))));

		return $this->render('uploaddict', array(
			'data' => $data,
			'pager' => $pager,
		));
	}	

	public function actionDeleteDict() {
		if(\Yii::$app->request->isGet) {
			$filename = $this->_getParam('file');
		} elseif(\Yii::$app->request->isPost) {
			$filename = $this->_getPost('file');
		}

		$sougouDir = ROOT_PATH . '/upload/dict/sougou';
		$filename = (array) $filename;
		if(!empty($filename)) {
			foreach($filename as $val) {
				@unlink($sougouDir . '/' . $val);
			}
		}

		$this->redirect('index.php?r=iadmin/tools/upload-dict');
	}

}