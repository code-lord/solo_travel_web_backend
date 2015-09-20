<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "friend_request_master".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $customer_id
 * @property integer $friend_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CustomerMaster $customer
 * @property CustomerMaster $friend
 */
class FriendRequestMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'friend_request_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['status', 'customer_id', 'friend_id'], 'integer'],
            [['customer_id', 'friend_id'], 'required'],
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
