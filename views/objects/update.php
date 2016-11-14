<?php
/**
 * @var $this   \yii\web\View
 * @var $object \app\models\ObjectsList
 */

use \yii\bootstrap\Html;
use \yii\bootstrap\ActiveForm;
use \kartik\file\FileInput;
use \kartik\widgets\ColorInput;

$this->title = "Объект № {$object->id}";
?>

<div class="row">
	<div class="col-md-6">
		<? $form = ActiveForm::begin([
			'id' => 'update-object-form',
			'options' => [
				'enctype' => 'multipart/form-data',
			],
		]) ?>

		<?= $form->field($object, 'name') ?>
		<?= $form->field($object, 'owner') ?>
		<?= $form->field($object, 'phone_first') ?>
		<?= $form->field($object, 'phone_second') ?>
		<?= $form->field($object, 'images[]')->widget(FileInput::className(), [
			'options' => [
				'accept'   => 'image/*',
				'multiple' => true,
			],
			'pluginOptions' => [
				'showPreview'          => true,
				'maxFileCount'         => 5,
				'showUpload'           => false,
				'overwriteInitial'     => false,
				'initialPreview'       => $object->photos,
				'initialPreviewAsData' => true,
			],
		]) ?>
		<?= $form->field($object, 'link1') ?>
		<?= $form->field($object, 'link2') ?>
		<?= $form->field($object, 'link3') ?>
		<?= $form->field($object, 'font_color')->widget(ColorInput::className(), [
			'options' => [
				'readonly' => true,
			]
		]) ?>

		<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

		<? $form->end() ?>
	</div>
</div>
