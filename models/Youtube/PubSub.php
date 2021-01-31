<?php


namespace app\models\Youtube;


use Yii;

class PubSub
{
    private $baseUrl = 'https://www.youtube.com/xml/feeds/videos.xml?channel_id=';
    private $pubSubHubBubUrl = 'https://pubsubhubbub.appspot.com/';

    private $callbackUrl = '';

    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    public function createSubscription($channelId)
    {
        $secret = hash('sha1', uniqid(rand(), true));
        $topic = $this->baseUrl . $channelId;

        $post_fields = array(
            'hub.callback' => (string)$this->callbackUrl,
            'hub.mode' => 'subscribe',
            'hub.topic' => $topic,
            'hub.verify' => 'async',
            'hub.lease_seconds' => Yii::$app->params['pubsubhubbubInterval'],
            'hub.secret' => $secret
        );

        $request = curl_init($this->pubSubHubBubUrl);
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt( $request, CURLOPT_VERBOSE, 1 );
        curl_exec($request);
        $code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        print_r( $code );
        if (in_array($code, array(202, 204))) {
            $info = "Positive response - request ($code). - secret: $secret   url: $topic";
            print_r($info);
            file_put_contents('subcription_made_' . date('d-m-Y_H-i-s') . '.txt', $info);
        }
        else {
            $error = "Error issuing - request - ($code).   url: $topic";
            print_r($error);
            file_put_contents('subcription_error_' . date('d-m-Y_H-i-s') . '.txt', $error);
        }
        curl_close($request);
    }
}