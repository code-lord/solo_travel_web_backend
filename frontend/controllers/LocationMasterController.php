<?php

namespace frontend\controllers;

use Yii;
use frontend\models\LocationMaster;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LocationMasterController implements the CRUD actions for LocationMaster model.
 */
class LocationMasterController extends Controller {

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

    /**
     * Lists all LocationMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => LocationMaster::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LocationMaster model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LocationMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new LocationMaster();
        $upload_model = new \backend\models\UploadForm;
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            $model->cover_photo_url = $this->uploadDLocationdphoto();


            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            if ($model->save()) {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                echo json_encode($model->errors);
                exit;
                $transaction->rollback();

                return $this->render('create', [
                            'model' => $model,
                            'upload_model' => $upload_model,
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'upload_model' => $upload_model,
            ]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    private function uploadDLocationdphoto() {
        try {
            $model = new \backend\models\UploadForm;
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            $filename = $model->random_string(10);
            if ($model->upload($filename)) {
                return "/uploads/" . $filename . '.' . $model->imageFiles[0]->extension;
//                $Responce = [
//                    'Status_code' => "200",
//                    'Success' => 'True',
//                    'image_url' => Yii::$app->getUrlManager()->getBaseUrl() . "/uploads/" . $model->profile_photo[0]->name,
//                ];
//                echo json_encode($Responce);
//                exit;
            }
            return "Not Set";
//            $Responce = [
//                'Status_code' => $_FILES,
//                'Success' => 'False',
//                'Message' => $model->getErrors(),
//            ];
//            echo json_encode($Responce);
//            exit;
        } catch (\Exception $e) {
            return "Not Set";
//            $Responce = [
//                'Status_code' => '400',
//                'Success' => 'False',
//                'Message' => $e->getMessage(),
//            ];
//            echo json_encode($Responce);
//            exit;
        }
    }

    /**
     * Updates an existing LocationMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LocationMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LocationMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = LocationMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
