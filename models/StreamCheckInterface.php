<?php

namespace app\models;

interface StreamCheckInterface
{
    public function checkStatus($channel, $title = "", $game = "");

    public function embedVideo($channel, $type);

    public function embedChat($channel, $domain, $hidden);

    public function embedCss($type = 0);

    public function getLastVideo($channel);

    public function getVideoId($channel);
}

?>
