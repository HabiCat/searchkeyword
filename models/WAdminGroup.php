<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "w_admin_group".
 *
 * @property string $id
 * @property string $group_name
 * @property string $group_options
 */
class WAdminGroup extends \app\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_admin_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['group_name', 'required'],
            [['group_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_name' => 'Group Name',
            'group_options' => 'Group Options',
        ];
    }

    /**
     * 取全部用户组
     * @return [type] [description]
     */
    public function getBaseAdminGroupList() {
        $connection = Yii::$app->db;
        $sql = 'select * from ' . self::tableName() . ' where id <> 1 order by id asc';
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

    /**
     * 获取管理用户中的下拉列表
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function getDropDownList($array) {
        $groupDropDownList = array();
        foreach($array as $value) {
            $groupDropDownList[$value['id']] = $value['group_name'];
        }

        return $groupDropDownList;
    }

    /**
     * 获取所有用组数据
     * @param  [type] $start    [description]
     * @param  [type] $pageSize [description]
     * @param  [type] $where    [description]
     * @return [type]           [description]
     */
    public function getAdminGroupList($start, $pageSize, $where) {
        $connection = Yii::$app->db;
        $sqlOne = 'select id, group_name from ' . self::tableName() . ' where ' . $where . ' order by id asc limit '. $start . ',' . $pageSize;
        $sqlTwo = 'select count(id) as n from ' . self::tableName() . ' where ' . $where;
        return array('data' => $connection->createCommand($sqlOne)->queryAll(), 'count' => $connection->createCommand($sqlTwo)->queryOne());
    }

    /**
     * 获取用户组权限
     * @return [type] [description]
     */
    public function getUserPower($id) {
        $className = self::className();
        return $className::find()->where(['id' => $id])->one();
    }

}
