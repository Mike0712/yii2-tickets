<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?= Yii::t('app', 'Are you sure you want to close the task? This action is irreversible') ?>

<?php $form = ActiveForm::begin([
    'id' => 'ticket-close-form',
    'method' => 'post',
    'action' => ['/ticket/close'],
    'options' => ['data-pjax' => true ]
]); ?>
<?= $form->field($model, 'ticketId')->hiddenInput([
    'value' => $theme->id
])->label(false); ?>

<?= Html::submitButton(
    Yii::t('app', 'Yes, I want to close this ticket'),
    [
        'class' => 'btn btn-success col-sm-12',
        'type' => 'button'
    ]); ?>
<span></span>

<?php ActiveForm::end(); ?>
