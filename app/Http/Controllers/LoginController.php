<?php

namespace App\Http\Controllers;

use App\Models\AuthUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Variavel para receber dados do usuario a ser retornado po json
     * @var array
     */
    private $data;

    /**
     * define se resultado positivo 0 ou 1
     * @var int
     */
    private $status;

    /**
     * mensagem de retorno
     * @var string
     */
    private $message;

    /**
     * @var local | login a ser validado
     */
    private $str_login;

    /**
     * @var local | senha a ser validada
     */
    private $password;

    /**
     * @var string LOCAL | identifica qual campo da tabela deve ser comparado para login
     */
    private $field_login = "login";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * login
     * @param Request $request inputs login | password
     * @return json
     */
    public function login(Request $request)
    {
        try
        {
            $this->str_login = $request->input('login');
            $this->password = $request->input('password');
        }
        catch (Exception $e)
        {
            $this->status = 0;
            $this->message = 'informe o usuário e a senha!';
            return $this->returnJson();
        }

        if(empty($this->str_login) || empty($this->password))
        {
            $this->status = 0;
            $this->message = 'informe o usuário e a senha!';
            return $this->returnJson();
        }

        $this->loginValid();
        return $this->returnJson();
    }

    /**
     * Valida login e carrega dados caso exista
     * @return bool
     */
    private function loginValid()
    {
        $this->identifiesLogin();
        $ObjUser = AuthUsuario::where($this->field_login, $this->str_login)->first();
        if($ObjUser)
        {
            if(Hash::check($this->password, $ObjUser->password))
            {
                $this->status = 1;
                $this->message = "Sucesso!";
                $this->data = $ObjUser->toArray();
            }
            else
            {
                $this->status = 0;
                $this->message = "Dados incorreto!";
            }
        }
        else
        {
            $this->status = 0;
            $this->message = "Você não é um cliente Conektta!";
        }

        return $this->returnJson();
    }

    /**
     * Identifica o campo a forma de login login | phone | email
     * @return void
     */
    private function identifiesLogin()
    {
        $l = $this->str_login;
        if($this->isEmail($l))
        {
            $this->field_login = "email";
        }
        if($this->isPhone($l))
        {
            $this->field_login = "phone";
        }
    }

    /**
     * verifica se login tem formato de email
     * @param $l - tring com login
     * @return bool
     */
    private function isEmail($l)
    {
        if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$l))
        {
            return true;
        }
        return false;
    }

    /**
     * verifica se login tem formato de telefone
     * @param $l - string com login
     * @return bool
     */
    private function isPhone($l)
    {
        $n = preg_replace("/[^0-9]/", "", $l);
        if(strlen($n) > 10 && strlen($n) < 20)
        {
            return true;
        }
        return false;
    }

    /**
     * Monta retorno
     * @return json
     */
    private function returnJson()
    {
        $ret = [
            'status' => $this->status,
            'message' => $this->message
        ];
        if(!empty($this->data))
        {
            $ret['data'] = $this->data;
        }
        return json_encode($ret, JSON_UNESCAPED_UNICODE);
    }
}
