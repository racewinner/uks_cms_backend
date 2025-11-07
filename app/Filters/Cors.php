<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class Cors implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        // Allow from any origin
        response()->setHeader("Access-Control-Allow-Origin", "*");
        response()->setHeader("Access-Control-Allow-Headers","X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Requested-Method, Authorization");
        response()->setHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS, PATCH, PUT, DELETE");
        
        // Handle preflight requests
        if ($request->getMethod() == 'options') {
            return response()->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    }
}