<?php

include 'User.php';
include 'db.php';

//проверка на ajax запрос
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

	$db = new DB_JSON($file_name);
	$cur_user = new User($_POST, $db);

	//валидация
	$response = $cur_user->validate();

	if ($response['status']===true) {
		$db->create($cur_user->my_serialize());
	}
	echo json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}