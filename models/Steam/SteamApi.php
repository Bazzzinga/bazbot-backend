<?php


namespace app\models\Steam;


use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\EFT\EFT;

class SteamApi
{
    private static $getOnlineAPIUrl = "https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/?appid=";

    private static $getNewsAPIUrl = "http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=";

    private static $getNewsAPIUrlPostfix = "&count=5&maxlength=3000&format=json";

    public static $EFTgameId = 'eft';

    public static function getGameOnline($game_id)
    {
        if ($game_id == static::$EFTgameId) {
            $response = EFT::getOnline();

            $log = new SteamOnline();
            $log->game = $game_id;
            $log->value = Json::encode($response);
            $log->save();

            return Json::encode([
                'response' => [
                    "player_count" => $response['total'],
                    'result' => 1,
                ],
            ]);
        } else {
            $url = static::$getOnlineAPIUrl . $game_id;

            $response = file_get_contents($url);

            $decoded = Json::decode($response);

            if (ArrayHelper::keyExists('response', $decoded)) {
                $data = $decoded['response'];
                if (ArrayHelper::keyExists('player_count', $data)) {
                    $player_count = intval($data['player_count']);

                    $log = new SteamOnline();
                    $log->game = $game_id;
                    $log->value = strval($player_count);
                    $log->save();
                }
            }

            return $response;
        }
    }

    public static function getGameNews($game_id)
    {
        $url = static::$getNewsAPIUrl . $game_id . static::$getNewsAPIUrlPostfix;

        $response =  file_get_contents($url);

        return $response;
    }
}