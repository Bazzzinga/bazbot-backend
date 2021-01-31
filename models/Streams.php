<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "streams".
 *
 * @property int $id
 * @property int $platform_id
 * @property int $domain_id
 * @property string $channel
 * @property string $name
 * @property int $priority
 */
class Streams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'streams';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['platform_id', 'channel', 'domain_id', 'name'], 'required'],
            [['platform_id', 'priority', 'domain_id'], 'integer'],
            [['channel', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => 'Domain',
            'platform_id' => 'Platform',
            'channel' => 'Channel',
            'name' => 'Name',
            'priority' => 'Priority',
        ];
    }

    public function getPlatform()
    {
        return $this->hasOne(Platforms::className(), ['id' => 'platform_id']);
    }

    public function getDomain()
    {
        return $this->hasOne(Domains::className(), ['id' => 'domain_id']);
    }
}
