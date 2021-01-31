<?php


namespace app\models\Twitch;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Twitch implements \app\models\StreamCheckInterface
{

    private $apiUrl = 'https://api.twitch.tv/helix/streams/?user_login=';


    private $clientIds = [
        'fu95xvzthq1acsopqj7ysxazgk7akl',   //secret e4j6jxvu66mhfvet19qrxn3kx14zs1
        //'j3xq35x6v63qlri4jr8wyazla18nl6',
        //'whyla4izgrj7rgzfilggwrcgjorm36',   // secret wxuoj9fnax949fen7glsbwv8ms1hbn
    ];

    /*
     * POST	 
     * https://id.twitch.tv/oauth2/token
	 *  ?client_id=fu95xvzthq1acsopqj7ysxazgk7akl
	 *  &client_secret=e4j6jxvu66mhfvet19qrxn3kx14zs1
	 *  &grant_type=client_credentials
	 *  &scope=viewing_activity_read     
     */

    private $tokens = [
        't7ygbuzptm9a8e11fmeoyuki7eywmu',
    ];

    private $cacheKey = 'tw_cache';

    private $cacheLifetime = 600;

    public function checkStatus($channel, $title = "", $game = "")
    {
        return $this->checkLive($channel, $game, $title);
    }

    public function embedVideo($channel, $type = "big", $parent = '')
    {
        $res = '';
        $res .= '<div class="twitch_' . $type . '">';
        $res .= '<iframe src="https://player.twitch.tv/?channel=' . $channel . '&autoplay=false&parent=' . $parent . '" frameborder="0" scrolling="no" allowfullscreen></iframe>';
        $res .= '</div>';
        return $res;
    }

    public function embedChat($channel, $domain = "", $hidden = false, $parent = '')
    {
        $res = '<div class="twitch_chat"><iframe frameborder="0" scrolling="no" src="https://twitch.tv/embed/' . $channel . '/chat?parent=' . $parent . '" id="tw_' . $channel . '_chat" ';
        if($hidden) {
            $res .= ' style="display:none;" ';
        }
        $res .= "></iframe></div>";

        return $res;
    }

    public function embedCss($type = 0)
    {
        return "";
    }

    public function getLastVideo($channel)
    {
        return null;
    }

    public function getVideoId($channel)
    {
        return "";
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
        $ch = curl_init();

        //$clientId = $this->clientIds[array_rand($this->clientIds, 1)];
        $clientId = $this->clientIds[0];
        //$token = $this->tokens[array_rand($this->tokens, 1)];
        $token = $this->tokens[0];

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Client-ID: ' . $clientId,
                'Authorization: ' . 'Bearer ' .  $token
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->apiUrl . $channel
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return Json::decode($response);
    }

    private function checkLive($channel, $game = "", $title = "")
    {
        $response_decoded = $this->getStreamObject($channel);
		
        $res = false;

        if(!ArrayHelper::keyExists('data', $response_decoded)) {
            return $res;
        }

        $response_decoded = $response_decoded['data'];

        if(!ArrayHelper::keyExists(0, $response_decoded)) {
            return $res;
        }

        $response_decoded = $response_decoded[0];

        if ( ArrayHelper::keyExists('type', $response_decoded) && $response_decoded['type'] == "live" ) {
            $vid_name = $response_decoded['title'];

            if($game == "")//500188 - hunt
                $res = $vid_name;
            else
                if(strval($response_decoded['game_id']) == strval($game))
                    $res = $vid_name;
                else
                    $res = false;

        } else {
            return $res;
        }
        return $res;
    }
}