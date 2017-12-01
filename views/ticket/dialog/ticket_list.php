<?php
    use yii\helpers\Html;
    use yii\widgets\Pjax;
?>

<h3>
    <?= Yii::t('app', 'Tickets'); ?>
    <?= Html::a(Yii::t('app', 'Create ticket'), \yii\helpers\Url::to(['/ticket/create']), ['class' => 'btn btn-success  pull-right']); ?>
</h3>
<?php /*Pjax::begin([
        'id' => 'pjax-ticket-list'
]) */?>
    <?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
            [   'value' => function($model) use ($themeId){
                    return Html::a(
                        $model->subject,
                        '/ticket/dialog/' . $model->id,
                        [
                            'class' => "list-group-item " . (($model->id === $themeId) ? 'list-group-item-success' : ''),
                            'data-pjax'=>'0',
                        ]);
                    },
                'format' => 'raw'

            ],
            [
                'label' => Yii::t('app', 'Created'),
                'value' => function($model){
                    return Yii::$app->formatter->asDateTime($model->created_at);
                },
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
                'value' => function($el) use ($themeId)
                {
                    if ($el->is_closed){
                        return '<span class="glyphicon glyphicon-lock"></span>';
                    }
                    if(!$themeId || $el->id == $themeId) {
                        return '<span class="glyphicon glyphicon-folder-close close-ticket" data-themeId="' . $el->id . '" data-toggle="modal" data-target=".bs-ticket-form-modal-sm" title="' . Yii::t('app', 'will close?') . '"
                                    style="cursor: pointer; color: #0275d8;"
                                    ></span>';
                    }
                    return '<span class="glyphicon glyphicon-folder-close"></span>';
                }
            ],
    ],
    'filterUrl' => \yii\helpers\Url::to(["ticket"]),
]); ?>
<?php ?>
<?php //Pjax::end(); ?>