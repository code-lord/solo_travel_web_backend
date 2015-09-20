<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\LocationMaster */

$this->title = 'Update Location Master: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Location Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="location-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
