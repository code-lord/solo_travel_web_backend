<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "location_footprint_master".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $customer_id
 * @property integer $location_id
 *
 * @property CustomerMaster $customer
 * @property LocationMaster $location
 */
class LocationFootprintMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'location_footprint_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['customer_id', 'location_id'], 'required'],
            [['customer_id', 'location_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'customer_id' => 'Customer ID',
            'location_id' => 'Location ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(CustomerMaster::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation() {
        return $this->hasOne(LocationMaster::className(), ['id' => 'location_id']);
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
