<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use \Firebase\JWT\JWT;
use App\Models\Manager;
use Config\App;

class AuthController extends BaseController
{
    private $appConfig;

    public function __construct() {
        parent::__construct();
        $this->appConfig = new App();
    }

    public function login() {
        $Manager = new Manager();

        try {
            $jsonData = request()->getJSON(true);
            $username = $jsonData['username'];
            $password = md5($jsonData["password"]);
    
            $user = $Manager->where("username", $username)
                ->where("password",$password)
                ->where("deleted", 0)
                ->select('manager_id,username,email,organization_id,role')
                ->first();
    
            if(!empty($user)) {
                $Manager->getPermissions($user);
                $token = array(
                    "iat" => time(),
                    // "exp" => time() + (2 * 60 * 60), // Token valid for 2 hour
                    "data" => array(
                        "id" => $user["manager_id"],
                        "username" => $username,
                        "email" => $user['email'],
                        "manager_id" => $user['manager_id'],
                        "role" => $user['role'],
                        "permissions" => json_encode($user['permissions']),
                        "organization_id" => $user['organization_id'],
                    )
                );
    
                $jwt = JWT::encode($token, $this->appConfig->secretKey, 'HS256');
    
                return response()->setJSON([
                    "authUser" => $user,
                    "authToken" => $jwt
                ]);
            } else {
                return response()->setJSON([
                    "message" => "Login failed"
                ])->setStatusCode(401);
            }
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function logout() {

    }
}