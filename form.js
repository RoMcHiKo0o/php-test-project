
$(document).ready(el => {
	//вход на сайт если юзер уже был залогинен
	$.ajax({
		url: 'php/session.php',
		method: 'post',
		dataType: 'json',
		data: {"action": "check"},
		encode: true,
		success: function(data) {
			if (data.status) {
				//скрывает формы и отображает привествие с кнопкой "Выйти"
				$('#auth').text("Hello " + data.name);
				$("input").each( function(i, el) { el.value = ''});

				$('.auth').removeClass("not-auth");


				$('.register-form').addClass("not-auth");
				$('.signin-form').addClass("not-auth");
			}
		}
	})
})


// Регистрация
$(".register-form").submit(function (event) {

	event.preventDefault();
	
	var post_data = {
		"login": $(".login").val(),
		"password": $(".password").val(),
		"confirm-password": $(".confirm-password").val(),
		"email": $(".email").val(),
		"name": $(".name").val()
	};

	$.ajax({
		url: 'php/register.php',
		method: 'post',
		dataType: 'json',
		data: post_data,
		//encode: true,
		success: function(data) {
			console.log(data);
			login(data);
		},
		error: function() {
			console.log("Ошибка отправки");
		}
	}); 
})

function login(data) {
	//вывод ошибок в соответсвующие поля, либо сообщение об успешной регистрации
	$('.error').hide();
	$('.signin-error').hide();
	$(".success-register").text("");
	console.log(data);
	if (!data.status) {
		data.errors.forEach((el) =>{
			$(`.${el.error}`).show().text(el.text);
		});
	}
	else {
		$(".success-register").text("Вы успешно зарегистрированы");
		$("input").each( function(i, el) { el.value = ''});
	}
}


//Авторизация
$(".signin-form").submit(function (event) {

	event.preventDefault();

	var post_data = {
		"login": $(".signin-login").val(),
		"password": $(".signin-password").val()
	};
	$.ajax({
		url: 'php/signin.php',
		method: 'post',
		dataType: 'json',
		data: post_data,
		encode: true,
		success: function(data) {
			signin(data);
		},
		error: function() {
			console.log("Ошибка отправки");
		}
	}); 
})



function signin(data) {
	//вывод ошибок в соответсвующие поля, либо (при успешной авторизации) скрывает формы и отображает приветствие с кнопкой "Выйти"
	$(".success-register").text("");
	$('.signin-error').hide();
	$('.error').hide();
	if (!data.status) {
		$(`.${data.error}`).show().text(data.text);
	}
	else {

		$('#auth').text("Hello " + data.name);
		$("input").each( function(i, el) { el.value = ''});


		$('.auth').removeClass("not-auth");


		$('.register-form').addClass("not-auth");
		$('.signin-form').addClass("not-auth");
		
	}
}


// Выход из аккаунта
$("#logout-btn").click(function () {

	$('#auth').text("");
	$('.auth').addClass("not-auth");

	$.ajax({
		url: 'php/session.php',
		method: 'post',
		dataType: 'json',
		data: {"action": "logout"},
		encode: true,
		success: function(data) {
			if (data.status) {
				//отображает формы, скрывает кнопку и приветствие
				$('#auth').text("Hello " + data.name);
				$("input").each( function(i, el) { el.value = ''});

				$('.auth').removeClass("not-auth");


				$('.register-form').addClass("not-auth");
				$('.signin-form').addClass("not-auth");
			}
		}
	});
	$('.register-form').removeClass("not-auth");
	$('.signin-form').removeClass("not-auth");
})