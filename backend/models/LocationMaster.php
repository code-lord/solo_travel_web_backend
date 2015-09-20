<?php

namespace backend\models;

use Yii;
use yii\helpers\Security;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "location_master".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $lat
 * @property string $lng
 * @property string $cover_photo_url
 * @property integer $interest_id
 * @property integer $customer_id
 * @property integer $status 
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FavoriteLocationMaster[] $favoriteLocationMasters
 * @property LocationFootprintMaster[] $locationFootprintMasters
 * @property InterestMaster $interest
 * @property CustomerMaster $customer
 * @property LocationShareMaster[] $locationShareMasters
 * @property RatingMaster[] $ratingMasters
 */
class LocationMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'location_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'description', 'lat', 'lng', 'interest_id', 'customer_id', 'status'], 'required'],
            [['description'], 'string'],
            [['interest_id', 'customer_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'lat', 'lng'], 'string', 'max' => 45],
            [['cover_photo_url'], 'string', 'max' => 200],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'cover_photo_url' => 'Cover Photo Url',
            'interest_id' => 'Interest ID',
            'customer_id' => 'Customer ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteLocationMasters() {
        return $this->hasMany(FavoriteLocationMaster::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationFootprintMasters() {
        return $this->hasMany(LocationFootprintMaster::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterest() {
        return $this->hasOne(InterestMaster::className(), ['id' => 'interest_id']);
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
    public function getLocationShareMasters() {
        return $this->hasMany(LocationShareMaster::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRatingMasters() {
        return $this->hasMany(RatingMaster::className(), ['location_id' => 'id']);
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
