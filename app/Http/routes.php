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

#REQUISITAR TOKEN
$app->get(
    'token/',
    [
        'middleware' => ['auth'],
        'as', 'token.create',
        'uses' => 'TokenController@create'
    ]
);
$app->post(
    'token/token',
    [
        'middleware' => ['auth'],
        'as', 'token.create',
        'uses' => 'TokenController@create'
    ]
);
#validat token
$app->get(
    'token/validate',
    [
        'middleware' => ['auth'],
        'as', 'token.validate',
        'uses' => 'TokenController@validate'
    ]
);

#teste
$app->get(
    'token/apptk',
    [
        'as', 'token.apptk',
        //'uses' => 'TokenController@apptk'
        function()
        {
            return json_encode(['tk'=> md5('conektta_mob'.date('Y').date('m').date("d").date("H").'123456789') ], JSON_UNESCAPED_UNICODE);
        }
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