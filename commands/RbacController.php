<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\UserGroupRule;

class RbacController extends Controller {
    public function actionIndex() {
	    $authManager = \Yii::$app->authManager;

	    // Create roles
	    $guest = $authManager->createRole('guest');
	    $user  = $authManager->createRole('user');
	    $admin = $authManager->createRole('admin');

	    // Create simple, based on action{$NAME} permissions
	    $view    = $authManager->createPermission('view');
	    $compare = $authManager->createPermission('compare');

	    // Add permissions in Yii::$app->authManager
	    $authManager->add($view);
	    $authManager->add($compare);

	    // Add rule, based on UserExt->group === $user->group
	    $userGroupRule = new UserGroupRule();
	    $authManager->add($userGroupRule);

	    // Add rule "UserGroupRule" in roles
	    $guest->ruleName = $userGroupRule->name;
	    $user->ruleName  = $userGroupRule->name;
	    $admin->ruleName = $userGroupRule->name;

	    // Add roles in Yii::$app->authManager
	    $authManager->add($guest);
	    $authManager->add($user);
	    $authManager->add($admin);

	    // Add permission-per-role in Yii::$app->authManager
	    $authManager->addChild($user, $view);
	    $authManager->addChild($admin, $compare);
	    $authManager->addChild($admin, $user);
    }
}
