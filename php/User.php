<?php


class User {

	private $user_data;
	private $login;
	private $password;
	private $conf_password;
	private $email;
	private $name;
	private $db;
	private $response;
	private $salt;


	public function __construct ($data, $db) {
		$this->login = $data['login'];
		$this->password = $data['password'];
		$this->conf_password = $data['confirm-password'];
		$this->email = $data['email'];
		$this->name = $data['name'];
		$this->db = $db;
		$this->user_data = $data;
		$this->response["errors"] = [];
		$this->salt = "asddahsdfkjsdfh4";
	}

	private function validate_login() {
		if (mb_strlen($this->login)<6 || preg_match('/\s/', $this->login)) {
			$this->response['errors'][] = [
				"error"=> "login-error",
				"text"=> "Логин должен быть минимум 6 символов в длину, без пробелов"
			];
		}
	}
	private function validate_password() {
		if (mb_strlen($this->password)<6 || !(preg_match("/[a-zA-Z]+/", $this->password) and preg_match("/[0-9]+/", $this->password)) || preg_match('/\s/', $this->password) || preg_match("/[!@#$%^&*()_\-=+?:;№)]+/", $this->password)) {
				$this->response['errors'][] = [
				"error"=> "password-error",
				"text"=> "Пароль должен быть минимум 6 символов , обязательно должны состоять из цифр и букв"
			];
		}
	}

	private function validate_conf_password() {
		if ($this->password !== $this->conf_password) {
			$this->response['errors'][] = [
				"error"=> "confirm-password-error",
				"text"=> "Пароли должны совпадать"
			];
		}
	}

	private function validate_email() {
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
  			$this->response['errors'][] = [
				"error"=> "email-error",
				"text"=> "Неверна введена почта"
			];
  		}
	}

	private function validate_name() {
		if (mb_strlen($this->name) <2 || preg_match("/[^a-zA-Z]+/", $this->name) || preg_match('/\s/', $this->name)) {
			$this->response['errors'][] = [
				"error"=> "name-error",
				"text"=> "Имя должно быть минимум 2 символа, только буквы"
			];
		}
	}

	private function validate_db() {
		$res = $this->db->is_in_db($this->user_data);
		if (!$res) {
			$this->response["Name"] = $this->name;
			$this->response['status'] = true;
		}
		else {
			$this->response["errors"][] = [
				"error" => 'db-error',
				"text" => "Пользователь с таким логином или почтой уже существует"
			];
			$this->response['status'] = false;
		}
	}

	public function validate() {
		$this->validate_login();
		$this->validate_password();
		$this->validate_conf_password();
		$this->validate_email();
		$this->validate_name();
		if (count($this->response['errors'])!=0) {
			$this->response['status'] = false;
		}
		else {
			$this->validate_db();
		}
		return $this->response;
	}

	public function my_serialize() {
		return [
			'login' => $this->login,
			'password' => md5($this->salt.$this->password),
			'email' => $this->email,
			'name' => $this->name
		];
	}
}