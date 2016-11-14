<?php
namespace app\controllers;

use app\models\Object;
use app\models\ObjectsList;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ObjectsController extends \yii\web\Controller {
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'   => true,
						'roles'   => ['admin'],
					],
				],
				'denyCallback' => function($rule, $action) {
					if (\Yii::$app->user->isGuest)
						return $this->redirect('/login');

					throw new NotFoundHttpException('Страница не найдена');
				},
			],
		];
	}

    public function actionIndex() {
	    $data = new ActiveDataProvider([
		    'query' => ObjectsList::find(),
	    ]);

	    return $this->render('index', [
		    'data' => $data,
	    ]);
    }

	public function actionCreate() {
		$object = new ObjectsList();
		if (($data = \Yii::$app->request->post('ObjectsList')) != null) {
			$object->setAttributes($data);

			$object->images = UploadedFile::getInstances($object, 'images');
			if ($object->images != null)
				$object->upload();

			$object->save();

			return $this->redirect('/objects', 301);
		}

		return $this->render('create', [
			'object' => $object,
		]);
	}

	public function actionUpdate($id) {
		$object = ObjectsList::findOne(['id' => $id]);
		if ($object == null)
			throw new NotFoundHttpException('object list not found');

		if (($data = \Yii::$app->request->post('ObjectsList')) != null) {
			$object->setAttributes($data);
			$object->save();

			return $this->redirect('/objects', 301);
		}

		return $this->render('update', [
			'object' => $object,
		]);
	}

	public function actionDelete($id) {
		ObjectsList::deleteAll(['id' => $id]);
		Object::deleteAll(['id_list' => $id]);

		return $this->redirect('/objects', 301);
	}
}
