<?php


namespace app\models;

use app\models\Stream;
use yii\helpers\ArrayHelper;

class StreamList
{
    private static function prepareStreamList($streams) {
        $streamsOrdered = [];
        foreach($streams as $key => $stream) {
            $streamsOrdered[] = $stream->platform->code . '|' . $stream->channel;
        }
        return $streamsOrdered;
    }

    private static function getStreamsHTML($order, $domain, $grid, $type, $hidden, $parent)
    {
        $result = "";
        $css = "";

        $platforms = [];

        $prefixUsage = explode('|', $domain->prefix_usage);

        $streamsLive = 0;
        foreach($order as $platform_channel) {
            $parts = explode('|', $platform_channel);

            if(count($parts) == 2) {
                $platform = $parts[0];
                $channel = $parts[1];

                $streamObject = new Stream(Stream::convertPlatformCode($platform));
                if(!ArrayHelper::keyExists($platform, $platforms)) {
                    $platforms[$platform] = true;
                }

                $prefix = "";
                if(in_array($platform, $prefixUsage)) {
                    $prefix = $domain->stream_prefix;
                }

                $status = $streamObject->checkStatus($channel, $prefix, $domain->steam_game_id);

                if($status !== false) {
                    $streamsLive++;					
                    $result.= $streamObject->embedStream($channel, $domain->domain, $type, $hidden, $parent);
                }
            }
        }

        foreach($platforms as $platform => $status) {
            $streamObject = new Stream(Stream::convertPlatformCode($platform));
            $gridType = 0;
            if(($grid == 1) && ($streamsLive > 1)) {
                $gridType = 1;
            }
            $css.= $streamObject->streamCss($gridType);
        }


        return '<style>' . $css . '</style><div class="all_streams">' . $result . '</div>';
    }

    public static function create($streams, $domain, $grid, $type = "big", $hidden = false, $parent = '')
    {
        $streamsOrdered = static::prepareStreamList($streams);
        return static::getStreamsHTML($streamsOrdered, $domain, $grid, $type, $hidden, $parent);
    }
}