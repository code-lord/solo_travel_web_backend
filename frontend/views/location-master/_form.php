<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\LocationMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php
//        $bikerlist_query = Yii::$app->db->createCommand(" SELECT  `name`,`id`,`status`,`last_lat`, `last_long`, SQRT(
//                                                        POW(69.1 * (`last_lat` - " . $model->pickAddress->lat . "), 2) +
//                                                        POW(69.1 * (" . $model->pickAddress->lng . " - `last_long`) * COS(`last_lat` / 57.3), 2)) AS distance
//                                                    FROM delivery_boy_master  WHERE `status`=0 ORDER BY distance ");
    $interest = frontend\models\InterestMaster::find()->where(['status' => 1])->all();
    $interestlistData = [];
    if ($interest != null) {
        $interestlistData = yii\helpers\ArrayHelper::map($interest, 'id', 'interest');
    }
    ?>

    <?= $form->field($model, 'interest_id')->dropDownList($interestlistData, ['prompt' => 'Select Interest']) ?>

    <?php
    $status = ['0' => 'Inactive', '1' => 'Active', '2' => 'Other'];
    ?>

    <?= $form->field($model, 'status')->dropDownList($status, ['prompt' => 'Select Status']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
