<?php


namespace app\models;

use app\models\Goodgame\Goodgame;
use Yii;
use app\models\Twitch\Twitch;
use app\models\Youtube\Youtube;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Stream
{
    /**
     * @var \app\models\StreamCheckInterface
     */
    private $stream;

    private $platform;

    private $cacheKey = "stream_cache";
    private $cacheLifetime = 600;

    const YOUTUBE = 'yt';
    const TWITCH = 'tw';
    const NOTHING = 'nth';
    const GOODGAME = 'gg';

    private static $platformCodes = [
        'twitch' => Stream::TWITCH,
        'youtube' => Stream::YOUTUBE,
        'goodgame' => Stream::GOODGAME,
    ];

    public function __construct($platform)
    {
        switch ($platform) {
            case self::YOUTUBE:
                $this->stream = new Youtube();
                $this->platform = self::YOUTUBE;
                break;
            case static::TWITCH:
                $this->stream = new Twitch();
                $this->platform = self::TWITCH;
                break;
            case static::GOODGAME:
                $this->stream = new Goodgame();
                $this->platform = self::GOODGAME;
                break;
            default:
                $this->stream = null;
                $this->platform = self::NOTHING;
                break;
        }
    }

    public function checkStatus($channel, $title = "", $game = "")
    {
        if($this->platform != self::NOTHING) {
            return Yii::$app->cache->getOrSet(
                [$this->cacheKey, $this->getPlatform(), $channel, $title, $game],
                function() use ($channel, $title, $game) {
                    return $this->stream->checkStatus($channel, $title, $game);
                },
                $this->cacheLifetime
            );
        }

        return false;
    }

    public function embedStream($channel, $domain, $type = "big", $hidden = false, $parent = '')
    {
        $result = "<div class='player_wrapper'>";
        $result.= $this->stream->embedVideo($channel, $type, $parent);
        $result.= $this->stream->embedChat($channel, $domain, $hidden, $parent);
        $result.= "</div>";

        return $result;
    }

    public function lastVideo($channel, $domain_id)
    {
        $platform_id = Platforms::getIdFromCode(static::platformCodeRecover($this->platform));

        $lastSavedVideo = LastVideo::find()
            ->where([
                'platform_id' => $platform_id,
                'channel' => $channel,
                'domain_id' => $domain_id,
                'shown' => 0,
            ])
            ->one();

        if ($lastSavedVideo) {
            $lastSavedVideo->shown = 1;
            $lastSavedVideo->save();
            return $lastSavedVideo->data;
        }

        return null;

        /*$newVideo =  $this->stream->getLastVideo($channel);

        if($lastSavedVideo != null) {
            if($newVideo['id'] == $lastSavedVideo->video_id) {
                return null;
            }
        }

        if($lastSavedVideo == null) {
            $lastSavedVideo = new LastVideo();
        }

        $lastSavedVideo->video_id = $newVideo['id'];
        $lastSavedVideo->channel = $channel;
        $lastSavedVideo->platform_id = $platform_id;
        $lastSavedVideo->data = Json::encode($newVideo);
        $lastSavedVideo->domain_id = $domain_id;
        $lastSavedVideo->save();

        return $lastSavedVideo->data;*/
    }

    public function streamCss($type)
    {

        $style_block = "	
            .player_wrapper {
                background-color: #0e0e0e;
                background-image: url(img/bg.png);
                background-size: auto 100%;
                background-repeat: no-repeat;
                background-position: center;
                position: relative;                
                margin-top: 10px;
                height: 0;                                
                text-align: center;
                vertical-align: middle;                
                -webkit-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
                -moz-box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);
                box-shadow: 0px 0px 15px 0px rgba(0,0,0,1);		
            }
            
            .twitch_chat, .youtube_chat {
                position: absolute;		
                height: 100%;
                width: 20%;
                top: 0;
                right:0;
            }
        
            .twitch_big, .youtube_big {
                display: inline-block;
                float: left;
                position: relative;		
                padding-bottom: 45%;
                height: 0;
                width: 80%;
                margin-left: 0;
            }
            
            .twitch_big iframe, .youtube_big iframe {	
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            
            .twitch_chat iframe, .youtube_chat iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;		
            }";

        if($type == 1) {
            $style_block .= "
                .all_streams {
                    text-align: center;
                }
                .player_wrapper {
                    width: 49%;
                    padding-bottom: 22.1%;
                    display: inline-block;
                    margin-left: 0.5%;
                    margin-right: 0.5%;
                }
            ";
        } else {
            $style_block .= "
                .player_wrapper {
                    width: 100%;
                    padding-bottom: 45%;
                    margin: 0 auto;
                    margin-top: 10px;
                }
            ";
        }

        return $style_block . $this->stream->embedCss($type);
    }

    public static function convertPlatformCode($code)
    {
        if(ArrayHelper::keyExists($code, static::$platformCodes)) {
            return static::$platformCodes[$code];
        }

        return Stream::NOTHING;
    }

    public static function platformCodeRecover($platform)
    {
        foreach(static::$platformCodes as $code => $_platform) {
            if($platform == $_platform) {
                return $code;
            }
        }

        return "";
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getVideoId($channel)
    {
        return $this->stream->getVideoId($channel);
    }

}
