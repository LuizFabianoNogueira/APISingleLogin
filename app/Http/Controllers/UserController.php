<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $status;
    private $message;
    private $validate;
    private $post_data;
    private $data;

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
     * Registra novo usuario
     * @param Request $request inputs
     * @return json
     */
    public function register(Request $request)
    {
        if($this->validFields($request))
        {
            $ObjUser = new User;
            $ObjUser->fill($this->post_data);
            if(!isset($ObjUser->level_of_access) || empty($ObjUser->level_of_access))
            {
                $AppList = new AppsListController();
                $ObjUser->level_of_access = $AppList->getLevelOfAccess($request->header('app_name'));
            }

            $ObjUser->active = 1;

            try
            {
                $ObjUser->save();
            }
            catch (\Exception $e)
            {
                $this->status = 0;
                $this->message = "Ooops algo saiu errado!";
            }

            if($ObjUser->id)
            {
                $this->status = 1;
                $this->message = "Sucesso!";
                $this->data = $ObjUser->toArray();
            }
            else
            {
                $this->status = 0;
                $this->message = "Falha ao salvar dados!";
            }
        }
        return $this->returnJson();
    }


    /**
     * Valida campos
     * @param $request
     * @return bool
     */
    private function validFields($request)
    {
        #field name
        try
        {
            $this->post_data['name'] = $request->input('name');
            if(!isset($this->post_data['name']) || empty($this->post_data['name']))
            {
                $this->validate['name'] = "O campo nome é obrigatório!";
            }
        }
        catch (\Exception $e)
        {
            $this->validate['name'] = "O campo nome deve ser informado!";
        }

        try
        {
            $this->post_data['login'] = $request->input('login');
            if(!empty($this->post_data['login']))
            {
                $ObjVeryLogin = User::where('login', $this->post_data['login'])->first();
                if($ObjVeryLogin)
                {
                    $this->validate['login'] = "Este Login já esta em uso!";
                }
            }
        }
        catch (\Exception $e)
        {
            //Campo Opcional
        }

        try
        {
            $this->post_data['email'] = $request->input('email');
            if(!empty($this->post_data['email']))
            {
                $ObjVeryLogin = User::where('email', $this->post_data['email'])->first();
                if($ObjVeryLogin)
                {
                    $this->validate['email'] = "Este e-mail já esta em uso!";
                }
            }
            else
            {
                $this->validate['email'] = "O campo email deve ser informado!";
            }
        }
        catch (\Exception $e)
        {
            $this->validate['email'] = "O campo email deve ser informado!";
        }

        try
        {
            $this->post_data['phone'] =preg_replace("/[^0-9]/", "", $request->input('phone'));
            if(!empty($this->post_data['phone']))
            {
                $ObjVeryLogin = User::where('phone', $this->post_data['phone'])->first();
                if($ObjVeryLogin)
                {
                    $this->validate['phone'] = "Este telefone já esta em uso!";
                }
            }
        }
        catch (\Exception $e)
        {
            //Campo Opcional
        }

        try
        {
            $this->post_data['password'] = $request->input('password');
            if(empty($this->post_data['password']))
            {
                $this->validate['password'] = "O campo senha é obrigatório!";
            }
            else
            {
                $this->post_data['password'] = Hash::make($this->post_data['password']);
                $this->post_data['remember_token'] = Hash::make($this->post_data['password']);
            }
        }
        catch (\Exception $e)
        {
            $this->validate['password'] = "O campo password deve ser informado!";
        }

        try
        {
            $this->post_data['level_of_access'] = $request->input('level_of_access');
        }
        catch (\Exception $e)
        {
            //Campo Opcional
        }

        /*
        if(empty($this->validate))
        {
            if (
                (!isset($this->post_data['login']) || empty($this->post_data['login'])) &&
                (!isset($this->post_data['email']) || empty($this->post_data['email'])) &&
                (!isset($this->post_data['phone']) || empty($this->post_data['phone']))
            )
            {
                $this->validate['login'] = "Você deve preencher login ou e-mail ou telefone como seu login!";
                $this->validate['email'] = "Você deve preencher login ou e-mail ou telefone como seu login!";
                $this->validate['phone'] = "Você deve preencher login ou e-mail ou telefone como seu login!";
            }
        }
        */

        /*
        if(empty($this->validate))
        {
            if (
                (!isset($this->post_data['email']) || empty($this->post_data['email'])) &&
                (!isset($this->post_data['phone']) || empty($this->post_data['phone']))
            )
            {
                $this->validate['email'] = "Você deve preencher e-mail ou telefone como seu meio de comunicação!";
                $this->validate['phone'] = "Você deve preencher e-mail ou telefone como seu meio de comunicação!";
            }
        }
        */

        #validar campos variavel
        if(!empty($this->post_data['login']))
        {
            if(strlen($this->post_data['login'])>250)
            {
                $this->validate['login'] = "Campo login deve conter no máximo 250 caracteres!";
            }
        }

        if(!empty($this->post_data['email']))
        {
            if (!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$this->post_data['email']))
            {
                $this->validate['email'] = "Campo email inválido!";
            }
        }

        if(!empty($this->post_data['phone']))
        {
            if(is_numeric($this->post_data['phone']))
            {
                if(strlen($this->post_data['phone']) < 10 || strlen($this->post_data['phone']) > 20)
                {
                    $this->validate['phone'] = "Campo telefone inválido!";
                }
            }
            else
            {
                $this->validate['phone'] = "Campo telefone deve conter apenas números!";
            }
        }

        if(!empty($this->post_data['level_of_access']))
        {
            if(!is_int($this->post_data['level_of_access']))
            {
                $this->validate['level_of_access'] = "Campo nível de acesso deve conter apenas números!";
            }
        }

        if(!empty($this->validate))
        {
            $this->status = 0;
            $this->message = "Formulário com erros!";
            return false;
        }
        else
        {
            return true;
        }
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
        if(!empty($this->validate))
        {
            $ret['validate'] = $this->validate;
        }
        if(!empty($this->data))
        {
            $ret['data'] = $this->data;
        }
        return json_encode($ret, JSON_UNESCAPED_UNICODE);
    }
}