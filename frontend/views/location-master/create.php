<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\LocationMaster */

$this->title = 'Create Location Master';
$this->params['breadcrumbs'][] = ['label' => 'Location Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form_new_loc', [
        'model' => $model,
        'upload_model' => $upload_model,
    ])
    ?>

</div>
