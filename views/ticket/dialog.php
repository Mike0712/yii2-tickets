<?php
use yii\helpers\Html;
use rgen3\tickets\assets\ticketAsset;
ticketAsset::register($this);
$this->title = Yii::t('app', 'Тикеты');

$afterMessage = \Yii::t('app', 'Ticket closed!');
$closed = Yii::t('app', 'Closed');
$script = <<<JS

$('.grid-view, #ticket-message-form').on('click', '.close-ticket', function() {
    var dataId = $(this).data('themeid')
    var title = 'Закрытие тикета №' +dataId;
    $('.modal-title').text(title);
    $.post('/ticket/ticket/get-close-form', {theme_id: dataId}, function(data) {
             $('.modal-body').html(data);
          });
});

jQuery('body').on('submit', '#ticket-close-form', function (event)
    {
        event.preventDefault()
        var form = $('#ticket-close-form'),
            data = form.serialize(),
            ticketId = $('#updateticket-ticketid').val(),
            container = $('#w0'),
            currentRow = container.find('[data-key=' + ticketId + ']');
        jQuery.ajax({
            url : '/ticket/ticket/close',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response)
            {
                if (response == 1){
                    $('.modal-body').html("$afterMessage");
                    currentRow.children('td:eq(2)').text('$closed');
                    currentRow.children('td:eq(3)').html('<span class="glyphicon glyphicon-folder-close"></span>');
                    $('#ticket-message-form').hide();
                }
            },
            error: function(response)
            {
                
            }
        });
        
        
        
        return false;
    });
JS;
$this->registerJs($script, \yii\web\View::POS_END);

?>

<div class="row tickets-block">
    <div class='<?= $theme ? "col-lg-4 col-md-5 col-sm-12" : "col-lg-12" ?>'>
        <div class="card-box">

            <div class="row chats-row">
                <div class="col-md-12">

                    <?= Html::a(Yii::t('app', 'Create ticket'), \yii\helpers\Url::to(['/ticket/create']), ['class' => 'btn btn-success']); ?>

                </div>
            </div>
            <div class="row">

                <!-- стрелка, которая показывает \ скрывает тикеты -->
                <i class="ion-ios7-arrow-down moreTickets"></i>

                <?= $this->render(
                    'dialog/ticket_list',
                    [
                        'dataProvider' => $dataProvider,
                        'themeId' => $theme && $theme->id ? $theme->id : false
                    ]
                ); ?>

            </div>
        </div>
    </div>

    <?php if ($theme): ?>
        <div class="col-lg-8 col-md-7 col-sm-12">
            <div class="card-box floatChat">

                <?= $this->render('dialog/manager_info', ['receiver' => $receiver, 'theme' => $theme]); ?>

                <?= $this->render('dialog/dialog_box', ['theme' => $theme]); ?>

            </div>
        </div>
    <?php endif; ?>

</div>

<div class="modal fade bs-ticket-form-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mySmallModalLabel"><?= Yii::t('app', 'Закрытие тикета №')  ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>