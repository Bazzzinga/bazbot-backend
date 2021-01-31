<?php

use app\models\Streams;
use yii\helpers\Html;
use yii\web\View;
use kartik\sortinput\SortableInput;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Streams */
/* @var $streams Streams list */

$this->title = 'Order Streams';
$this->params['breadcrumbs'][] = ['label' => 'Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Domains', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = [];
foreach($streams as $stream) {
    $items[$stream->id] = [
        'content' => $stream->name
    ];
}
?>

<div class="showcase-update">
    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo SortableInput::widget([
        'name' => 'streams_order',
        'items' => $items
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
