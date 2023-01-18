<?php

$file_name = "../db.json";
class DB_JSON {

	private $unique_fields = ['login', 'email'];
	public function __construct($file_name) {
		$this->file_name = $file_name;
	}

	//вернуть все записи в бд (read)
	public function select_all() {
		$json = file_get_contents($this->file_name);
		$data = json_decode($json, true);
		return $data;
	}

	//вернуть все записи в бд у условием
	public function select($conds) {
		$tmp_data = $this->select_all();
		foreach ($tmp_data as $k=>$el) {
			$flag = false;
			foreach ($conds as $c_key => $c_value) {
				if ($el[$c_key]!==$c_value) {
					$flag = true;
					break;
				}
			}
			if ($flag) {
				unset($tmp_data[$k]);
			}

		}
		return $tmp_data;
	}

	//проверить есть ли юзер в бд с таким логином или такой почтой
	public function is_in_db($instance) {
		$data = $this->select(["login"=>$instance['login']]);
		return (empty($this->select(["login"=>$instance['login']])) and empty($this->select(["email"=>$instance['email']])));
		
	}

	// проверить есть ли юзер в бд
	public function is_user($instance) {
		$data = $this->select(["login"=>$instance['login']]);
		return !empty($data);
		
	}

	// проверка правильности пароля
	public function is_correct_password($instance) {
		$user = array_values($this->select(["login"=>$instance['login']]))[0];
		return ($user['password']===$instance['password']);
	}

	// вернуть имя юзера
	public function get_name($instance) {
		return array_values($this->select(["login"=>$instance['login']]))[0]['name'];
	}

	//создать юзера
	public function create($instance) {
		$data = $this->select_all();
		$data = $this->select_all();
		foreach ($data as $user) {
			foreach ($this->unique_fields as $un_f) {
				if ($user[$un_f] === $instance[$un_f]) {
					return false;
				}
			}
		}

		$data[] = $instance;
		file_put_contents($this->file_name, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
		return true;
	}

 	/*
 	обновить юзера (функция не используется, однако я попытался её написать, учтя все тонкости в виде
 	уникальности каждого юзера до и после обновления)
 	*/
	public function update($new_data, array $conds) {
		$data = $this->select_all();
		$indexes;
		if (count(array_diff($this->unique_fields, array_keys($new_data))) < count($this->unique_fields)) {
			//если в new_data есть login или email, то нужны дополнительные проверки
			foreach ($data as $k=>$user) {
				$flag = false;
				foreach ($this->unique_fields as $un_f) {
					if($user[$un_f]===$new_data[$un_f]) {
						$flag = true;
						break;
					}
				}
				if ($flag) {
					$indexes[$k] = $user;
				}

				if(count($indexes)>1) {
					return false;
				}
			}
			if (!empty($indexes)) {
				//если есть юзеры с таким новым login или email
				$cur_user = array_values($indexes)[0];
				$cur_index = array_keys($indexes)[0];
				$flag = true;
				foreach ($conds as $c_key => $c_value) {
					if ($cur_user[$c_key]!== $c_value) {
						$flag = false;
						return true;
						//возвращаю тру потому что просто не найдено пользователей по таким conds, не ошибка
					}
				}
				foreach ($new_data as $n_key => $n_value) {
					$data[$cur_index][$n_key] = $n_value;
				}
			}
			else {
				//если нет, то мы просто меняем первого юзера который удовлетворяет conds. Дальше не меняем потому что в new_data есть login или email
				foreach ($data as $k=>$user) {
					$flag = true;
					foreach ($conds as $c_key => $c_value) {
						if ($user[$c_key]!== $c_value) {
							$flag = false;
							break;
						}
					}
					if ($flag) {
						foreach ($new_data as $key => $value) {
							$data[$k][$key] = $value;
						}
						break;
					}
				}
			}
			
		}
		else {
			//если в new_data нет login и email, просто можно обновить значение юзеров
			foreach ($data as $k=>$user) {
				$flag = true;
				foreach ($conds as $c_key => $c_value) {
					if ($user[$c_key]!== $c_value) {
						$flag = false;
						break;
					}
				}
				if ($flag) {
					foreach ($new_data as $key => $value) {
						$data[$k][$key] = $value;
					}
				}
			}
		}

		file_put_contents($this->file_name, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
		return true;
	}

	//удалить юзера
	public function delete(array $conds) {
		$data = $this->select_all();
		foreach ($data as $user) {
			$flag = true;
			foreach ($conds as $key => $value) {
				if ($user[$key]!==$value) {
					$flag = false;
					break;
				}
			}
			if ($flag) {
				unset($user);
			}
		}

		file_put_contents($this->file_name, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
	}

}