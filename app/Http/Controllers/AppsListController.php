<?php

namespace App\Http\Controllers;

class AppsListController extends Controller
{
    private $AppsNames = ['conektta_mob', 'conektta_crm'];
    private $Apps;

    public function __construct()
    {
        $this->Apps = [
            'conektta_mob' => [
                'status' => 1,
                'level_of_access' => 1,
                'key' => '123456789'

            ],
            'conektta_crm' => [
                'status' => 10,
                'level_of_access' => 1,
                'key' => '123456789'
            ]
        ];
    }

    public function get()
    {
        return $this->Apps;
    }

    public function getAppNames()
    {
        return $this->AppsNames;
    }

    public function getLevelOfAccess($AppName)
    {
        if (in_array($AppName, $this->AppsNames))
        {
            return $this->Apps[$AppName]['level_of_access'];
        }
        else
        {
            return 0;
        }
    }
}