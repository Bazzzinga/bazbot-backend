<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Video */
/* @var $platforms array */
/* @var $domains array */

$this->title = 'Update Video: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="video-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'platforms' => $platforms,
        'domains' => $domains,
    ]) ?>

</div>
