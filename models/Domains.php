<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "domains".
 *
 * @property int $id
 * @property string $domain
 * @property string $name
 * @property string $token
 * @property string $steam_game_id
 * @property string $stream_prefix
 * @property string $prefix_usage
 * @property int $use_grid
 */
class Domains extends \yii\db\ActiveRecord
{
    public $platformPrefixSelection = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domains';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain', 'name'], 'required'],
            [['use_grid'], 'integer'],
            [['domain', 'name', 'token', 'steam_game_id', 'stream_prefix', 'prefix_usage'], 'string', 'max' => 255],
            [['platformPrefixSelection'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => 'Domain',
            'name' => 'Name',
            'token' => 'Token',
            'steam_game_id' => 'Game',
            'stream_prefix' => 'Stream prefix',
            'prefix_usage' => 'Prefix usage',
            'platformPrefixSelection' => 'Where to use stream prefix',
            'use_grid' => 'Use grid',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            if($this->token == null) {
                $this->token = Yii::$app->security->generateRandomString();
            }

            if($this->platformPrefixSelection == "") {
                $this->platformPrefixSelection = [];
            }
            $this->prefix_usage = implode("|", $this->platformPrefixSelection);
            return true;

        } else {
            return false;
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        $platformMap = ArrayHelper::map(Platforms::find()->all(), 'code', 'name');

        $this->platformPrefixSelection = [];

        $platforms = explode('|', $this->prefix_usage);

        foreach($platforms as $id) {
            if(ArrayHelper::keyExists($id, $platformMap)) {
                $this->platformPrefixSelection[] = $platformMap[$id];
            }
        }

        return true;
    }
}
