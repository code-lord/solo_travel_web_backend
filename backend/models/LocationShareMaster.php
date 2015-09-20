<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "location_share_master".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $customer_id
 * @property integer $friend_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $location_id
 *
 * @property CustomerMaster $customer
 * @property CustomerMaster $friend
 * @property LocationMaster $location
 */
class LocationShareMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'location_share_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['status', 'customer_id', 'friend_id', 'location_id'], 'required'],
            [['status', 'customer_id', 'friend_id', 'location_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'customer_id' => 'Customer ID',
            'friend_id' => 'Friend ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
    public function getFriend() {
        return $this->hasOne(CustomerMaster::className(), ['id' => 'friend_id']);
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
