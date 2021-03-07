<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property string $slug
 * @property string $token
 * @property string $updated_at
 * @property int $life_time
 */
class Token extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'token';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['slug', 'token'], 'required'],
            [['life_time'], 'integer'],
            [['slug', 'token'], 'string', 'max' => 255],
            [['updated_at'], 'safe'],
            [['slug'], 'unique']
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'Идентификатор',
            'slug' => 'Внутреннее название сервиса',
            'token' => 'Токен',
            'updated_at' => 'Дата обновления',
            'life_time' => 'Время жизни токена'
        ];
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}