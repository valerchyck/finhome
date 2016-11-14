<?php
/**
 * @var \yii\web\View             $this
 * @var \app\models\Object        $object
 * @var \app\models\ObjectsList[] $list
 */

use \yii\bootstrap\Html;
use \yii\helpers\ArrayHelper;
use \yii\bootstrap\ActiveForm;
use \yii\helpers\Url;

$this->title = 'Загрузка';
?>

<div class="row">
	<div class="col-md-4">
		<? $form = ActiveForm::begin([
			'id' => 'load-xls',
			'options' => [
				'enctype' => 'multipart/form-data',
			],
		]); ?>

		<div class="form-group">
			<?= Html::dropDownList('list', null, ArrayHelper::map($list, 'id', 'name'), ['class' => 'form-control']) ?>
		</div>
		<div class="form-group">
			<?= \kartik\file\FileInput::widget([
				'name' => 'xls',
				'options' => [
					'accept' => '.xls,.xlsx',
					'disabled' => $list == null,
				],
				'pluginOptions' => [
					'showUpload' => false,
				],
			]) ?>
		</div>
		<? if ($list == null): ?>
			<div class="alert alert-danger">Сначала нужно <a href="<?= Url::to(['index/new-object']) ?>">создать объект</a> для загрузки</div>
		<? endif ?>
		<div class="form-group">
			<?= Html::submitButton('Загрузить', ['class' => 'btn btn-success', 'disabled' => $list == null]) ?>
		</div>

		<? $form->end(); ?>
	</div>
</div>
