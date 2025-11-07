<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Manager;

use App\Traits\ControllerTrait;

class ManagerController extends BaseController
{
    use ControllerTrait;

    public function index() {
        if(!$this->checkPermission('user_management')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        $authUser = request()->fetchGlobal('user');

        try {
            $model = new Manager();
            $model->with([
                [
                    'table' => 'epos_organizations',
                    'local_field' => 'organization_id',
                    'remote_field' => 'id',
                ]
            ]);
            if($authUser['role'] == ManagerRole['cms_admin']) {
                $model->where('organization_id', $authUser['organization_id']);
            }
            $model->select('epos_managers.*, epos_organizations.name as organization');
            $result = $this->dataTable($model);
            foreach($result['rows'] as &$row) {
                unset($row['password']);
                $model->getPermissions($row);
            }
            return response()->setJSON($result);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    // To activate/deactivate user
    public function activate() {
        if(!$this->checkPermission('user_management')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        $model = new Manager();
        $id = request()->getVar('manager_id');
        $deleted = request()->getVar('deleted') ?? 0;

        try {
            $updateData = [
                "deleted" => $deleted
            ];
            $model->update($id, $updateData);

            return response()->setJSON([]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function show($id) {
        if(!$this->checkPermission('user_management')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        $model = new Manager();
        try {
            $row = $model->find($id);
            if(!empty($row)) {
                unset($row['password']);
                $model->getPermissions($row);
                return response()->setJSON([
                    'user' => $row
                ]);
            } else {
                return response()->setJSON([
                    'message'=>'No found CMS'
                ])->setStatusCode(403);
            }
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function save($id=null) {
        if(!$this->checkPermission('user_management')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }
        
        $model = new Manager();
        try {
            $jsonData = json_decode(request()->getBody(), true);

            if(!empty($id)) {
                $user = $model->find($id);
                if(empty($user)) {
                    return response()->setJSON([
                        'message' => 'No found user'
                    ])->setStatusCode(403);
                }

                $user = [
                    ...$user,
                    ...$jsonData
                ];
                if(!empty($jsonData['password'])) {
                    $user['password'] = md5($jsonData['password']);
                }
                $model->save($user);
            } else {
                // To check whether the same username already exists.
                $existOne = $model->where('username', $jsonData['username'])->findAll();
                if(!empty($existOne)) {
                    return response()->setJSON([
                        'message' => 'The same username already exists'
                    ])->setStatusCode(400);
                }

                // To insert a new manager
                $model->save([
                    ...$jsonData,
                    'password' => md5($jsonData['password'])
                ]);
                $id = $model->insertID();
            }

            // To set permission.
            $model->setPermissions($id, $jsonData['permissions']);

            return response()->setJSON([]);
        }catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}