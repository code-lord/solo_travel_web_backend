<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class MobileController extends \yii\rest\Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionInterests() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {

            $postdata = file_get_contents("php://input");
            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $interests = \backend\models\InterestMaster::find()
                    ->asArray()
                    ->where(['status' => 1])
                    ->all();

            for ($index = 0; $index < count($interests); $index++) {
                $my_interest = \backend\models\CustomerInterestMaster::find()
                        ->select('interest_id')
                        ->asArray()
                        ->where(['customer_id' => $data['customer_id'], 'interest_id' => $interests[$index]['id']])
                        ->all();
                if ($my_interest == NULL) {
                    $interests[$index]['checked'] = false;
                } else {
                    $interests[$index]['checked'] = true;
                }
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'interests' => $interests,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionSharelocation() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            $data = json_decode($postdata, true);

            $location = new \backend\models\LocationShareMaster;
            $location->customer_id = $data['customer_id'];
            $location->friend_id = $data['friend_id'];
            $location->location_id = $data['location_id'];
            $location->status = 1;
            if ($location->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location shared successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionFriendrequest() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            if ($data['customer_id'] == $data['friend_id']) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'You are sending a friend request to yourself!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $is_friend = \backend\models\FriendRequestMaster::find()->where(
                            [
                                'customer_id' => $data['customer_id'],
                                'friend_id' => $data['friend_id']
                    ])->asArray()
                    ->one();
            if ($is_friend != NULL) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Your Friend request already sent !',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }

            $location = new \backend\models\FriendRequestMaster;
            $location->customer_id = $data['customer_id'];
            $location->friend_id = $data['friend_id'];
            $location->status = 1;
            if ($location->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location added successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionSinglefriend() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);


            $friend = \backend\models\CustomerMaster::find()
                    ->where(['id' => $data['friend_id']])
                    ->asArray()
                    ->one();
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'friend' => $friend,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionMyfriends() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $my_location = \backend\models\CustomerMaster::find()->where(['id' => \yii\helpers\ArrayHelper::getColumn(
                                \backend\models\FriendRequestMaster::find()->where(['customer_id' => $data['customer_id'], 'status' => 1])
                                ->select('friend_id')
                                ->asArray()
                                ->all(), 'friend_id'
                )])->asArray()
                    ->andWhere(['status' => 1])
                    ->all();
            for ($index = 0; $index < count($my_location); $index++) {
                $my_location[$index]['checked'] = false;
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'friends' => $my_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionSearchfriends() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $my_location = \backend\models\CustomerMaster::find()
                    ->where('name LIKE :name')
                    ->addParams([':name' => '%' . $data['name'] . '%'])
                    ->asArray()
                    ->andWhere(['status' => 1])
                    ->all();
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'friends' => $my_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionRemovefootprint() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = \backend\models\LocationFootprintMaster::findOne(['customer_id' => $data['customer_id'], 'location_id' => $data['location_id']]);

            if ($location == NULL) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Favorite Not found !'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            if ($location->delete()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location Deleted successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if ($lat1 == $lat2 && $lon1 == $lon2) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "M") {
            return ($miles * 1.609344) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function actionAddfootprint() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = \backend\models\LocationMaster::find()
                    ->where(['id' => $data['location_id']])
                    ->asArray()
                    ->one();
            if ($location == NULL) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Location Not found !'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
//            echo $this->distance($data['user_lat'], $data['user_lng'], $location['lat'], $location['lng'], 'M');
//            exit;
            $distance = $this->distance($data['user_lat'], $data['user_lng'], $location['lat'], $location['lng'], 'M');
            if ($distance > 100) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'You are not near to this location.'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $location = new \backend\models\LocationFootprintMaster;
            $location->customer_id = $data['customer_id'];
            $location->location_id = $data['location_id'];
            if ($location->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'distance' => $distance,
                    'Message' => 'Location added successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionGetlocationfootprint() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $favorite_location = \backend\models\CustomerMaster::find()
                    ->where(['id' => \yii\helpers\ArrayHelper::getColumn(
                                \backend\models\LocationFootprintMaster::find()
                                ->select('customer_id')
                                ->asArray()
                                ->where(['location_id' => $data['location_id']])
                                ->all(), 'customer_id'
                        ), 'status' => 1])
                    ->asArray()
                    ->all();
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'footprints' => $favorite_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionGetfootprint() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $favorite_location = \backend\models\LocationMaster::find()
                    ->where(['id' => \yii\helpers\ArrayHelper::getColumn(
                                \backend\models\LocationFootprintMaster::find()
                                ->select('location_id')
                                ->asArray()
                                ->where(['customer_id' => $data['customer_id']])
                                ->all(), 'location_id'
                        ), 'status' => 1])
                    ->asArray()
                    ->all();
            for ($index = 0; $index < count($favorite_location); $index++) {
                $favorite_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $favorite_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $favorite_location[$index]['id']]);
                $favorite_location[$index]['rating'] = $query->average('rating');
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'favorite_locations' => $favorite_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionMylocations() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $my_location = \backend\models\LocationMaster::find()->where(['customer_id' => $data['customer_id'], 'status' => 1])
                    ->asArray()
                    ->all();
            for ($index = 0; $index < count($my_location); $index++) {
                $my_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $my_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $my_location[$index]['id']]);
                $my_location[$index]['rating'] = $query->average('rating');
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'locations' => $my_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionLocationsinbox() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);


            $query = new \yii\db\Query;
            $query->select([
                        'location_share_master.friend_id AS friend_id',
                        'location_master.id AS id',
                        'location_master.name AS name',
                        'location_master.description AS description',
                        'location_master.cover_photo_url AS cover_photo_url',
                        'customer_master.id as  friend_id',
                        'customer_master.name as  friend_name']
                    )
                    ->from('location_share_master')
                    ->join('INNER JOIN', 'location_master', 'location_share_master.location_id =location_master.id')
                    ->join('INNER JOIN', 'customer_master', 'location_share_master.customer_id =customer_master.id')
                    ->where(['location_share_master.friend_id' => $data['customer_id']]);

            $command = $query->createCommand();
            $my_location = $command->queryAll();
            for ($index = 0; $index < count($my_location); $index++) {
                $my_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $my_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $my_location[$index]['id']]);
                $my_location[$index]['rating'] = $query->average('rating');
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'locations' => $my_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionAddmyintersts() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);

            $myinterest = \backend\models\CustomerInterestMaster::deleteAll('customer_id = :customer_id', [':customer_id' => $data['customer_id']]);
