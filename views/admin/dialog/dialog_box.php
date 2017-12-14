<?php

use yii\widgets\Pjax;
use yii\helpers\Html;

$style = <<< CSS
    .messenger-box {
        height: 200px;
        overflow-y: scroll;
    }
CSS;

$this->registerCss($style, [], 'messenger-style');

$afterMessage = \Yii::t('app', 'Message create!');

$script = <<< JS
    var messengerBox = document.getElementById("messenger-box");
    messengerBox.scrollTop = messengerBox.scrollHeight;
    jQuery.pjax.reload({container: '#pjax-ticket-dialog-box'});
JS;


$this->registerJs($script, \yii\web\View::POS_END);
?>
<div class="row current-chat-area">
    <div class="col-md-12">
        <?php Pjax::begin([
            'id' => 'pjax-ticket-dialog-box',
            'enablePushState' => false
        ]); ?>
        <ul class="media-list messenger-box" id="messenger-box">
            <?php $user_from = $theme->user_from ?>
            <?php foreach ($theme->getDialog()->orderBy('created_at')->all() as $message): ?>
                <li class="media">
                    <div class="media-body">
                        <div class="media">
                            <span class="pull-left" href="#">
                                <?= Html::img(\rgen3\tickets\Module::$defaultUserImage, [
                                    'class' => 'media-object img-circle'
                                ]); ?>
                            </span>
                            <div class="media-body">
                                <?= $message->message; ?>
                                <br>
                                <small class="text-muted">
                                    <?= $message->answeredBy->username; ?> |
                                    <?= Yii::$app->formatter->asDateTime($message->created_at); ?>
                                    <br>
                                    <?php if($message->answered_by == $user_from && $message->status): ?>
                                        <?= $message->status->translationModel->title ?>
                                    <?php endif; ?>
                                </small>
                                <hr>
                            </div>
                        </div>

                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php Pjax::end(); ?>
    </div>
</div>
