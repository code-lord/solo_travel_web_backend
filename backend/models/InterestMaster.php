<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "interest_master".
 *
 * @property integer $id
 * @property string $interest
 * @property integer $status
 *
 * @property CustomerInterestMaster[] $customerInterestMasters
 * @property LocationMaster[] $locationMasters
 */
class InterestMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'interest_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['interest', 'status'], 'required'],
            [['status'], 'integer'],
            [['interest'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'interest' => 'Interest',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerInterestMasters() {
        return $this->hasMany(CustomerInterestMaster::className(), ['interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationMasters() {
        return $this->hasMany(LocationMaster::className(), ['interest_id' => 'id']);
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

}
