<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\App;

class AuthFilter implements FilterInterface {
    private $appConfig;

    public function __construct() {
        $this->appConfig = new App();
    }

    public function before(RequestInterface $request, $arguments = null) {
        $authHeader = $request->getHeader('Authorization');

        if ( !empty($authHeader) ) {
            $token = str_replace('Bearer ', '', $authHeader->getValue());
            try {
                $decoded = JWT::decode($token, new Key($this->appConfig->secretKey, 'HS256'));
                $request->setGlobal('user', (array)[
                    ...(array)$decoded->data,
                    "permissions" => json_decode($decoded->data->permissions, true)
                ]);
            } catch (\Exception $e) {
                return response()->setJSON('Unauthorized: ' . $e->getMessage())->setStatusCode(401);
            }
        } else {
            return response()->setJSON('Authorization header not found.')->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    { 

    }
}
