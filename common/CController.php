<?php
/**
 *
 * 基础类控制器，所有控制器必须继承此类
 * 
 */

namespace app\common;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\web\Cookie;

class CController extends Controller {

    public $layout = 'front';
    public $enableCsrfValidation = false;
    protected $_gets;
    protected $_baseUrl;
    protected $_session;
    protected $_cookies;


    /**
	 * 初始化
	 */
    public function init () {
        $this->_session = Yii::$app->session;
        $this->_session->open();
        $this->_cookies = Yii::$app->response->cookies;
        $this->_gets = Yii::$app->request;
        $this->_baseUrl = Yii::$app->getUrlManager()->getBaseUrl();
    }

    /**
     * 设置cookie
     * @param  string  $name   [description]
     * @param  string  $value  [description]
     * @param  integer $expire [description]
     * @param  string  $path   [description]
     * @param  string  $domain [description]
     * @param  boolean $secure [description]
     * @return [type]          [description]
     */
    protected function _cookiesSet ($name = '', $value = '', $expire = 3600, $path = '', $domain = '', $secure = false)
    {
        $cookieSet = new Cookie(array(
        	'name' => $name, 
        	'value' => $value,
        	'expire' => $secure,
        	'path' => $path,
        	'domain' => $domain,
        	'secure' => $secure,
        ));
        $this->_cookies->add($cookieSet);
    }

    /**
     * 获取cookie，同时获取默认值
     * @param  [type]  $name  [description]
     * @param  string  $value [description]
     * @return [type]         [description]
     */
    protected function _cookiesGet ($name, $value = '') {

    	if($value)
    		$data = $this->_cookies->get($name, $value);
    	else
    		$data = $this->_cookies->get($name);

        return $data;
    }

    /**
     * 删除cookie
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function _cookiesRemove ($name){
		$this->_cookies->remove($name);
    }

    /**
     * 设置session
     * @param  [type]  $name   [description]
     * @param  string  $value  [description]
     * @return [type]          [description]
     */
    protected function _sessionSet ($name, $value = ''){
        $this->_session[$name] = $value;
    }

    /**
     * 获取session
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function _sessionGet ($name) {
        return $this->_session[$name];
    }

    /**
     * 删除session
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function _sessionRemove ($name) {
        $this->_session->remove($name);
    }

    /**
     * 重用方法
     * @return [type] [description]
     */
    public function actions (){
        return [
        	'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 1 ,
                'maxLength' => 5 ,
                'backColor' => 0xFFFFFF ,
                'width' => 100 ,
                'height' => 40 
            ],
        ];
    }

    public function _getParam($key) {
    	return $this->_gets->get($key);
    }

     public function _getPost($key) {
    	return $this->_gets->post($key);
    }
   

    /**
     * 构建条件语句
     * @param  array  $arrayStr  [description]
     * @param  string $condition 选项为'and' 或者 'or'
     * @param  string $connect   判断作为where子句构建还是作为update子句构建, 1为where, 2为update
     * @return [type]            [description]
     */
    public static function buildQuery($arrayStr, $condition = 'and', $connect = 1) {
        if(is_array($arrayStr)) {
            $switchArr = '';
            $n = 1;
            if($connect === 1) {
                foreach($arrayStr as $key => $value) {
                    if($n < count($arrayStr)) {
                        $switchArr .= $key . '="' . $value . '" ' . $condition . ' ';
                    } elseif ($n == count($arrayStr)) {
                        $switchArr .= $key . '="' . $value . '"';
                    }
                    $n++;
                }
            } elseif ($connect === 2) {
                $switchArr = array();
                foreach($arrayStr as $key => $value) {
                    array_push($switchArr, $key . '="' . $value . '"');
                }
                $switchArr = implode(',', $switchArr);
            }
            return $switchArr;
        }
    }

    /**
     * insert操作
     * @param  [type] $mode     模型实例
     * @param  array  $arrayStr [description]
     * @return [type]           [description]
     */
    public static function buildInsert($mode, array $arrayStr) {
        if(is_object($mode)) {
            if(is_array($arrayStr)) {
                $mode->attributes = $arrayStr;

                if($mode->validate()) {
                    if($mode->save()) {
                        return 1;
                    } else {
                        return -1;
                    }
                } 
            }
        }   
    } 

    /**
     * update 操作
     * @param  [type] $id       要Update的记录ID
     * @param  [type] $model     [description]
     * @param  array  $arrayStr [description]
     * @return [type]           [description]
     */
    public static function buildUpdate($id, $model, array $arrayStr) {
        if(is_object($model)) {
            $modelClass = get_class($model);
            $model = $modelClass::findOne($id);
            if($model !== null) {
                if(is_array($arrayStr)) {
                    foreach($arrayStr as $key => $value) {
                        $model->$key = $value;
                    }

                    if($model->validate()) {
                        if($model->save()) {
                            return 1;
                        } else {
                            return -1;
                        }
                    } else {
                        return -1;
                    }
                    
                }
            } else {
                throw new NotFoundHttpException;
            }
        }       
    }


    /**
     * delete操作
     * @param  [type] $id   要Delete的记录ID
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    public static function buildDelete($id, $mode) {
        if(is_object($mode)) {
            $modeClass = get_class($mode);
            $mode = $modeClass::findOne($id);
            if($mode !== null) {
                if($mode->delete()) {
                    return 1;
                } else {
                    return -1;
                }
            }
        }       
    }

    /**
     * 格式化输出
     * @param  [type]  $vars   [description]
     * @param  string  $label  [description]
     * @param  boolean $return [description]
     * @return [type]          [description]
     */
    public function dump($vars, $label = '', $return = false) {
        if (ini_get('html_errors')) {
            $content = "<pre>\n";
            if ($label != '') {
                $content .= "<strong>{$label} :</strong>\n";
            }
            $content .= htmlspecialchars(print_r($vars, true));
            $content .= "\n</pre>\n";
        } else {
            $content = $label . " :\n" . print_r($vars, true);
        }
        if ($return) { return $content; }
        echo $content;
        return null;
    }
}