<?php
namespace app\controllers;

use app\models\Helper;
use app\models\LoginForm;
use app\models\Object;
use app\models\ObjectsList;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class IndexController extends Controller {
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
						'actions' => ['download', 'objects', 'update', 'delete'],
						'allow'   => true,
						'roles'   => ['admin'],
					],
					[
						'actions' => ['login'],
						'allow'   => true,
						'roles'   => ['?'],
					],
					[
						'actions' => ['logout', 'index', 'update'],
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
		$idList = [];
		$object = new Object();
		$query  = ObjectsList::find()
			->select(['ol.id', 'ol.name'])
			->from(['ol' => ObjectsList::tableName()])
			->innerJoin('object o', 'ol.id = o.id_list')
			->groupBy('ol.id');

		if (!\Yii::$app->user->can('admin')) {
			$query->innerJoin('users_lists ul', 'ul.id_list = o.id_list')
				 ->where(['ul.id_user' => \Yii::$app->user->id]);
		}
		$list = $query->all();

		if (\Yii::$app->request->post('lists') != null) {
			$idList = \Yii::$app->request->post('lists');
			\Yii::$app->session->set('lastList', $idList);
		}
		else if (\Yii::$app->session->get('lastList') != null) {
			$idList = \Yii::$app->session->get('lastList');
		}
		else if ($list != null) {
			$idList = [$list[0]->id];
		}

		if (\Yii::$app->request->post('page-count') != null) {
			\Yii::$app->session->set('page-count', \Yii::$app->request->post('page-count'));
		}

		return $this->render('index', [
			'full_sum' => (int)Object::find()->where(['id_list' => $idList])->sum('summa / course'),
			'data'     => $object->search($idList, \Yii::$app->request->get()),
			'search'   => $object,
			'list'     => $list,
			'idList'   => $idList,
		]);
	}

	public function actionUpdate($id) {
        if (!\Yii::$app->user->can('admin') && !\Yii::$app->user->identity->isEdit)
            throw new ForbiddenHttpException('access denied');

		$object = Object::findOne(['id' => $id]);
		if ($object == null)
			throw new NotFoundHttpException('object not found');

		if (($data = \Yii::$app->request->post('Object')) != null) {
			$object->setAttributes($data);
			$object->save();

			return $this->redirect('/');
		}

		return $this->render('update', [
			'object' => $object,
		]);
	}

	public function actionDelete($id) {
		Object::deleteAll(['id' => $id]);

		return $this->redirect('/', 301);
	}

	public function actionLogin() {
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(\Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}

		return $this->render('login', [
			'model' => $model,
		]);
	}

	public function actionLogout() {
		\Yii::$app->user->logout();

		return $this->goHome();
	}

	public function actionDownload() {
		if (!empty($_FILES['xls']['tmp_name']) &&
			Helper::checkExtension($_FILES['xls']['name'], ['xls', 'xlsx']) &&
			($listId = \Yii::$app->request->post('list')) != null) {
			$filename = Helper::xlsToCsv($_FILES['xls']['tmp_name']);

			/* parsing CSV and insert to DB. begin */
			$data = array_map('str_getcsv', file($filename));
			if (Helper::isEmpty($data))
				throw new NotFoundHttpException('file is empty');

			$columns = $data[1];
			unset($data[0], $data[1]);

			$objects = [];
			foreach ($data as $number => $item) {
				$object = $item;
				$pos    = count($objects);

				if ($item[0] == null) {
					$pos--;
					$object = $objects[$pos];
				}

				foreach ($item as $key => $value) {
					if (in_array($key, [5, 6])) {
						if (isset($object[$key]) && is_array($object[$key]))
							array_push($object[$key], $value);
						else
							$object[$key] = [
								$value,
							];
					}
				}

				$objects[$pos] = $object;
			}

			$list = ObjectsList::findOne(['id' => $listId]);
			if ($list == null)
				throw new NotFoundHttpException('list not found');

			Object::deleteAll(['id_list' => $listId]);
			foreach ($objects as $key => $object) {
				$model = new Object(['id_list' => $listId]);
				$model->save(true, $object, $columns);
			}
		}

		return $this->render('download', [
			'object' => new Object(),
			'list'   => ObjectsList::find()->all(),
		]);
	}
}
