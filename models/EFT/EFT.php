<?php

namespace app\models\EFT;

use Symfony\Component\DomCrawler\Crawler;

class EFT
{
    private static $eftUrl = 'http://eftstat.us/';

    public static function getOnline()
    {
        $html = file_get_contents(static::$eftUrl);

        $pq = new Crawler($html);

        $totalOnline = intval(trim($pq->filter('.col-6-l #countplayer')->text()));

        $regionsNames = [];
        $regionsData = [];

        $pq->filter('.title-server > strong')
            ->each(function(Crawler $node, $i) use (&$regionsNames){
                $regionsNames[$i] = trim($node->text());
            });

        $pq->filter('.title-lobbies > strong')
            ->each(function(Crawler $node, $i) use (&$regionsData, $regionsNames){
                $regionsData[$regionsNames[$i]] = intval(trim($node->text()));
            });

        return [
            'total' => $totalOnline,
            'regions' => $regionsData,
        ];
    }
}
