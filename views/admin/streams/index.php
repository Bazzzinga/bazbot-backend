<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StreamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Streams';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="streams-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Streams', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Order', ['preorder'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'platform_id',
                'value' => function($model) {
                    return $model->platform->name;
                }
            ],
            [
                'attribute' => 'domain_id',
                'value' => function($model) {
                    return $model->domain->name;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
