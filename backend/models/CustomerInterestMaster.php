<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "customer_interest_master".
 *
 * @property integer $id
 * @property integer $interest_id
 * @property integer $customer_id
 *
 * @property CustomerMaster $customer
 * @property InterestMaster $interest
 */
class CustomerInterestMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'customer_interest_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['interest_id', 'customer_id'], 'required'],
            [['interest_id', 'customer_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'interest_id' => 'Interest ID',
            'customer_id' => 'Customer ID',
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
    public function getInterest() {
        return $this->hasOne(InterestMaster::className(), ['id' => 'interest_id']);
    }

}
