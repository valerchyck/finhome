<?php
/**
 * @var $this \yii\web\View
 * @var $data \yii\data\ActiveDataProvider
 */

use \kartik\grid\GridView;
use \yii\bootstrap\Html;
use \yii\grid\ActionColumn;
use \yii\helpers\Url;

$this->title = 'Объекты';
?>

<?= GridView::widget([
	'dataProvider' => $data,
	'pjax'         => true,
	'hover'        => true,
	'toolbar'      => [
		[
			'content' => Html::a(
				'<i class="glyphicon glyphicon-plus"></i>',
				Url::to(['objects/create']), ['class'   => 'btn btn-success']
			),
		],
	],
	'panel'        => [
		'type' => GridView::TYPE_PRIMARY,
	],
	'columns'      => [
		'name',
		'owner',
		'phone_first',
		'phone_second',
		'link1',
		'link2',
		'link3',
		[
			'class'      => ActionColumn::className(),
			'template'   => '{update}{delete}',
			'header'     => 'Действие',
			'controller' => 'objects',
			'buttons'    => [
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
]);
