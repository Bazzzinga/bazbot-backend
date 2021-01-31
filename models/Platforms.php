<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "platforms".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 */
class Platforms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'platforms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
        ];
    }

    public static function getIdFromCode($code)
    {
        $platform = static::find()
            ->where(['code' => $code])
            ->one();

        if($platform != null) {
            return $platform->id;
        }

        return 0;
    }
}
