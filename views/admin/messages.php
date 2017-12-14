<?php
use \yii\grid\GridView;
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-body well-lg">
                <div class="card-box">

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'dataTable table table-striped table-hover goodsTable table-bordered mainT'],

                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>