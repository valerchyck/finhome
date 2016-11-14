<?php
/**
 * @var $this      \yii\web\View
 * @var $data      \yii\data\ActiveDataProvider
 * @var $search    \yii\data\ActiveDataProvider
 * @var $list      \app\models\ObjectsList[]
 * @var $full_sum  integer
 * @var $idList    integer
 */

use \kartik\grid\GridView;
use \yii\bootstrap\Html;
use \yii\helpers\ArrayHelper;
use \kartik\grid\ActionColumn;
use \kartik\widgets\Select2;

$this->title = 'База';
$this->registerJs(<<<JS
	var summary = $('.kv-page-summary td').eq(4).text();
	$('.kv-page-summary td').eq(3).text(parseInt(summary));
JS
);
?>

<div class="row">
	<? if ($list == null): ?>
		<h2>У вас нет доступных объектов</h2>
	<? else: ?>
	<form method="post" id="object-form">
		<div class="col-md-3">
			<label>Объекты</label>
			<?= Select2::widget([
				'name'    => 'lists',
				'value'   => $idList,
				'data'    => ArrayHelper::map($list, 'id', 'name'),
				'options' => [
					'multiple' => true,
				],
			]); ?>
			<input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfTokenFromHeader ?>">
		</div>

		<div class="col-md-1">
			<button type="submit" class="btn btn-primary submit">Применить</button>
		</div>
		<div class="col-md-1">
			<button type="button" onclick="window.location = '/'" class="btn btn-danger submit">Сбросить</button>
		</div>

		<div class="col-md-2">
			<label>Кол-во записей</label>
			<?= Html::dropDownList('page-count', Yii::$app->session->get('page-count'), [20=>20, 50=>50, 100=>100, 200=>200], [
				'class'    => 'form-control',
				'onchange' => "$('#object-form').submit()",
			]) ?>
		</div>

		<? if (Yii::$app->user->can('admin')) :?>
			<div class="col-md-2">
				<label>Общаю сумма объекта</label>
				<div class="sum"><?= $full_sum ?></div>
			</div>
		<? endif ?>
	</form>

	<div class="col-md-12">
		<?= GridView::widget([
			'id'              => 'data',
			'dataProvider'    => $data,
			'filterModel'     => $search,
			'hover'           => true,
			'showPageSummary' => Yii::$app->user->can('admin'),
			'rowOptions'      => function ($model, $key, $index, $grid) {
				return ['style' => "color: {$model->list->font_color}"];
			},
			'columns'         => [
				Yii::$app->request->get('Object') == null ?
				[
					'class'     =>'kartik\grid\BooleanColumn',
					'attribute' => 'mark',
					'value'     => function($model) {
						return $model->mark != 1;
					},
				] : 'list.name',
				[
					'attribute'   => 'date',
					'pageSummary' => 'Сумма',
					'options'     => [
						'class' => 'col-small',
					],
					'filterType'  => GridView::FILTER_DATE_RANGE,
					'filterWidgetOptions' => [
						'convertFormat' => true,
						'pluginOptions' => [
							'locale' => [
								'format'    => 'Y-m-d',
								'separator' => ' - ',
							],
						],
						'options' => [
							'style' => 'width: auto',
						],
					],
				],
				[
					'attribute' => 'course',
					'options'   => [
						'class' => 'mark col-small',
					],
				],
				[
					'attribute' => 'summa',
					'options'   => [
						'class' => 'col-small',
						'style' => 'width: auto',
					],
				],
				[
					'attribute'      => 'sumResult',
					'pageSummary'    => true,
					'contentOptions' => [
						'class' => 'total-summary',
					],
					'hidden'         => true,
				],
                'composition',
				'provider',
				[
					'attribute' => 'info.name',
					'value'     => function($model) {
						return implode("\n", ArrayHelper::getColumn($model->info, 'name'));
					},
					'filter'    => Html::textInput('Object[name]',
						isset(Yii::$app->request->get('Object')['name']) ? Yii::$app->request->get('Object')['name'] : '',
						['class' => 'form-control']),
				],
				[
					'attribute' => 'info.count',
					'value'     => function($model) {
						return implode("\n", ArrayHelper::getColumn($model->info, 'count'));
					},
					'filter'    => Html::textInput('Object[count]',
						isset(Yii::$app->request->get('Object')['count']) ? Yii::$app->request->get('Object')['count'] : '',
						['class' => 'form-control']),
				],
				'comment',
				[
					'hidden'     => !Yii::$app->user->can('admin') && !Yii::$app->user->identity->isEdit,
					'class'      => ActionColumn::className(),
					'template'   => '{update}{delete}',
					'header'     => 'Действие',
					'controller' => 'index',
					'buttons'    => [
						'update' => function($url, $model, $key) {
                            $options = [
                                'title'   => 'Редактировать',
                                'user-id' => $model->id,
                            ];
                            if (!Yii::$app->user->can('admin') && !Yii::$app->user->identity->isEdit)
                                $options += ['class' => 'hidden'];

							return Html::a(Html::icon('pencil'), $url, $options);
						},
						'delete' => function($url, $model, $key) {
                            $options = [
                                'title'   => 'Удалить',
                                'class'   => 'remove-user',
                                'user-id' => $model->id,
                            ];
                            if (!Yii::$app->user->can('admin'))
                                $options['class'] .= ' hidden';

                            return Html::a(Html::icon('trash'), $url, $options);
						},
					],
				],
			],
		]) ?>
	</div>
	<? endif ?>
</div>
