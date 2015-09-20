<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\CustomerMaster */

$this->title = 'Create Customer Master';
$this->params['breadcrumbs'][] = ['label' => 'Customer Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
