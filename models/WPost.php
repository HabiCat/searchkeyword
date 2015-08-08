<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "w_post".
 *
 * @property string $id
 * @property string $subject
 * @property string $keywords
 * @property string $createtime
 * @property string $url
 * @property string $description
 * @property string $url_code
 * @property string $cid
 */
class WPost extends \app\models\BaseModel
{
    public $searchName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'url', 'keywords'], 'required'],
            [['subject', 'url', 'keywords', 'description'], 'string'],
            ['url', 'match', 'pattern' => '/((http|ftp|https):\/\/)(([a-zA-Z0-9\._-]+\.[a-zA-Z]{2,6})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,4})*(\/[a-zA-Z0-9\&%_\.\/-~-]*)?/'], 
            ['keywords', 'keywords_validation'],
        ];
    }

    public function keywords_validation($attribute, $params){
        $keywordsExp = explode(',', $this->$attribute);
        if(count($keywordsExp) > 7) {
            $this->addError($attribute, '关键词不能超过7个');
        } else {
            foreach($keywordsExp as $key => $value) {
                $utf8Str = mb_convert_encoding($value, 'UTF-8', array('GBK', 'GB2312'));
                if(\app\common\XUtils::utf8_strlen($utf8Str) >= 12) {
                    $this->addError($attribute, '每个关键词长度不能超过12个字符');
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'keywords' => 'Keywords',
            'createtime' => 'Createtime',
            'url' => 'Url',
            'description' => 'Description',
            'url_code' => 'Url Code',
            'cid' => 'Cid',
        ];
    }

    public function beforeSave ($insert){
        parent::beforeSave($insert);
        
        $this->url_code = \app\common\XUtils::shorturl($this->url);
        $this->description = \app\common\XUtils::ihtmlspecialchars($this->description);
        $this->createtime = time();
        
        return true;

        // $fp = fopen(ROOT_PATH . '/sql.txt', 'w');
        // fwrite($fp, $this->createCommand()->getRawSql());//
        // fclose($fp);
    }

    // public function afterSave($insert, $changedAttributes) {
    //     parent::afterSave($insert);
    // }


    public function getPostListByPage($start, $pagesize, $where = 1) {
        $list = self::find()->select('id, subject, keywords, url_code')->where($where)->orderBy('createtime desc, id desc')->limit($pagesize)->offset($start)->all();
        return ['list' => $list, 'n' => self::find()->where($where)->count('id')];
    }
}
