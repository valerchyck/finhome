<?php
/**
 * @var $this   \yii\web\View
 * @var $object \app\models\ObjectsList
 */

use \yii\bootstrap\Html;
use \yii\bootstrap\ActiveForm;
use \kartik\file\FileInput;

$this->title = 'Новый объект';
?>

<div class="row">
	<div class="col-md-6">
		<? $form = ActiveForm::begin([
			'id' => 'new-object-form',
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
				'maxFileCount' => 5,
				'showUpload' => false,
			],
		]) ?>
		<?= $form->field($object, 'link1') ?>
		<?= $form->field($object, 'link2') ?>
		<?= $form->field($object, 'link3') ?>

		<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

		<? $form->end() ?>
	</div>
</div>
