<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discord".
 *
 * @property int $id
 * @property int $total
 * @property int $online
 * @property int $domain_id
 */
class Discord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discord';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total', 'online', 'domain_id'], 'required'],
            [['total', 'online', 'domain_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total' => 'Total',
            'online' => 'Online',
            'domain_id' => 'Domain ID',
        ];
    }
}
