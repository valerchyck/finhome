<?php
/**
 * @var $this   \yii\web\View
 * @var $object \app\models\Object
 */

use \yii\bootstrap\ActiveForm;
use \yii\bootstrap\Html;

$this->title = "Запись №{$object->id}";
?>

<div class="row">
	<div class="col-md-12">
		<? $form = ActiveForm::begin([
			'id'     => 'object-form',
		]) ?>

        <? if (Yii::$app->user->can('admin')): ?>
            <?= $form->field($object, 'date') ?>
            <?= $form->field($object, 'course') ?>
            <?= $form->field($object, 'summa') ?>
            <?= $form->field($object, 'provider') ?>
        <? endif ?>
        <? if (Yii::$app->user->can('admin') || Yii::$app->user->identity->isEdit): ?>
            <?= $form->field($object, 'comment') ?>
        <? endif ?>

		<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

		<? $form->end() ?>
	</div>
</div>
