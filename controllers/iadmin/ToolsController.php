<?php

namespace app\controllers\iadmin;

use Yii;

class ToolsController extends \app\common\AdminBaseController {

	public function actionUploadDict() {
		$sougouDir = Yii::$app->params['sougouDictDir'];
		if(Yii::$app->request->isPost) {
			ini_set('upload_tmp_dir', ROOT_PATH . '/upload/upload_tmp_dir/');
			$upload = new \app\common\UploadFile();
			$upload->allowExts = array('scel');
			$upload->autoSub = true;
			$upload->subType = 'date';
			$upload->saveRule = '';
			$upload->savePath = $sougouDir . '/';

        	if ( ! $upload->upload() ) {
            	exit('{"jsonrpc" : "2.0","error" : {"code": 102, "message": "' . $upload->getErrorMsg() . '"}, "id" : "id"}');
            } else {
            	// file_put_contents($sougouDir . '/lasttime.tmp', time());
            	exit('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
            }
		}

		
		$dictFiles = array();
		if(file_exists($sougouDir)) {
			if(is_writable($sougouDir)) {
				$oDir = dir($sougouDir);
				while (($file = $oDir->read()) !== false) {
					if(strcasecmp($file, '.') != 0 && strcasecmp($file, '..') != 0) {					
						$filename = $sougouDir . '/' . $file;
						if(is_dir($filename)) {						
							$subDir = dir($filename);
							while (($subFile = $subDir->read()) !== false) {
								if(strcasecmp($subFile, '.') != 0 && strcasecmp($subFile, '..') != 0) {
									$dictFiles[$file][] = $subFile;
								}
							}
						}
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
			$sArr = array_slice($dictFiles, $start, $end, true);

			foreach($sArr as $time => $value) {
				if(!empty($value)) {
					foreach($value as $key => $val) {
						if(preg_match('/\.scel/', $val)) {
							$file = $sougouDir . '/' . $time . '/' . $val;
							$data[$time][$key] = array(
								'filename' => mb_convert_encoding($val, 'UTF-8', 'GBK,GB2312'),
								'filesize' => \app\common\XUtils::file_size_format(filesize($file)),
								'filetype' => filetype($file),
								'filetime' => date('Y-m-d H:i:s', filemtime($file)),
							);
						}					
					}
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
			$dirname = $this->_getParam('dir');
		} elseif(\Yii::$app->request->isPost) {
			$filename = $this->_getPost('file');
			$dirname = $this->_getPost('dir');
		}

		$sougouDir = Yii::$app->params['sougouDictDir'];
		if(!empty($dirname)) {
			$dirname = (array) $dirname;
			if(!empty($dirname)) {
				foreach($dirname as $val) {
					$this->delDir($sougouDir . '/' . $val);
				}
			}	
		} elseif(!empty($filename)) {
			$filename = (array) $filename;
			if(!empty($filename)) {
				foreach($filename as $val) {
					if(is_file($sougouDir . '/' . $val))
						@unlink($sougouDir . '/' . $val);
				}
			}			
		}


		$this->redirect('index.php?r=iadmin/tools/upload-dict');
	}

	public function delDir($dir) {
		//先删除目录下的文件：
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if($file !="." && $file!=".." ) {
				$fullpath = $dir . "/" . $file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					$this->deldir($fullpath);
				}
			}
 		}
		closedir($dh);
		//删除当前文件夹：
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
 	}
}