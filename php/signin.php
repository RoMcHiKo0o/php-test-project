<?php

include 'User.php';
include 'db.php';
//проверка на ajax запрос
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$db = new DB_JSON($file_name);
	$cur_user = new User($_POST, $db);
	$ser_user = $cur_user->my_serialize();


	$response = check_login($ser_user['login'], $ser_user['password'], $db);

	if ($response['status']) {
		$login = $ser_user['login'];
		setcookie('login', $login, time()+3600);
		session_start();

		$_SESSION['login'] = $login;
	}
	echo json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}

	

//проверка правильности логина и пароля
function check_login($login, $password, $db)  {
	//проверка на логин
	if ($db->is_user(['login'=>$login])) {
		//проверка на правильность пароля
		if ($db->is_correct_password(['login'=>$login, 'password'=>$password])) {
			$result['status'] = true;
			$result['name'] = $db->get_name(['login'=>$login]);
		}
		else {
			$result['status'] = false;
			$result['error'] = 'signin-password-error';
			$result['text'] = 'Неправильный пароль';
		}
	}
	else {
		$result['status'] = false;
		$result['error'] = 'signin-login-error';
		$result['text'] = 'Неправильный логин';
	}
	return $result;
}
