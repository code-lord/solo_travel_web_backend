<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Location Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-master-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Location Master', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'description:ntext',
            [
                'attribute' => 'interest_id',
                'label' => 'Interest',
                'content' => function($data) {
                    return $data->interest->interest;
                }
            ],
            [
                'attribute' => 'customer_id',
                'label' => 'User',
                'content' => function($data) {
                    return $data->customer->name;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
