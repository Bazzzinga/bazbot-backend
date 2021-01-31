<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Streams */
/* @var $platforms array */
/* @var $domains array */

$this->title = 'Create Streams';
$this->params['breadcrumbs'][] = ['label' => 'Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="streams-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'platforms' => $platforms,
        'domains' => $domains,
    ]) ?>

</div>
