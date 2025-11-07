<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index() {
        $indexFile = FCPATH . 'index.html';
        if(file_exists($indexFile)) {
            $htmlContent = file_get_contents($indexFile);
            return response()->setContentType('text/html')->setBody($htmlContent);
        } else {
            response()->setStatusCode(404, 'Index.html not found.');
        }
    }
}