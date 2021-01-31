<?php

namespace app\commands;

use app\models\Domains;
use app\models\Platforms;
use app\models\Video;
use app\models\Youtube\PubSub;
use Yii;
use yii\console\Controller;

class PubSubController extends Controller
{
    /**
     * update all subscriptions
     */
    public function actionIndex()
    {
        $domains = Domains::find()->all();

        $platform = Platforms::find()
            ->where(['code' => 'youtube'])
            ->one();
        echo "PubSubHubBub start\n";
        if ($platform) {
            echo "Platform ID: " . $platform->id . "\n";
            $api = new PubSub();
            foreach ($domains as $domain) {
                $api->setCallbackUrl('http://stream.huntshowdown.info/yt-sub/' . $domain->id);

                $videos = Video::find()
                    ->where([
                        'domain_id' => $domain->id,
                        'platform_id' => $platform->id,
                    ])
                    ->all();

                foreach ($videos as $video) {
                    $time = time();
                    $resub = false;
                    echo "Channel ID: " . $video->channel_id . "\n";
                    if (!$video->sub_time) {
                        $resub = true;
                    } else {
                        if ($time - (int)$video->sub_time >= Yii::$app->params['pubsubhubbubInterval']) {
                            $resub = true;
                        }
                    }

                    if ($resub) {
                        echo 'Created subscription for channel ' . $video->channel_id . "\n";
                        $api->createSubscription($video->channel_id);
                        $video->sub_time = (string)$time;
                        $video->save();
                    }
                }
            }
        }
    }
}