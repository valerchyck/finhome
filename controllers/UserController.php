<?php
namespace app\controllers;

use app\models\ObjectsList;
use app\models\User;
use app\models\UsersObjects;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller {
	public function actions() {
		return [
			'error'  => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['create', 'index', 'update', 'delete', 'download', 'new-object', 'access', 'save-access'],
						'allow'   => true,
						'roles'   => ['admin'],
					],
					[
						'actions' => ['login'],
						'allow'   => true,
						'roles'   => ['?'],
					],
					[
						'actions' => ['logout'],
						'allow'   => true,
						'roles'   => ['@'],
					],
					[
						'actions' => ['error'],
						'allow' => true,
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
		    'query'      => User::find(),
		    'pagination' => [
			    'pageSize' => 20,
		    ],
	    ]);

	    return $this->render('index', [
		    'data' => $data,
	    ]);
    }

	public function actionCreate() {
		if (\Yii::$app->request->post('User') != null) {
			$user = new User();
			if ($user->load(\Yii::$app->request->post()) && $user->validate()) {
				$user->save();
			}
		}

		return $this->redirect(Url::toRoute('index'), 301);
	}

	public function actionUpdate($id) {
		$user = User::findOne(['id' => $id]);
		if ($user == null)
			throw new NotFoundHttpException('user not found');

		if (($data = \Yii::$app->request->post('User')) != null) {
			$user->setAttributes($data);
			$user->save();

			return $this->redirect('/users', 301);
		}

		return $this->render('form', [
			'user' => $user,
			'url'  => Url::toRoute(['update', 'id' => $id]),
		]);
	}

	public function actionDelete($id) {
		User::deleteAll(['id' => $id]);

		return $this->redirect('/users', 301);
	}

	public function actionAccess($id) {
		$user = User::findOne(['id' => $id]);
		if ($user == null)
			throw new NotFoundHttpException('user not found');

		return $this->renderAjax('access', [
			'lists' => ObjectsList::find()->all(),
			'user'  => $user,
		]);
	}

	public function actionSaveAccess($id) {
		$user = User::findOne(['id' => $id]);
		if ($user == null)
			throw new NotFoundHttpException('user not found');

        User::updateAll(['isEdit' => \Yii::$app->request->post('isEdit')], ['id' => $id]);
        if (($data = \Yii::$app->request->post('data')) == null)
			return true;

		return $user->saveAccess($data);
	}
}
