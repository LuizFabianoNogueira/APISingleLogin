<?php
//$http_origin = $_SERVER['HTTP_ORIGIN'];
//header('Access-Control-Allow-Origin:' . $http_origin);
//header('Access-Control-Allow-Credentials: true');

/*
|---------------
| API Conektta |
|---------------
*/

#RAIZ
$app->get('/', function () {
    return 'API Conektta ON-LINE';
});

#LOGIN
$app->post(
    'login/valid',
    [
        'middleware' => ['auth'],
        'as', 'login.valid',
        'uses' => 'LoginController@login'
    ]
);

#CADASTRO DE LOGIN
$app->post(
    'user/register',
    [
        'middleware' => 'auth',
        'as', 'login.register',
        'uses' => 'UserController@register'
    ]
);

#ENDERECO POR CEP
/*
$app->post(
    'cep',
    [
        'middleware' => 'auth',
        'as', 'cep',
        'uses' => 'CepController@busca'
    ]
);
*/

#VALIDA CPF
/*
$app->post(
    'cpf',
    [
        'middleware' => 'auth',
        'as', 'cpf',
        'uses' => 'CpfcnpjController@cpf'
    ]
);
*/

#VALIDA CNPJ
/*
$app->post(
    'cnpj',
    [
        'middleware' => 'auth',
        'as', 'cnpj',
        'uses' => 'CpfcnpjController@cnpj'
    ]
);
*/