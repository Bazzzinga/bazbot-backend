<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "last_video".
 *
 * @property int $id
 * @property string $video_id
 * @property string $channel
 * @property int $platform_id
 * @property string $data
 * @property int $domain_id
 * @property int $shown
 */
class LastVideo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'last_video';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['video_id', 'channel', 'platform_id', 'domain_id'], 'required'],
            [['platform_id', 'domain_id', 'shown'], 'integer'],
            [['data'], 'string'],
            [['video_id', 'channel'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'Video ID',
            'channel' => 'Channel',
            'platform_id' => 'Platform ID',
            'shown' => 'Показано',
        ];
    }

    public static function updateLastVideo($channelId, $videoId, $platformId, $channelName, $videoName, $url, $domainId)
    {
        $old = static::find()
            ->where([
                'platform_id' => $platformId,
                'channel' => $channelId,
                'domain_id' => $domainId,
            ])
            ->one();

        if ($old) {
            if ($videoId != $old->video_id) {
                $old->video_id = $videoId;
                $old->shown = 0;
                $old->data = Json::encode([
                    'id' => $videoId,
                    'title' => $videoName,
                    'channelTitle' => $channelName,
                    'keywords' => '',
                    'url' => $url,
                ]);

                $old->save();
            }
        } else {
            $new = new self();

            $new->platform_id = $platformId;
            $new->channel = $channelId;
            $new->video_id = $videoId;
            $new->domain_id = $domainId;
            $new->shown = 0;
            $new->data = Json::encode([
                'id' => $videoId,
                'title' => $videoName,
                'channelTitle' => $channelName,
                'keywords' => '',
                'url' => $url,
            ]);
            $new->save();
        }
    }
}
