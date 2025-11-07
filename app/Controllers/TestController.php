<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use \Firebase\JWT\JWT;

class TestController extends BaseController
{
    public function test1() {
        echo "Hello";
    }
}