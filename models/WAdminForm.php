<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class WAdminForm extends Model
{
    public $username;
    public $password;
    public $repassword;
    public $groud_id;
    public $email;
    public $mobile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'repassword'], 'required'],
            [['username', 'password'], 'string', 'max' => 255],
            [['repassword'], 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'skipOnEmpty' => true],
            ['email', 'isEmail'],
            ['mobile', 'isMobile'],
        ];
    }

    /**
     * 验证邮箱格式是否正确
     * @param  [type]  $attribute [description]
     * @return boolean            [description]
     */
    public function isEmail($attribute, $params) {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (!strpos( $this->$attribute, '@') !== false && strpos($this->$attribute, '.') !== false ) {
            $this->addError($attribute, '邮箱格式不正确');
        } else {
            if (!preg_match( $chars, $this->$attribute)) {
                $this->addError($attribute, '邮箱格式不正确');
            }
        }
    }

    /**
     * 验证手机号码格式是否正确
     * @param  [type]  $attribute [description]
     * @return boolean            [description]
     */
    public function isMobile($attribute, $params) {
        if(!preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $this->$attribute)) {
            $this->addError($attribute, '手机号码格式不正确');
        }
    }


    /**
     * 入库前自动处理
     */
    public function beforeSave ($insert){
        parent::beforeSave();

        $this->password = md5($this->password);

        $this->create_time = time();
        $this->last_login_time = time();
        return true;
    }

    /**
     * @return array customized attribute labels
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
    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string  $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        } else {
            return false;
        }
    }
}
