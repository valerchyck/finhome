<?php
/**
 * @var $this \yii\web\View
 * @var $data \yii\data\ActiveDataProvider
 */

use \yii\bootstrap\Html;
use \kartik\grid\GridView;
use \yii\bootstrap\Modal;
use \app\models\User;
use \kartik\grid\ActionColumn;

$this->title = 'Пользователи';
?>

<?= GridView::widget([
	'dataProvider' => $data,
	'id'           => 'users',
	'pjax'         => true,
	'toolbar'      => [
		[
			'content' => Html::button(
				'<i class="glyphicon glyphicon-plus"></i>',
				[
					'type'    => 'button',
					'class'   => 'btn btn-success',
					'onclick' => "$('#new-user-modal').modal('show')",
				]),
		],
	],
	'panel' => [
		'type' => GridView::TYPE_PRIMARY,
	],
	'columns' => [
		'login',
		'name',
		'phone',
		'email',
		'skype',
		[
			'class' => ActionColumn::className(),
			'template' => '{access}{update}{delete}',
			'header' => 'Действие',
			'controller' => 'user',
			'buttons' => [
				'access' => function($url, $model, $key) {
					if ($model->role == 1)
						return '';

					return Html::a(Html::icon('cog'), '#', [
						'title'   => 'Доступ',
						'class'   => 'user-objects',
						'onclick' => "showAccessModal($model->id)",
					]);
				},
				'update' => function($url, $model, $key) {
					return Html::a(Html::icon('pencil'), $url, [
						'title'   => 'Редактировать',
						'user-id' => $model->id,
					]);
				},
				'delete' => function($url, $model, $key) {
					return Html::a(Html::icon('trash'), $url, [
						'title'   => 'Удалить',
						'class'   => 'remove-user',
						'user-id' => $model->id,
					]);
				},
			],
		],
	],
]) ?>

<? Modal::begin([
	'id' => 'new-user-modal',
]) ?>
<?= $this->render('form', ['user' => new User(), 'url' => \yii\helpers\Url::to('user/create')]) ?>
<? Modal::end() ?>

<? Modal::begin([
	'id'     => 'user-objects-modal',
	'header' => '<h4>Доступные объекты</h4>',
	'size'   => Modal::SIZE_SMALL,
]) ?>
<? Modal::end() ?>
