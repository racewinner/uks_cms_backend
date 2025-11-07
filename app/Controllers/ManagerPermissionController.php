<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ManagerPermission;

use App\Traits\ControllerTrait;

class ManagerPermissionController extends BaseController
{
    use ControllerTrait;

    public function index() {
        $authUser = request()->fetchGlobal('user');
        if($authUser['role'] != ManagerRole['superadmin']) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        try {
            $model = new ManagerPermission();
            $result = $this->dataTable($model);
            return response()->setJSON($result);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function all() {
        try {
            $authUser = request()->fetchGlobal('user');
            $model = new ManagerPermission();
            if($authUser['role'] != ManagerRole['superadmin']) {
                $model->where('value !=', 'user_management');
                $model->where('value !=', 'organization');
            }
            $rows = $model->findAll();
            return response()->setJSON([
                'rows' => $rows
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}