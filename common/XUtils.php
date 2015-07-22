<?php
namespace app\common;

use Yii;

class XUtils {

	/**
	 * 转义字符串
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	static public function ihtmlspecialchars($str) {
		if(is_array($str)) {
			foreach($str as $key => $value) {
				$str[$key] = self::ihtmlspecialchars($value);
			} 
		} else {
			$str = htmlspecialchars($str);
		}

		return $str;
	}


    /**
     * 提示信息
     */
    static public function message($action = 'success', $content = '', $redirect = '', $timeout = 3) {

        $url = $redirect;
        switch ($action) {
	        case 'success': 
	            $vars = array('titler'=>'操作完成', 'class'=>'success','status'=>'✔'); 
	            break;
	        case 'error': 
	            $vars = array('titler'=>'操作未完成', 'class'=>'error','status'=>'✘'); 
	            break;
	        case 'errorBack': 
	            $vars = array('titler'=>'操作未完成', 'class'=>'error','status'=>'✘'); 
	            break;
	        case 'redirect': 
	        	//header("Location:$url"); break;
	        case 'script':
	            exit('<script language="javascript">alert("' . $content . '");window.location=" ' . $url . '"</script>');
	            break;
        }
        if($action !='errorBack')
            $script = '<div class="go">系统自动跳转在 <span id="time">'.$timeout.'</span> 秒钟后，如果不想等待 > <a href="'.$redirect.'">点击这里跳转</a><script>function redirect(url) {window.location.href = url;} setTimeout("redirect(\''.$redirect.'\');",'.$timeout * 1000 .');</script>';
        else
            $script = '<a href="'.$url.'" >[点这里返回上一页]</a>';
        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>'.$vars['titler'].'</title><style type="text/css">body { font-size: 15px; font-family: "Tahoma", "Microsoft Yahei" }.wrap { background: #F7FBFE; border: 1px solid #DEEDF6; width: 650px; padding: 50px; margin: 50px auto 0; border-radius: 5px }h1 { font-size: 25px }div { padding: 6px 0 }div:after { visibility: hidden; display: block; font-size: 0; content: " "; clear: both; height: 0; }a { text-decoration: none; }#status, #content { float: left; }#status { height: auto; line-height: 50px; margin-right: 30px; font-size: 25pt }#content { float: left; width: 550px; }.message { color: #333; line-height: 25px }#time { color: #F00 }.error { color: #F00 }.success{color:#060}.go { font-size: 12px; color: #666 }</style></head><body><div class="wrap"><div id="status" class="'.$vars['class'].'">'.$vars['status'].'</div><div id="content"><div class="message '.$vars['class'].'">'.$content.'</div>'.$script.'</p></div></div></div></body></html>';
        exit ($body);
    }

}