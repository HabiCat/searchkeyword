<?php
namespace app\models;

use Yii;

class BaseModel extends \yii\db\ActiveRecord {

	/**
	 * 是否存在记录
	 * @param  [type]  $id         ID
	 * @param  [type]  $whereArray 条件数组
	 * @param  [type]  $fieldset   统计字段
	 * @return boolean             [description]
	 */
    public function isExist($whereArray, $fieldset) {
        $className = self::className();
        return $className::find()->andWhere($whereArray)->count($fieldset);
    }

	/**
	 * 删除记录
	 * @param  [type] $where 条件
	 * @return [type]        [description]
	 */
	public function deleteRecord($where) {
        $className = self::className();
        $status = '\\' . $className::deleteAll($where);
         if($status)
             return true;
	}
}