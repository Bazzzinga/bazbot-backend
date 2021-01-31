<?php

namespace app\controllers\admin;

use app\models\Domains;
use app\models\Platforms;
use Yii;
use app\models\Streams;
use app\models\StreamsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreamsController implements the CRUD actions for Streams model.
 */
class StreamsController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Streams models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StreamsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Streams model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Streams model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Streams();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $platforms = ArrayHelper::map(Platforms::find()->all(), 'id', 'name');

        $domains = ArrayHelper::map(Domains::find()->all(), 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'platforms' => $platforms,
            'domains' => $domains,
        ]);
    }

    /**
     * Updates an existing Streams model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $platforms = ArrayHelper::map(Platforms::find()->all(), 'id', 'name');

        $domains = ArrayHelper::map(Domains::find()->all(), 'id', 'name');

        return $this->render('update', [
            'model' => $model,
            'platforms' => $platforms,
            'domains' => $domains,
        ]);
    }

    /**
     * Deletes an existing Streams model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionPreorder()
    {
        $domains = Domains::find()->all();

        return $this->render('preorder', [
            'domains' => $domains
        ]);
    }

    public function actionOrder($domain_id)
    {
        if (Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            if($postData != null) {
                $orders = explode(",", $postData['streams_order']);
                $_streams = Streams::find()
                    ->all();

                $streams_count = count($orders);

                foreach($_streams as $stream) {
                    if(ArrayHelper::isIn($stream->id, $orders)) {
                        $stream->priority = $streams_count - array_search($stream->id, $orders);
                        $stream->save();
                    }
                }
            }
        }

        $streams = Streams::find()
            ->orderBy('priority DESC')
            ->where(['domain_id' => $domain_id])
            ->all();

        return $this->render('order', [
            'streams' => $streams,
        ]);
    }

    /**
     * Finds the Streams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Streams the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Streams::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
