<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "w_admin".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $group_id
 * @property string $create_time
 * @property string $last_login_time
 * @property string $email
 * @property string $mobile
 */
class WAdmin extends \yii\db\ActiveRecord {

    public $repassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'repassword', 'group_id'], 'required'],
            ['username', 'unique', 'message' => '此用户名已被使用'],  
            [['repassword'], 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'skipOnEmpty' => true],
            ['email', 'match', 'pattern' => '/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i', 'message' => '邮箱格式不正确'],
            ['mobile', 'match', 'pattern' => '/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$/', 'message' => '手机号码格式不正确'],
        ];
    }

    /**
     * 验证邮箱格式是否正确
     * @param  [type]  $attribute [description]
     * @return boolean            [description]
     */
    // public function isEmail($attribute, $params) {
    //     $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    //     if (!strpos( $this->$attribute, '@') !== false && strpos($this->$attribute, '.') !== false ) {
    //         $this->addError($attribute, '邮箱格式不正确');
    //     } else {
    //         if (!preg_match( $chars, $this->$attribute)) {
    //             $this->addError($attribute, '邮箱格式不正确');
    //         }
    //     }
    // }

    /**
     * 验证手机号码格式是否正确
     * @param  [type]  $attribute [description]
     * @return boolean            [description]
     */
    // public function isMobile($attribute, $params) {
    //     if(!preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $this->$attribute)) {
    //         $this->addError($attribute, '手机号码格式不正确');
    //     }
    // }


    /**
     * 入库前自动处理
     */
    public function beforeSave ($insert){
        parent::beforeSave($insert);

        if(trim($this->password)) {
            $this->password = md5($this->password);
        }

        $this->create_time = time();
        $this->last_login_time = time();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'repassword' => '确认密码',
            'group_id' => '用户组',
            'email' => 'Email',
            'mobile' => '手机号码',
        ];
    }

    /*public function getAdminGroup(){
        return $this->hasMany(WAdminGroup::className(), ['id' => 'group_id']);
    }*/

    /**
     * 获取所有用户数据
     * @param  [type] $start    [description]
     * @param  [type] $pageSize [description]
     * @param  [type] $where    [description]
     * @return [type]           [description]
     */
    public function getAdminList($start, $pageSize, $where) {
        $connection = Yii::$app->db;
        $sqlOne = 'select a.id, a.username, a.last_login_time, g.group_name from ' . WAdmin::tableName() . ' as a left join ' . WAdminGroup::tableName() . ' as g on a.group_id = g.id where ' . $where . ' order by a.last_login_time desc, a.id asc limit '. $start . ',' . $pageSize;
        $sqlTwo = 'select count(a.id) as n from ' . WAdmin::tableName() . ' as a left join ' . WAdminGroup::tableName() . ' as g on a.group_id = g.id where ' . $where;

        return array('data' => $connection->createCommand($sqlOne)->queryAll(), 'count' => $connection->createCommand($sqlTwo)->queryOne());

    }

    /**
     * 是否存在用户
     * @param  [type]  $aid [description]
     * @return boolean      [description]
     */
    public function isAdminExist($id) {
        if(((int) $id) > 0) {
            $className = self::className();
            return $className::find()->andWhere(['id' => $id])->count('id');
        }
    }

    /**
     * 获取指定用户信息
     * @param  [type] $id     [description]
     * @param  string $select [description]
     * @return [type]         [description]
     */
    public function getSingleAdminInfo($id, $select = '*') {
        $className = self::className();
        return $className::find()->select($select)->where(['id' => $id])->one();
    }

    /**
     * 通过用户名查找
     * @param  [type]  $str [description]
     * @return boolean      [description]
     */
    public function isAdminNameExist($str) {
        $className = self::className();
        return $className::find()->andWhere(['username' => $str])->count('id');
    }

    /**
     * 验证用户信息
     * @param  array  $array [description]
     * @return [type]        [description]
     */
    public function writeDataValidate($array = array()) {
        if(!empty($array)) {

            foreach($array as $k => $v) {
                $this->$k = $v;
            }

            $status = array();
            if($array['username']) {
                $status['username'] = 1;
            } else {
                $status['username'] = 0;
                $this->addError('username', '用户名必填');                  
            }

            if(trim($array['password']) && trim($array['repassword'])) {
                if($array['password'] == $array['repassword']) {
                    $array['password'] = md5($array['password']);
                    $status['password'] = 1;
                } else {
                    $status['password'] = 0;
                    $this->addError('repassword', '两次密码不匹配');                   
                }
            } elseif(!$array['password'] && !$array['repassword']) {
                unset($array['password']);
                unset($array['repassword']);
                $status['password'] = 1;
            } else {
                unset($array['password']);
                unset($array['repassword']);
                $status['password'] = 0;
                $this->addError('repassword', '两次密码不匹配');               
            }

            if((int) $array['group_id'] > 0) {
                $status['group_id'] = 1;                           
            } else {
                $status['group_id'] = 0;
                $this->addError('group_id', '必须选择用户组'); 
            }

            if($array['email']) {
                if(preg_match('/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i', $array['email'])) {
                    $status['email'] = 1;
                } else  {
                    unset($array['email']);
                    $status['email'] = 0;
                    $this->addError('email', 'Email格式不正确');
                }
            }

            if($array['mobile']) {
                if(preg_match('/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$/', $array['mobile'])) {
                    $status['mobile'] = 1;
                } else {
                    unset($array['mobile']);
                    $status['mobile'] = 0;
                    $this->addError('mobile', '手机号码格式不正确');
                }
            }

            $sum = 1;
            foreach($status as $v) {
                $sum *= $v;
            }

            if($sum > 0) {
                return $array;
            }
            return array();
       }     
    }

    /**
     * 更新用户信息
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function updateAdminInfo($array) {
        $connection = Yii::$app->db;
        $condintion = \app\common\CController::buildQuery($array, ',');

        $sql = 'update ' . self::tableName() . ' set ' . $condintion . ' where id="' . $array['id'] . '"';
        return $connection->createCommand($sql)->execute();       
    }

    public function deleteAdminRecord($id) {
        // $connection = Yii::$app->db;
        // $status = $connection->createCommand()->delete(self::tableName(), 'id in (' . $id . ')')->execute();
        $className = self::className();       
        $status = $className::deleteAll('id in (' . $id .')');
         if($status)
             return true;
    }

}
