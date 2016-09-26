<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Token;

class TokenController extends Controller
{

    public function __construct()
    {
        //
    }

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
     * @param Request $request
     * @return json
     */
    public function create(Request $request)
    {
        try
        {
            $app_name = $request->header('app_name');
            if(empty($app_name))
            {
                $this->status = 0;
                $this->message = 'Algo errado na sua identificação! entre em contato com o suporte!';
                return $this->returnJson();
            }
        }
        catch (\Exception $x)
        {
            $this->status = 0;
            $this->message = 'Erro ao ler os dados enviados! Verificar a documentação. Erro:tk02';
            return $this->returnJson();
        }

        try
        {
            $user_id = $request->input('id');
            if(empty($user_id) || $user_id <= 0)
            {
                $this->status = 0;
                $this->message = 'Usuário a receber o token o não identificado!';
                return $this->returnJson();
            }
        }
        catch (\Exception $x)
        {
            $this->status = 0;
            $this->message = 'Erro ao ler os dados enviados! Verificar a documentação. Erro:tk02';
            return $this->returnJson();
        }

        try
        {
            if($user_id > 0 && !empty($app_name))
            {
                $exist = Token::where('user_id', $user_id)->where('app_name', $app_name)->where('validated_at', null)->get();
                if(count($exist) > 0)
                {
                    if(count($exist)>1)
                    {
                        foreach ($exist as $item)
                        {
                            if($item->id > 0)
                                Token::find($item->id)->delete();
                        }
                    }
                    else
                    {
                        if(time() > (strtotime($exist[0]->created_at)+30))
                        {
                            if($exist[0]->id > 0)
                                Token::find($exist[0]->id)->delete();
                        }
                        else
                        {
                            $this->status = 1;
                            $this->message = 'Token já criado';
                            return $this->returnJson();
                        }
                    }
                }

                $tk = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

                $ModelToken = new Token();
                $ModelToken->token = $tk;
                $ModelToken->user_id = $user_id;
                $ModelToken->app_name = $app_name;
                $ModelToken->save();

                $this->status = 1;
                $this->message = 'OK!';
                $this->data = ['token' => $tk];
                return $this->returnJson();
            }
            else
            {
                $this->status = 0;
                $this->message = 'Erro ao ler os dados enviados! Verificar a documentação. Erro:tk03';
                return $this->returnJson();
            }
        }
        catch (\Exception $x)
        {
            $this->status = 0;
            $this->message = 'Erro ao ler os dados enviados! Verificar a documentação';
            return $this->returnJson();
        }
    }

    /**
     * valida token
     * @param Request $request
     */
    public function tokenvalidate(Request $request)
    {
        try
        {
            $app_name = $request->header('app_name');
            $user_id = $request->input('id');
            $token = $request->input('token');

            if(empty($app_name))
            {
                $this->status = 0;
                $this->message = 'Algo errado na sua identificação! entre em contato com o suporte!';
                return $this->returnJson();
            }

            if(empty($user_id) || $user_id <= 0)
            {
                $this->status = 0;
                $this->message = 'Usuário a receber o token o não identificado!';
                return $this->returnJson();
            }

            if(empty($token))
            {
                $this->status = 0;
                $this->message = 'token inválido!';
                return $this->returnJson();
            }
        }
        catch (\Exception $x)
        {
            $this->status = 0;
            $this->message = 'Erro ao ler os dados enviados! Verificar a documentação. Erro:527';
            return $this->returnJson();
        }

        $exist = Token::where('user_id', $user_id)
            ->where('app_name', $app_name)
            ->where('validated_at', null)
            ->where('token', $token)
            ->first();
        if(count($exist)>0)
        {
            if(time() > (strtotime($exist->created_at)+120))
            {
                if($exist->id > 0)
                    Token::find($exist->id)->delete();

                $this->status = 0;
                $this->message = 'Token inválido! ';
                return $this->returnJson();
            }
            else
            {
                $exist->validated_at = date('Y-m-d H:i:s');
                $exist->save();

                $this->status = 1;
                $this->message = 'Token Válido! ';
                return $this->returnJson();
            }
        }
        else
        {
            $this->status = 0;
            $this->message = 'Token inválido! ';
            return $this->returnJson();
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
        if(!empty($this->data))
        {
            $ret['data'] = $this->data;
        }
        return json_encode($ret, JSON_UNESCAPED_UNICODE);
    }
}