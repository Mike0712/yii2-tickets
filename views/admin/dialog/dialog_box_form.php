<?php

use \rgen3\tickets\models\forms\CreateMessage;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$model = new CreateMessage();

//$this->registerJs($script, \yii\web\View::POS_END);

if($withDialog){
   echo $this->render('dialog_box', ['theme' => $theme]);
}

?>


<?php $form = ActiveForm::begin([
    'id' => 'ticket-message-form',
    'method' => 'post',
    'action' => ['/ticket/answer'],
    'options' => ['data-pjax' => true ]
]); ?>
<?= $form->field($model, 'dialogId')->hiddenInput([
    'value' => $theme->id
])->label(false); ?>
<?= $form->field($model, 'message', [
    'template' => '<div class="col-sm-12">{label}</div><div class="col-sm-12">{error}</div><div class="col-sm-12">{input}</div>',
]); ?>
<?= Html::submitButton(
    Yii::t('app', 'Send'),
    [
        'class' => 'btn btn-success col-sm-2',
        'type' => 'button'
    ]); ?>
<?php ActiveForm::end(); ?>