//                    find()->where(['customer_id'=>$data['customer_id']])->all();

            for ($index = 0; $index < count($data['interest']); $index++) {
                if (!$data['interest'][$index]['checked']) {
                    continue;
                }
                $location = new \backend\models\CustomerInterestMaster;
                $location->customer_id = $data['customer_id'];
                $location->interest_id = $data['interest'][$index]['id'];
                if ($location->save()) {
                    
                } else {
                    $transaction->rollback();
                    $Responce = [
                        'Status_code' => '400',
                        'Success' => 'False',
                        'Message' => $location->getErrors(),
                    ];
                    \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
                }
            }
            $transaction->commit();
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'Message' => 'Location added successfully!',
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionMyinterests() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);


            $interest_location = \backend\models\InterestMaster::find()
                    ->where(['id' => \yii\helpers\ArrayHelper::getColumn(
                                \backend\models\CustomerInterestMaster::find()
                                ->select('interest_id')
                                ->asArray()
                                ->where(['customer_id' => $data['customer_id']])
                                ->all(), 'interest_id'
                )])
                    ->asArray()
                    ->all();
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'interests' => $interest_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionGetfavorite() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
//            $favorite_location = \yii\helpers\ArrayHelper::getColumn(
//                            \backend\models\FavoriteLocationMaster::find()
//                                    ->select('location_id')
//                                    ->asArray()
//                                    ->where(['customer_id' => $data['customer_id']])
//                                    ->all(), 'location_id'
//            );
//            $favorite_location = \backend\models\FavoriteLocationMaster::find()
//                    ->select('location_id')
//                    ->asArray()
//                    ->where(['customer_id' => $data['customer_id']])
//                    ->all();
            $favorite_location = \backend\models\LocationMaster::find()
                    ->where(['id' => \yii\helpers\ArrayHelper::getColumn(
                                \backend\models\FavoriteLocationMaster::find()
                                ->select('location_id')
                                ->asArray()
                                ->where(['customer_id' => $data['customer_id']])
                                ->all(), 'location_id'
                )])
                    ->andWhere(['status' => 1])
                    ->asArray()
                    ->all();
            for ($index = 0; $index < count($favorite_location); $index++) {
                $favorite_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $favorite_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $favorite_location[$index]['id']]);
                $favorite_location[$index]['rating'] = $query->average('rating');
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'favorite_locations' => $favorite_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionRemovefavorite() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = \backend\models\FavoriteLocationMaster::findOne(['customer_id' => $data['customer_id'], 'location_id' => $data['location_id']]);
            if ($location == NULL) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Favorite Not found !'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            if ($location->delete()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location Deleted successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionAddfavorite() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = new \backend\models\FavoriteLocationMaster;
            $location->customer_id = $data['customer_id'];
            $location->location_id = $data['location_id'];
            if ($location->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location added successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionAddlocationrating() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = \backend\models\LocationMaster::find()
                    ->where(['id' => $data['location_id']])
                    ->asArray()
                    ->one();
            if ($location == NULL) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Location Not found !'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
//            echo $this->distance($data['user_lat'], $data['user_lng'], $location['lat'], $location['lng'], 'M');
//            exit;
            $distance = $this->distance($data['user_lat'], $data['user_lng'], $location['lat'], $location['lng'], 'M');
            if ($distance > 100) {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'You are not near to this location.'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }

            $rating_old = \backend\models\RatingMaster::findOne(['customer_id' => $data['customer_id'], 'location_id' => $data['location_id']]);
            if ($rating_old == NULL) {
                $rating = new \backend\models\RatingMaster;
                $rating->customer_id = $data['customer_id'];
                $rating->location_id = $data['location_id'];
                $rating->rating = "" . $data['rating'];
                if ($rating->save()) {
                    $transaction->commit();
                    $Responce = [
                        'Status_code' => '200',
                        'Success' => 'True',
                        'Message' => 'Location rated successfully!',
                    ];

                    \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
                } else {
                    $transaction->rollback();
                    $Responce = [
                        'Status_code' => '400',
                        'Success' => 'False',
                        'Message' => $rating->getErrors(),
                    ];
                    \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
                }
            }
            $rating_old->rating = "" . $data['rating'];
            if ($rating_old->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'Message' => 'Location rated successfully!',
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $rating_old->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function getUserbyphone($username) {
        $appuser = \backend\models\CustomerMaster::find()->where(['username' => $username])
                ->asArray()
                ->one();
        if ($appuser != NULL) {
            return $appuser;
        }
        return NULL;
    }

    public function actionRegisterappuser() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);

//
//            $appuser = $this->getUserbyphone($data['username']);
//            if ($appuser != NULL) {
//                $Responce = [
//                    'Status_code' => '200',
//                    'Success' => 'False',
//                    'Message' => 'User with this Username already registered !',
//                ];
//
//                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
//            }


            $appuser = new \backend\models\CustomerMaster;
            $appuser->name = $data['name'];
            $appuser->username = $data['username'];
            $appuser->setPassword($data['password']);
            $appuser->email = $data['email'];
            $appuser->phone = "" . $data['phone'];
            $appuser->dob = $data['dob'];
            $appuser->status = 1;
            if ($appuser->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'app_user_id' => $appuser->id,
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $appuser->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionAddlocation() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $location = new \backend\models\LocationMaster;
            $location->name = $data['name'];
            $location->description = $data['description'];
            $location->lat = $data['lat'];
            $location->lng = $data['lng'];
            $location->cover_photo_url = $data['cover_photo_url'];
            $location->interest_id = $data['interest_id'];
            $location->customer_id = $data['customer_id'];
            $location->status = 1;
            if ($location->save()) {
                $transaction->commit();
                $Responce = [
                    'Status_code' => '200',
                    'Success' => 'True',
                    'location_id' => $location->id,
                ];

                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            } else {
                $transaction->rollback();
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => $location->getErrors(),
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $transaction->rollback();
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $transaction->rollback();
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionSingleinterestlocation() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $interest_location = \backend\models\LocationMaster::find()->where(['interest_id' => $data['interest_id'], 'status' => 1])->asArray()->all();
            for ($index = 0; $index < count($interest_location); $index++) {
                $interest_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $interest_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $interest_location[$index]['id']]);
                $rating = $query->average('rating');
                if ($rating != NULL) {
                    $interest_location[$index]['rating'] = $rating;
                } else {
                    $interest_location[$index]['rating'] = 0;
                }
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'locations' => $interest_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionInterestlocation() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $interest_location = \backend\models\LocationMaster::find()
                            ->where([
                                'interest_id' => \backend\models\CustomerInterestMaster::find()
                                ->select('interest_id')
                                ->asArray()
                                ->where(['customer_id' => $data['customer_id']])
                                ->all()
                                , 'status' => 1])->asArray()->all();
            for ($index = 0; $index < count($interest_location); $index++) {
                $interest_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $interest_location[$index]['cover_photo_url'];
                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $interest_location[$index]['id']]);
                $rating = $query->average('rating');
                if ($rating != NULL) {
                    $interest_location[$index]['rating'] = $rating;
                } else {
                    $interest_location[$index]['rating'] = 0;
                }
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'locations' => $interest_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionGetlocationdetails() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);
            $interest_location = \backend\models\LocationMaster::find()->where(['id' => $data['location_id']])->asArray()->all();
            for ($index = 0; $index < count($interest_location); $index++) {
                $interest_location[$index]['cover_photo_url'] = Yii::$app->getUrlManager()->getHostInfo() . Yii::$app->getUrlManager()->getBaseUrl() . $interest_location[$index]['cover_photo_url'];

                $footprint = \backend\models\LocationFootprintMaster::find()->where(['customer_id' => $data['customer_id'], 'location_id' => $data['location_id']])->asArray()->one();

                if ($footprint == NULL) {
                    $interest_location[$index]['footprint'] = false;
                } else {
                    $interest_location[$index]['footprint'] = true;
                }
                $favorite = \backend\models\FavoriteLocationMaster::find()->where(['customer_id' => $data['customer_id'], 'location_id' => $data['location_id']])->asArray()->one();

                if ($favorite == NULL) {
                    $interest_location[$index]['favorite'] = false;
                } else {
                    $interest_location[$index]['favorite'] = true;
                }

                $query = (new \yii\db\Query())->from('rating_master')->where(['location_id' => $interest_location[$index]['id']]);
                $rating = $query->average('rating');
                if ($rating == NULL) {
                    $interest_location[$index]['rating'] = 1;
                } else {
                    $interest_location[$index]['rating'] = $rating;
                }
            }
            $Responce = [
                'Status_code' => '200',
                'Success' => 'True',
                'location_details' => $interest_location,
            ];

            \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionLoginappuser() {
        \Yii::$app->Codelord_secure->_checkAuth();
        try {
            $postdata = file_get_contents("php://input");

            if ($postdata == '') {
                $Responce = [
                    'Status_code' => '403',
                    'Success' => 'False',
                    'Message' => 'Unathorized Access'
                ];
                \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
            }
            $data = json_decode($postdata, true);


            $appuser = $this->getUserbyphone($data['username']);
            if ($appuser != NULL) {
                if (sha1($data['password']) === $appuser["password"]) {
                    $Responce = [
                        'Status_code' => '200',
                        'Success' => 'True',
                        'Message' => 'Login Successful !',
                        'Customer' => $appuser
                    ];

                    \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
                } else {
                    $Responce = [
                        'Status_code' => '400',
                        'Success' => 'False',
                        'Message' => 'Plese check your Password.',
                    ];
                    \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
                }
            } else {
                $Responce = [
                    'Status_code' => '400',
                    'Success' => 'False',
                    'Message' => 'Plese check your Username.',
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
        } catch (\yii\db\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
        $Responce = [
            'Status_code' => '400',
            'Success' => 'false',
            'Message' => 'unknown error'
        ];

        \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
    }

    public function actionUploadpickupimage() {
//        \Yii::$app->Codelord_secure->_checkAuth();

        try {
            $model = new \backend\models\UploadForm();
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            $filename = $model->random_string(10);
            if ($model->upload($filename)) {
                $Responce = [
                    'Status_code' => "200",
                    'Success' => 'True',
                    'image_url' => "/uploads/" . $filename . '.' . $model->imageFiles[0]->extension,
                ];
                \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
            }
            $Responce = [
                'Status_code' => $_FILES,
                'Success' => 'False',
                'Message' => $model->getErrors(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        } catch (\Exception $e) {
            $Responce = [
                'Status_code' => '400',
                'Success' => 'False',
                'Message' => $e->getMessage(),
            ];
            \Yii::$app->Codelord_secure->_sendResponse(400, $Responce);
        }
    }

    public function actionTest() {
        \Yii::$app->Codelord_secure->_checkAuth();
        $Responce = [
            'Status_code' => '200',
            'Success' => 'True',
            'Message' => 'From My component',
        ];

        \Yii::$app->Codelord_secure->_sendResponse(200, $Responce);
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

}
