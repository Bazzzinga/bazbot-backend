<?php

namespace app\models\Steam;

use Yii;

/**
 * This is the model class for table "steam_online".
 *
 * @property int $id
 * @property string $game
 * @property int $value
 * @property string $date
 */
class SteamOnline extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'steam_online';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['game', 'value'], 'required'],
            [['value'], 'string'],
            [['date'], 'safe'],
            [['game'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'game' => 'Game',
            'value' => 'Value',
            'date' => 'Date',
        ];
    }
}
