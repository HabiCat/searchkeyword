<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "w_setting".
 *
 * @property string $keys
 * @property string $values
 */
class WSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['values'], 'required'],
            [['values'], 'string'],
            [['keys'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'keys' => 'Keys',
            'values' => 'Values',
        ];
    }
}
