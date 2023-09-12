<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Artisan;

class CommandController extends Controller
{
    public function migrate()
    {
        Artisan::call('migrate');
    }
    public function seed()
    {
        Artisan::call('db:seed');
    }
    public function passportInstall()
    {
        Artisan::call('passport:install');
    }
    public function keyGenerate()
    {
        Artisan::call('key:generate');
    }
}
