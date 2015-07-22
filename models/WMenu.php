<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "w_menu".
 *
 * @property string $id
 * @property string $menu_title
 * @property string $menu_url
 * @property string $menu_acl
 * @property string $pid
 */
class WMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_title', 'menu_url', 'menu_acl', 'pid', 'type'], 'required'],
            [['pid'], 'integer'],
            [['menu_title', 'menu_url', 'menu_acl'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_title' => 'Menu Title',
            'menu_url' => 'Menu Url',
            'menu_acl' => 'Menu Acl',
            'pid' => 'Pid',
        ];
    }

    /**
     * 获取所有菜单项
     * @param  integer $where [description]
     * @return [type]         [description]
     */
    public function getAllMenus($where = 1) {
        $connection = Yii::$app->db;
        $sql = 'select * from ' . self::tableName() . ' where ' . $where . ' order by sort asc, id asc';
        return $connection->createCommand($sql)->queryAll();       
    }

    /**
     * 获取格式化以后的菜单，用于分配权限
     * @return [type] [description]
     */
    public function getAllFormatMenus() {
        $data = $this->getAllMenus();
        if(!empty($data)) {
            $filterData = array();

            foreach($data as $pkey => $primaryValue) {
                if($primaryValue['pid'] == 0) {
                    $filterData[$pkey] = $primaryValue;
                    foreach($data as $skey => $secondValue) {
                        if($secondValue['pid'] == $primaryValue['id']) {
                            $filterData[$pkey]['submenu'][$skey] = $secondValue;
                            foreach($data as $tkey => $threeValue) {
                                if($threeValue['type'] == 1 && $threeValue['pid'] == $secondValue['id'])
                                $filterData[$pkey]['submenu'][$skey]['ops'][] = $threeValue;
                            }
                        }
                    }
                }
                
            }
            return $filterData;
        }
    }

    /**
     * 格式化菜单标题
     * @param  integer $pid     [description]
     * @param  integer $level   [description]
     * @param  array   $options [description]
     * @param  string  $repeat  [description]
     * @return [type]           [description]
     */
    public function getMenuListOptions($menus, $pid = 0, $level = 0, $options = array(), $repeat = '----') {
        $flag = '';
        if($level) {
            $flag = str_repeat($repeat, $level);
        }
        if(!empty($menus)) {
            foreach($menus as $k => $v) {
                if($v['pid'] == $pid) {
                    $v['menu_title'] = $flag . $v['menu_title'];
                    $options[] = $v;
                    $options = $this->getMenuListOptions($menus, $v['id'], $level + 1, $options);
                }
            }

            return $options;
        }
    }

    /**
     * 获取菜单列表
     * @param  [type] $start    [description]
     * @param  [type] $pageSize [description]
     * @param  [type] $where    [description]
     * @return [type]           [description]
     */
    public function getMenuArray($start, $pageSize, $where) {
        $connection = Yii::$app->db;
        $sqlOne = 'select * from ' . self::tableName() . ' where ' . $where . ' order by sort asc, id asc limit '. $start . ',' . $pageSize;
        $res = $connection->createCommand($sqlOne)->queryAll();
        $res = $this->getMenuListOptions($res);
        
        $sqlTwo = 'select count(id) as n from ' . self::tableName() . ' where ' . $where;
        return array('data' => $res, 'count' => $connection->createCommand($sqlTwo)->queryOne());
    }

    /**
     * 判断有无此菜单
     * @return boolean [description]
     */
    public function isMenuExist($id) {
        if(((int) $id) > 0) {
            $className = self::className();
            return $className::find()->andWhere(['id' => $id])->count('id');
        }
    }
}
