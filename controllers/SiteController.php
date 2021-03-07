<?php

namespace app\controllers;

use app\models\Discord;
use app\models\Domains;
use app\models\Steam\SteamApi;
use app\models\Steam\SteamOnline;
use app\models\Stream;
use app\models\StreamList;
use app\models\Streams;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\PChart\PChart;
use app\models\EFT\EFT;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
		
        $result = [
            'state' => false,
            'stream' => false,
            'channel' => "",
            'service' => "",
            'key' => "",
            'name' => "",
            'title' => ""
        ];

        $getData = Yii::$app->request->get();
        if( ArrayHelper::keyExists('k', $getData) &&
            ArrayHelper::keyExists('s', $getData) &&
            ArrayHelper::keyExists('c', $getData) &&
            ArrayHelper::keyExists('n', $getData) ) {

            $domain = Domains::find()
                ->where(['token' => $getData['k']])
                ->one();

            if($domain == null) {
                return Json::encode($result);
            }

            $platform = $getData['s'];
            $channel = $getData['c'];
            $name = $getData['n'];

            $title = "";
            if(ArrayHelper::keyExists('t', $getData)) {
                $title = $getData['t'];
            }

            $game = "";
            if(ArrayHelper::keyExists('g', $getData)) {
                $game = $getData['g'];
            }

            $result['channel'] = $channel;
            $result['service'] = $platform;
            $result['name'] = $name;
            $result['key'] = $platform . $channel;

            $live = false;

            $stream = null;

            $stream = new Stream(Stream::convertPlatformCode($platform));

            if($stream !== null) {

                $result['state'] = true;
                $live = $stream->checkStatus($channel, $title, $game);

                if($live !== false) {
                    $result['stream'] = true;
                    $result['title'] = $live;
                    $result['video'] = $stream->getVideoId($channel);
                }
            }
        }

        return Json::encode($result);
    }

    public function actionCheck()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $getData = Yii::$app->request->get();

        if( ArrayHelper::keyExists('k', $getData) ) {
            $domain = Domains::find()
                ->where(['token' => $getData['k']])
                ->one();

            if($domain == null) {
                return 0;
            }

            $prefixUsage = explode('|', $domain->prefix_usage);

            $streams = Streams::find()
                ->with('platform')
                ->where(['domain_id' => $domain->id])
                ->all();

            $liveStreams = 0;
            foreach ($streams as $stream) {
                $streamObject = new Stream(Stream::convertPlatformCode($stream->platform->code));
				
                $prefix = "";
                if(in_array($stream->platform->code, $prefixUsage)) {
                    $prefix = $domain->stream_prefix;
                }

                if($streamObject->checkStatus($stream->channel, $prefix, $domain->steam_game_id) !== false) {
                    $liveStreams++;
                }
            }

            return $liveStreams;
        }

        return 0;
    }

    public function actionStreams()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;

        $getData = Yii::$app->request->get();

        $result = "";

        if(ArrayHelper::keyExists('d', $getData) &&
			ArrayHelper::keyExists('p', $getData)) {
            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            if($domain != null) {
                $streams = Streams::find()
                    ->with('platform')
                    ->where(['domain_id' => $domain->id])
                    ->orderBy('priority DESC')
                    ->all();
				
                $result.= StreamList::create($streams, $domain, $domain->use_grid, 'big', false, $getData['p']);
            }
        }

        return  $result;
    }

    public function actionNews()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $getData = Yii::$app->request->get();

        if(ArrayHelper::keyExists('g', $getData) &&
            ArrayHelper::keyExists('d', $getData)) {
            $gameId = $getData['g'];
            $token = $getData['d'];

            $domain = Domains::find()
                ->where(['token' => $token])
                ->one();

            if($domain != null) {
                return SteamApi::getGameNews($gameId);
            }
        }

        return "";
    }

    public function actionOnline()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $getData = Yii::$app->request->get();

        if(ArrayHelper::keyExists('g', $getData) &&
            ArrayHelper::keyExists('d', $getData)) {
            $gameId = $getData['g'];

            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            if($domain != null) {
                return SteamApi::getGameOnline($gameId);
            }
        }
    }

    public function actionLastVideo()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $getData = Yii::$app->request->get();

        if(ArrayHelper::keyExists('c', $getData) &&
            ArrayHelper::keyExists('d', $getData)) {

            $channel = $getData['c'];

            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            $platform = 'youtube';

            if($domain != null) {
                $stream = new Stream(Stream::convertPlatformCode($platform));
                return Json::encode($stream->lastVideo($channel, $domain->id));
            }
        }
    }

    public function actionGraph()
    {
        $getData = Yii::$app->request->get();
        if (ArrayHelper::keyExists('g', $getData) &&
            ArrayHelper::keyExists('d', $getData)) {

            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            if($domain == null) {
                return;
            }

            $game = $getData['g'];

            Yii::$app->response->format = Response::FORMAT_RAW;
            Yii::$app->getResponse()->headers->set('Content-Type', 'image/png');

            return Yii::$app->cache->getOrSet(
                ['graph', 'g' => $game],
                function () use ($game) {
                    $pChart = new PChart();

                    $date = date('Y-m-d H:i:s', strtotime('-3 days'));

                    $data = SteamOnline::find()
                        ->select(['date', 'value'])
                        ->where(['>=', 'date', $date])
                        ->andWhere(['game' => $game])
                        ->orderBy(['date' => SORT_ASC])
                        ->asArray()
                        ->all();

                    ob_start();
                    if ($game == SteamApi::$EFTgameId) {
                        $pChart->drawMultiChart($data);
                    } else {
                        $pChart->drawChart($data);
                    }
                    $result = ob_get_contents();
                    ob_end_flush();
                    return $result;
                },
                180
            );
        }
    }

    public function actionUpdateDiscord()
    {
        $getData = Yii::$app->request->get();
        if (ArrayHelper::keyExists('t', $getData) &&
            ArrayHelper::keyExists('o', $getData) &&
            ArrayHelper::keyExists('d', $getData)) {

            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            if($domain == null) {
                return;
            }

            $online = $getData['o'];
            $total = $getData['t'];

            $discord = Discord::find()
                ->where(['domain_id' => $domain->id])
                ->one();

            if($discord == null) {
                $discord = new Discord();
                $discord->domain_id = $domain->id;
            }

            $discord->online = $online;
            $discord->total = $total;

            $discord->save();
        }
    }

    public function actionGetDiscord()
    {
        $getData = Yii::$app->request->get();
        if (ArrayHelper::keyExists('d', $getData)) {
            $domain = Domains::find()
                ->where(['token' => $getData['d']])
                ->one();

            if($domain == null) {
                return;
            }

            $discord = Discord::find()
                ->where(['domain_id' => $domain->id])
                ->one();

            if ($discord == null) {
                return Json::encode(['total' => 0, 'online' => 0]);
            }

            return Json::encode(['total' => $discord->total, 'online' => $discord->online]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Url::to(['/admin/streams']));
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRefresh()
    {
        Yii::$app->cache->flush();
    }

    public function actionYoutubeCallback()
    {
        //https://developers.google.com/youtube/v3/guides/push_notifications
        Yii::info("YUOTUBE PUSH MESSAGE\n" . print_r(Yii::$app->request, true), 'yt_cb');
    }

    public function actionTwitchCallback()
    {
        //https://dev.twitch.tv/docs/api/webhooks-reference/
        /*

         POST https://api.twitch.tv/helix/webhooks/hub + client_id
            + body:
            {
                "hub.callback": "http://stream.huntshowdown.info/twitch-callback",
                "hub.mode": "subscribe",
                "hub.topic": "https://api.twitch.tv/helix/streams?user_id=37402112"
            }

            user_id из запроса

            GET https://api.twitch.tv/helix/users?login=shroud

         */
        Yii::info("TWITCH PUSH MESSAGE\n" . print_r(Yii::$app->request, true), 'tw_cb');
    }

	public function actionTwitchAuth()
    {
        $getData = Yii::$app->request->get();

        if (ArrayHelper::keyExists('code', $getData)) {
            $code = $getData['code'];
            var_dump($code);
			die;
        }
    }

}
