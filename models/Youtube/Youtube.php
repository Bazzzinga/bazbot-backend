<?php


namespace app\models\Youtube;

use Yii;
use app\models\Youtube\EmbedYoutubeLiveStreaming;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Youtube implements \app\models\StreamCheckInterface
{
    private $apiKey = "AIzaSyCDQIqHeaBzuNE0GpvO7SiUuhql609GRiA";//"AIzaSyBka1SA1q_hd-xGlz0QbFLKnBUr9XJqQt0";

    private $cacheKey = 'yt_cache';

    private $cacheLifetime = 600;

    private $cacheVideoLifetimeMultiplier = 10;

    public function checkStatus($channel, $title = "", $game = "")
    {
        return $this->checkLive($channel, $title);
    }

    public function embedVideo($channel, $type = "big")
    {
        $YTL = $this->getStreamObject($channel);

        $res = '<div class="youtube_big">';
        $res.= $YTL->embed_code;
        $res.= '</div>';

        return $res;
    }

    public function embedChat($channel, $domain, $hidden = false)
    {
        $YTL = $this->getStreamObject($channel);
        $res = '<div class="twitch_chat"><iframe frameborder="0" scrolling="no" src="https://www.youtube.com/live_chat?v=' . $YTL->live_video_id . '&embed_domain=' . $domain . '" id="yt_' . $channel . '_chat" ';
        if($hidden) {
            $res .= ' style="display:none;" ';
        }
        $res .= "></iframe></div>";

        return $res;
    }

    public function embedCss($type = 0)
    {
        $style_block = "";
        return $style_block;
    }

    public function getLastVideo($channel)
    {
        //TODO CHANGE
        return Yii::$app->cache->getOrSet(
            [$this->cacheKey, 'lastVideo', $channel],
            function() use ($channel) {
                return $this->lastPostedVideo($channel);
            },
            $this->cacheLifetime * $this->cacheVideoLifetimeMultiplier
        );
    }

    public function getVideoId($channel)
    {
        $YTL = $this->getStreamObject($channel);
        return $YTL->live_video_id;
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

    private function apiCall($channel)
    {
        $YTL = new EmbedYoutubeLiveStreaming($channel, $this->apiKey, 'yt_' . $channel . '_player');
        return $YTL;
    }

    private function checkLive($channel, $title = "")
    {
        $YTL = $this->getStreamObject($channel);

        if($YTL->isLive) {
            $vid_name = $YTL->live_video_title;

            if($title == "")
                return $vid_name;

            if((strpos($vid_name, $title) !== false) && (strpos($vid_name, $title) == 0))
                return $vid_name;

            return false;
        }

        return false;
    }

    private function getVideoKeywords($video_id) {
        $res = file_get_contents("https://www.youtube.com/watch?v=" . $video_id);

        $search1 = "ytplayer.config = ";
        $search2 = ";ytplayer.load";

        $p1 = strpos($res, $search1);
        $p2 = strpos($res, $search2, $p1);

        $res = substr($res, $p1 + strlen($search1), $p2 - $p1 - strlen($search1) );

        $res = Json::decode($res);
        $res = Json::decode($res["args"]["player_response"]);

        return $res;
    }

    private function lastPostedVideo($channel)
    {
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet";
        $url.= "&channelId=" . $channel;
        $url.= "&maxResults=1";
        $url.= "&order=date";
        $url.= "&type=video";
        $url.= "&key=" . $this->apiKey;

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        $response = Json::decode($response);

        if(ArrayHelper::keyExists('items', $response)) {
            $videos = $response['items'];

            if(count($videos) > 0) {
                $video = $videos[0];

                $videoData = [
                    'id' => $video['id']['videoId'],
                    'title' => $video['snippet']['title'],
                    'channelTitle' => $video['snippet']['channelTitle'],
                    'keywords' => $this->getVideoKeywords($video['id']['videoId'])
                ];

                $videoData['url'] = 'https://www.youtube.com/watch?v=' . $videoData['id'];

                return $videoData;
            }
        }

        return null;
    }
}