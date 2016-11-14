<?php
/**
 * @var $this \yii\web\View
 * @var $user \app\models\User
 * @var $url  string
 */

use \yii\helpers\Html;
use \kartik\widgets\SwitchInput;
use \yii\widgets\ActiveForm;

$this->title = 'Новый пользователь';
?>

<div class="row">
	<div class="col-md-12">
		<? $form = ActiveForm::begin([
			'id'     => 'new-user-form',
			'action' => $url,
		]) ?>

		<?= $form->field($user, 'login') ?>
		<?= $form->field($user, 'open_pass')->passwordInput() ?>
		<?= $form->field($user, 'repeatPassword')->passwordInput() ?>
		<?= $form->field($user, 'name') ?>
		<?= $form->field($user, 'phone') ?>
		<?= $form->field($user, 'email') ?>
		<?= $form->field($user, 'skype') ?>
		<?= $form->field($user, 'role')->widget(SwitchInput::className(), [
			'pluginOptions' => [
				'onText'  => 'Админ',
				'offText' => 'Юзер',
			],
		]) ?>

		<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

		<? $form->end() ?>
	</div>
</div>
