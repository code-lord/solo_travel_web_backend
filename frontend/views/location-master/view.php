<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\LocationMaster */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Location Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-master-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Location Image',
                'attribute' => 'cover_photo_url',
                'type' => 'raw',
                'format' => ['image', ['width' => '300px']],
                'value' => 'http://localhost/solo_travel/backend/web/' . $model->cover_photo_url,
            ],
            'name',
            'description:ntext',
            [
                'label' => 'Interest',
                'value' => $model->interest->interest,
            ],
            [
                'label' => 'Customer',
                'value' => $model->customer->name,
            ],
        ],
    ])
    ?>

</div>
