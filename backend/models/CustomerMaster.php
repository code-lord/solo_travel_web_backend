<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer_master".
 *
 * @property integer $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property string $dob
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CustomerInterestMaster[] $customerInterestMasters
 * @property FavoriteLocationMaster[] $favoriteLocationMasters
 * @property FriendRequestMaster[] $friendRequestMasters
 * @property FriendRequestMaster[] $friendRequestMasters0
 * @property LocationFootprintMaster[] $locationFootprintMasters
 * @property LocationMaster[] $locationMasters
 * @property LocationShareMaster[] $locationShareMasters
 * @property LocationShareMaster[] $locationShareMasters0
 * @property RatingMaster[] $ratingMasters
 */
class CustomerMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'customer_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'username', 'password', 'email', 'phone', 'dob', 'status'], 'required'],
            [['dob', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'integer'],
            [['name', 'username', 'password', 'email', 'phone'], 'string', 'max' => 45],
            [['username'], 'unique'],
            [['phone'], 'unique'],
            [['email'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'phone' => 'Phone',
            'dob' => 'Dob',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerInterestMasters() {
        return $this->hasMany(CustomerInterestMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteLocationMasters() {
        return $this->hasMany(FavoriteLocationMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriendRequestMasters() {
        return $this->hasMany(FriendRequestMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriendRequestMasters0() {
        return $this->hasMany(FriendRequestMaster::className(), ['friend_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationFootprintMasters() {
        return $this->hasMany(LocationFootprintMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationMasters() {
        return $this->hasMany(LocationMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationShareMasters() {
        return $this->hasMany(LocationShareMaster::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationShareMasters0() {
        return $this->hasMany(LocationShareMaster::className(), ['friend_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRatingMasters() {
        return $this->hasMany(RatingMaster::className(), ['customer_id' => 'id']);
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
