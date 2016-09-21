<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AppsListController;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * @var local | nome do aplicativo que esta acessando
     */
    protected $app_name;

    /**
     * @var local | token do aplicativo que esta acessando
     */
    protected $app_token;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $this->app_name = $request->header('app_name');
        $this->app_token = $request->header('app_token');
        if($this->appValid())
        {
            if(!$this->tokenValid())
            {
                return response('Unauthorized. -t', 401);
            }
        }
        else
        {
            return response('Unauthorized. -u', 401);
        }
        return $next($request);
    }

    /**
     * valida app
     * @param $app
     * @return bool
     */
    private function appValid()
    {
        $ObjApps = new AppsListController();
        $AppList = $ObjApps->getAppNames();

        if(in_array($this->app_name, $AppList))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * valida token
     * @param $token
     * @return bool
     */
    private function tokenValid()
    {
        return (trim($this->app_token) == trim($this->tk()) ? true:false);
    }

    /**
     * string md5
     * @return string md5
     */
    private function tk()
    {
        $ObjApps = new AppsListController();
        $Apps = $ObjApps->get();
        $key = $Apps[$this->app_name]['key'];

        return md5($this->app_name.date('Y').date('m').date("d").date("H").$key);
    }
}
