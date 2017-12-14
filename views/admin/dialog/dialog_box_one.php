<?php

use yii\helpers\Html;

?>
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
                        <?= $message->status->translationModel->title ?? '' ?>
                </small>
                <hr>
            </div>
        </div>
    </div>
</li>