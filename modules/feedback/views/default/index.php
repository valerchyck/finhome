<?php
/**
 * @var $this     \yii\web\View
 * @var $feedback \app\modules\feedback\models\Feedback
 */

use \yii\bootstrap\ActiveForm;
use \yii\bootstrap\Html;
use \kartik\widgets\FileInput;

$this->title = 'Обратная связь';
?>

<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">
			<? $form = ActiveForm::begin([
				'id'      => 'feedback-form',
				'options' => [
					'enctype' => 'multipart/form-data',
				],
			]) ?>
			<?= $form->field($feedback, 'name') ?>
			<?= $form->field($feedback, 'email') ?>
			<?= $form->field($feedback, 'text')->textarea() ?>
			<?= $form->field($feedback, 'files[]')->widget(FileInput::className(), [
				'options'       => [
					'multiple' => true,
				],
				'pluginOptions' => [
					'showPreview'      => true,
					'maxFileCount'     => 5,
					'showUpload'       => false,
					'overwriteInitial' => false,
				],
			]) ?>

			<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
			<? $form->end() ?>
		</div>

		<div class="alert alert-info col-md-6">
			На этой странице Вы можете писать свои предложения/замечания, найденые ошибки
		</div>
	</div>
</div>
