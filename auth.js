//пример другой страницы сайта для проверки работы сессии
$(document).ready(el => {
	//вход на сайт если уже был залогинен
	$.ajax({
		url: 'php/session.php',
		method: 'post',
		dataType: 'json',
		data: {"action": "check"},
		encode: true,
		success: function(data) {
			if (data.status) {
				$('body').text("Hello " + data.name);
			}
		}
	})
})
