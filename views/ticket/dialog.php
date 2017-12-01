<?php

use yii\helpers\Html;

$afterMessage = \Yii::t('app', 'Ticket closed!');
$closed = Yii::t('app', 'Closed');
$script = <<<JS

$('.grid-view, .current-chat').on('click', '.close-ticket', function() {
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
                    $('.dialog-form-block').hide();
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
<div class="header-offers">
    <div class="row">
        <div class="col-sm-4">
            <h3 class="offers-title"><?= $theme->subject ?? ''; ?></h3>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 pull-right">
            <div class="row chats-row">
                <div class="col-md-12">
                    <?= $this->render('dialog/manager_info', ['receiver' => $receiver]); ?>
                </div>
                <div class="col-md-12">

                    <?= $this->render(
                            'dialog/ticket_list',
                            [
                                'dataProvider' => $dataProvider,
                                'themeId' => $theme && $theme->id ? $theme->id : false,
                            ]
                    ); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 current-chat">
            <?php if ($theme): ?>
                <?= $this->render('dialog/dialog_box', ['theme' => $theme]); ?>
            <?php else: ?>
                <div>Выберите тикет из списка</div>
            <?php endif; ?>
        </div>
    </div>
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