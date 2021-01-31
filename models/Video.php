<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "video".
 *
 * @property int $id
 * @property int $domain_id
 * @property int $platform_id
 * @property string $channel_id
 * @property string $sub_time
 */
class Video extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id', 'platform_id', 'channel_id'], 'required'],
            [['domain_id', 'platform_id'], 'integer'],
            [['channel_id', 'sub_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => 'Domain ID',
            'platform_id' => 'Platform ID',
            'channel_id' => 'Channel ID',
            'sub_time' => 'Дата подписки'
        ];
    }
}
