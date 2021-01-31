<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Domains */
/* @var $form yii\widgets\ActiveForm */
/* @var $platformList array */

//prepare product sections info for listBox
$listBoxSelected = [];
if($model->platformPrefixSelection != null) {
    foreach ($model->platformPrefixSelection as $key => $selected) {
        foreach($platformList as $id => $name) {
            if($selected == $name) {
                $listBoxSelected[$id] = ['selected' => true];
                $model->platformPrefixSelection[$key] = $id;
                break;
            }
        }
    }
}
?>

<div class="domains-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'steam_game_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stream_prefix')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'platformPrefixSelection')->listBox($platformList, ['size' => 5,'multiple' => true, $listBoxSelected]) ?>

    <?= $form->field($model, 'use_grid')->DropDownList(['0' => 'No', '1' => 'Yes']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
