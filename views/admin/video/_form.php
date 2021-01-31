<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Video */
/* @var $form yii\widgets\ActiveForm */
/* @var $platforms array */
/* @var $domains array */
?>

<div class="video-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'domain_id')->dropDownList($domains, ['prompt' => 'Choose domain']) ?>

    <?= $form->field($model, 'platform_id')->dropDownList($platforms, ['prompt' => 'Choose platform']) ?>

    <?= $form->field($model, 'channel_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
