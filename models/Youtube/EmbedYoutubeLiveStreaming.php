<?php


namespace app\models\Youtube;


class EmbedYoutubeLiveStreaming
{
    public $channelId;
    public $API_Key;
    public $jsonResponse; // pure server response
    public $objectResponse; // response decoded as object
    public $arrayResponse; // response decoded as array
    public $isLive; // true if there is a live streaming at the channel
    public $queryData; // query values as an array
    public $getAddress; // address to request GET
    public $getQuery; // data to request, encoded
    public $queryString; // Address + Data to request
    public $part;
    public $eventType;
    public $type;
    public $default_embed_width;
    public $default_embed_height;
    public $default_ratio;
    public $embed_code; // contain the embed code
    public $embed_js;
    public $embed_autoplay;
    public $embed_width;
    public $embed_height;
    public $name;
    public $live_video_id;
    public $live_video_title;
    public $live_video_description;
    public $live_video_published_at;
    public $live_video_thumb_default;
    public $live_video_thumb_medium;
    public $live_video_thumb_high;
    public $channel_title;
    public function __construct($ChannelID, $API_Key, $name, $w = 854, $h = 480, $autoQuery = true)
    {
        $this->live_video_id = "";
        $this->channelId = $ChannelID;
        $this->API_Key = $API_Key;
        $this->name = $name;
        $this->part = "snippet";
        $this->eventType = "live";
        $this->type = "video";
        $this->getAddress = "https://www.googleapis.com/youtube/v3/search?";
        $this->default_embed_width = strval($w);
        $this->default_embed_height = strval($h);
        $this->default_ratio = $this->default_embed_width / $this->default_embed_height;
        $this->embed_width = $this->default_embed_width;
        $this->embed_height = $this->default_embed_height;
        $this->embed_autoplay = true;
        if($autoQuery == true) { $this->queryIt(); }
    }
    public function queryIt()
    {
        $this->queryData = array(
            "part" => $this->part,
            "channelId" => $this->channelId,
            "eventType" => $this->eventType,
            "type" => $this->type,
            "key" => $this->API_Key,
        );
        $this->getQuery = http_build_query($this->queryData); // transform array of data in url query

        $this->queryString = $this->getAddress . $this->getQuery;


        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->queryString
        ));

        $this->jsonResponse = curl_exec($ch);

        curl_close($ch);

        $this->objectResponse = json_decode($this->jsonResponse); // decode as object

        $this->arrayResponse = json_decode($this->jsonResponse, TRUE); // decode as array
        $this->isLive();
        if($this->isLive)
        {
            $this->live_video_id = $this->objectResponse->items[0]->id->videoId;
            $this->live_video_title = $this->objectResponse->items[0]->snippet->title;
            $this->live_video_description = $this->objectResponse->items[0]->snippet->description;
            $this->live_video_published_at = $this->objectResponse->items[0]->snippet->publishedAt;
            $this->live_video_thumb_default = $this->objectResponse->items[0]->snippet->thumbnails->default->url;
            $this->live_video_thumb_medium = $this->objectResponse->items[0]->snippet->thumbnails->medium->url;
            $this->live_video_thumb_high = $this->objectResponse->items[0]->snippet->thumbnails->high->url;
            $this->channel_title = $this->objectResponse->items[0]->snippet->channelTitle;
            $this->embedCode();
        }
    }
    public function isLive($getOrNot = false)
    {
        if($getOrNot==true)
        {
            $this->queryIt();
        }
        $live_items = count($this->objectResponse->items);
        if($live_items>0)
        {
            $this->isLive = true;
            return true;
        }
        else
        {
            $this->isLive = false;
            return false;
        }
    }
    public function setEmbedSizeByWidth($width, $refill_code = true)
    {
        $ratio = $this->default_embed_width / $this->default_embed_height;
        $this->embed_width = $width;
        $this->embed_height = $width / $ratio;
        if( $refill_code == true ) { $this->embedCode(); }
    }
    public function setEmbedSizeByHeight($height, $refill_code = true)
    {
        $ratio = $this->default_embed_width / $this->default_embed_height;
        $this->embed_height = $height;
        $this->embed_width = $height * $ratio;
        if( $refill_code == true ) { $this->embedCode(); }
    }
    public function embedCode()
    {
        $autoplay = $this->embed_autoplay ? "?autoplay=1" : "";

        $ap_param = $autoplay?"true":"false";

        $nameMD5 = md5($this->name);

        //$this->embed_code = "<script>var ytb_{$nameMD5}_player;</script>";
        $this->embed_code = '<iframe src="https://www.youtube.com/embed/' . $this->live_video_id . '?rel=0" frameborder="0" allowfullscreen></iframe>';
        //$this->embed_js = "ytb_{$nameMD5}_player = new YT.Player('{$nameMD5}', {height: '{$this->default_embed_height}',width: '{$this->default_embed_width}',videoId: '{$this->live_video_id}',autoplay: '{$ap_param}'});";
        return $this->embed_code;
    }
}
?>
