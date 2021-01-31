<?php


namespace app\controllers;


use app\models\Domains;
use app\models\LastVideo;
use app\models\Platforms;
use DateTime;
use DateTimeZone;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class CallbackController extends Controller
{
    public function actionYoutubeSubscription($domain)
    {
        $domainId = $domain;
        $domain = Domains::find()
            ->where(['id' => $domainId])
            ->one();

        if ($domain) {

            $getData = Yii::$app->request->get();

            if (ArrayHelper::keyExists('hub_challenge', $getData)) {
                $hubChallenge = $getData['hub_challenge'];
                //40 last symbols -> secret
                $secret = substr($hubChallenge, -40);

                Yii::info("YOUTUBE PUBSUBHUBBUB NEW SUBSCRIPTION\nDomain: " . print_r($domain, true) . "\nSECRET: " . print_r($secret, true), 'yt_ps');
            } else {
                $xml = file_get_contents("php://input");
                Yii::info("YOUTUBE PUBSUBHUBBUB NEW EVENT\nDomain: " . print_r($domain, true) . "\nXML: " . print_r($xml, true), 'yt_ps');
                $parsedXml = simplexml_load_string($xml);
                $entry = $parsedXml->entry;
                $id = $entry->id;
                $idParts = explode(':', $id);
                $videoId = (string)array_pop($idParts);
                $title = (string)$entry->title;
                $link = (string)($entry->link->attributes()['href']);
                $author = (string)($entry->author->name);

                $time = (string)$entry->updated;
                $timeParts = explode('+', $time);
                $time = array_shift($timeParts);
                $timeParts = explode('.', $time);
                $time = array_shift($timeParts);
                $dtz = new DateTimeZone('Europe/London');
                $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s', $time, $dtz);

                $parser = xml_parser_create();
                xml_parse_into_struct($parser, $xml, $test, $index);

                $channelId = '';

                foreach ($test as $item) {
                    if ($item['tag'] == 'YT:CHANNELID') {
                        $channelId = $item['value'];
                    }
                }

                $platform = Platforms::find()
                    ->where(['code' => 'youtube'])
                    ->one();

                if ($platform) {
                    LastVideo::updateLastVideo($channelId, $videoId, $platform->id, $author, $title, $link, $domainId);
                }
            }
        }
    }
}