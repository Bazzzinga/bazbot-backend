<?php


namespace app\models\Goodgame;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Goodgame implements \app\models\StreamCheckInterface
{
    //http://api2.goodgame.ru/streams/Pomi

    private $apiUrl = 'http://api2.goodgame.ru/streams/';

    private $cacheKey = 'gg_cache';

    private $cacheLifetime = 60;

    private function apiCall($channel)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->apiUrl . $channel
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return Json::decode($response);
    }

    private function getStreamObject($channel)
    {
        return Yii::$app->cache->getOrSet(
            [$this->cacheKey, $channel],
            function() use ($channel) {
                return $this->apiCall($channel);
            },
            $this->cacheLifetime
        );
    }

    public function checkStatus($channel, $title = "", $game = "")
    {
        $response_decoded = $this->getStreamObject($channel);

        if(ArrayHelper::keyExists('status', $response_decoded)) {
            return $response_decoded['status'] == 'Live';
        }

        return false;
    }

    public function embedVideo($channel, $type)
    {
        $response_decoded = $this->getStreamObject($channel);

        if(!ArrayHelper::keyExists('channel', $response_decoded)) {
            $channel = $response_decoded['channel'];
            if(!ArrayHelper::keyExists('embed', $response_decoded)) {
                return $channel['embed'];
            }
        }

        return '';
    }

    public function embedChat($channel, $domain, $hidden)
    {
        return '<iframe src="https://https://goodgame.ru/chat/' . $channel . '/" frameborder="0" scrolling="no" allowfullscreen></iframe>';
    }

    public function embedCss($type = 0)
    {
        return "";
    }

    public function getLastVideo($channel)
    {
        return "";
    }

    public function getVideoId($channel)
    {
        return "";
    }
}
