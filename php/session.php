<?php


include 'db.php';


//проверка на ajax запрос
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	session_start();
	if ($_POST['action']==='check') {
		$login = $_SESSION['login'];
		if ($ans = isset($login)) {
			$db = new DB_JSON($file_name);
			$name = $db->get_name(['login'=>$login]);
		};
		echo json_encode(['status'=>$ans,'name'=>$name]);
	}
	else {
		session_destroy();
		exit();
	}
}


