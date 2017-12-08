<?php

use yii\widgets\Pjax;
use \yii\grid\GridView;
use yii\helpers\Html;
use \common\models\User;

$afterMessage = \Yii::t('app', 'Ticket closed!');

$style = <<< CSS
    .messenger-box {
        height: 200px;
        overflow-y: scroll;
    }
CSS;

$script = <<<JS

$('.grid-view').on('click', '.ticket-answer', function() {
        var dataId = $(this).data('ticketid')
        var title = 'Форма ответа пользователю, тикет №' +dataId;
        $('.modal-title').text(title);
        
        $.post('/ticket/admin/get-answer-form', {theme_id: dataId}, function(data) {
             $('.modal-body').html(data);
        });  
    });

$('.grid-view').on('click', '.close-ticket', function() {
    var dataId = $(this).data('themeid')
    var title = 'Удаление тикета №' +dataId;
    $('.modal-title').text(title);
    $.post('/ticket/admin/get-close-form', {theme_id: dataId}, function(data) {
             $('.modal-body').html(data);
        });
});

jQuery('body').on('submit', '#ticket-close-form', function ()
    {
        var form = $('#ticket-close-form'),
            data = form.serialize();

        jQuery.ajax({
            url : '/ticket/admin/close',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response)
            {
                if (response == 1){
                    $('.modal-body').html("$afterMessage");
                }
            },
            error: function(response)
            {
                
            }
        });
        return false;
    });

    jQuery('body').on('submit', '#ticket-message-form', function ()
    {
        var form = $('#ticket-message-form'),
            data = form.serialize();

        jQuery.ajax({
            url : '/ticket/admin/answer',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response)
            {
                $('this').trigger('reset');
                var list = $('.media-list');
                $("<li/>", {
                    class: "media",
                       html: $("<div/>", {
                        class: "media-body",
                        html: $("<div/>", {class: 'media', html: '<span class="pull-left"></span><div class="media-body">' + response.message + '</div>'}) 
                        })
                }).appendTo(list)
                list.scrollTop(list.prop('scrollHeight'))
            },
            error: function(response)
            {
                
            }
        });
        return false;
    });

JS;

$this->registerCss($style, [], 'messenger-style');
$this->registerJs($script, \yii\web\View::POS_END);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-body well-lg">
                <div class="card-box">

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'dataTable table table-striped table-hover goodsTable table-bordered mainT'],
                        'columns' => [
                            'id',
                            [
                              'label' => Yii::t('app', 'User from'),
                              'value' => function($model){
                                  $userId = $model->user_from;
                                  $user = User::findOne($userId);
                                  return $user->username;
                              }
                            ],
                            [
                                'label' => Yii::t('app', 'Assigned to'),
                                'value' => function($model){
                                    $userId = $model->assigned_to;
                                    $user = User::findOne($userId);
                                    return $user->username;
                                },
                                'format' => 'raw'
                            ],
                            [
                                'label' => Yii::t('app', 'Subject'),
                                'value' => 'subject'
                            ],
                            [
                                'label' => Yii::t('app', 'Last user\'s message'),
                                'value' => function($model){
                                    $sender = $model->sender->id;
                                    $message = $model->getDialog()->where(['answered_by' => $sender])->one();
                                    if($message){
                                        return $this->render('dialog/dialog_box_one', ['message' => $message]);
                                    }

                                },
                                'format' => 'raw'
                            ],
                            [
                                'value' => function($model, $key, $index){
                                    if($model->is_closed){
                                        return '';
                                    }
                                    return Html::button(\Yii::t('app', 'Answer'), ['class' => 'ticket-answer', 'data-ticketId' => $model->id, 'data-toggle' => 'modal',  'data-target' => '.bs-ticket-form-modal-sm']);
                                },
                                'format' => 'raw'
                            ],
                            [
                              'label' => Yii::t('app', 'Status'),
                              'value' => function($model){
                                 if (!$model->is_closed){
                                    if(count(array_filter($model->dialog, function ($item) use ($model){
                                        return $item->answered_by == $model->assigned_to || $item->answered_by == 1;
                                    }, ARRAY_FILTER_USE_BOTH))){
                                        return Yii::t('app', 'Open');
                                    }
                                     return Yii::t('app', 'New');
                                 }
                                 return Yii::t('app', 'Closed');
                              }
                            ],
                            [
                                'format' => 'raw',
                                'value' => function($el)
                                {
                                    if ($el->is_closed){
                                        return '<span class="glyphicon glyphicon-lock"></span>';
                                    }
                                    return '<span class="glyphicon glyphicon-folder-close close-ticket" data-themeId="' . $el->id . '" data-toggle="modal" data-target=".bs-ticket-form-modal-sm" title="' . Yii::t('app', 'will close?') . '"
                                    style="cursor: pointer; color: #0275d8;"
                                    ></span>';
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-ticket-form-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mySmallModalLabel"><?= Yii::t('app', 'Форма ответа пользователю')  ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>