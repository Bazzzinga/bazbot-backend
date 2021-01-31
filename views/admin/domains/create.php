<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Domains */
/* @var $platformList array */

$this->title = 'Create Domains';
$this->params['breadcrumbs'][] = ['label' => 'Domains', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domains-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'platformList' => $platformList
    ]) ?>

</div>
