<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Organization;

use App\Traits\ControllerTrait;

class OrganizationController extends BaseController
{
    use ControllerTrait;

    public function all() {
        try {
            $authUser = request()->fetchGlobal('user');
            $model = new Organization();

            if($authUser['role'] != ManagerRole['superadmin']) {
                $model->where('id', $authUser['organization_id']);
            }

            $organizations = $model->findAll();
            return response()->setJSON([
                'organizations' => $organizations
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function index() {
        try {
            if(!$this->checkPermission('organization')) {
                return response()->setJSON([
                    'message' => 'Unauthorized request'
                ])->setStatusCode(403);
            }            

            $model = new Organization();
            $result = $this->dataTable($model);
            foreach($result['rows'] as &$row) {
                Organization::populate($row);
            }
            return response()->setJSON($result);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function delete($id) {
        if(!$this->checkPermission('organization')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        try {
            $model = new Organization();
            if($model->isInUse($id)) {
                return response()->setJSON([
                    'message' => 'This organization is in use now'
                ])->setStatusCode(403);
            } else {
                $model->delete($id);
                return response()->setJSON([
                    'message' => 'Operation Success'
                ]);
            }
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function show($id) {
        try {
            $model = new Organization();
            $organization = $model->find($id);
            return response()->setJSON([
                'organization' => $organization
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function save($id=null) {
        if(!$this->checkPermission('organization')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }
        
        try {
            $model = new Organization();
            $jsonData = json_decode(request()->getBody(), true);

            if(!empty($id)) {
                $organization = $model->find($id);
                if(empty($organization)) {
                    return response()->setJSON([
                        'message' => 'No found organization'
                    ])->setStatusCode(403);
                }

                // To check whether the same organization alrady exists.
                $existOne = $model->where('name', $jsonData['name'])->where('id !=', $id)->findAll();
                if(!empty($existOne)) {
                    return response()->setJSON([
                        'message' => 'The same organization alrady exists'
                    ])->setStatusCode(403);
                }

                $organization = [
                    ...$organization,
                    ...$jsonData
                ];
                $model->save($organization);
            } else {
                // To check whether the same organization alrady exists.
                $existOne = $model->where('name', $jsonData['name'])->findAll();
                if(!empty($existOne)) {
                    return response()->setJSON([
                        'message' => 'The same organization alrady exists'
                    ])->setStatusCode(403);
                }

                $model->save([
                    ...$jsonData,
                ]);
                $id = $model->insertID();
            }

            return response()->setJSON([]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function bulkAction() {
        if(!$this->checkPermission('organization')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        try {
            $model = new Organization();
            $jsonData = json_decode(request()->getBody(), true);

            if(empty($jsonData['action'])) {
                return response()->setJSON([
                    'message' => 'Bad Request'
                ])->setStatusCode(400);
            } else {
                $ids = $jsonData['ids'];
                $action = $jsonData['action'];

                switch($action) {
                    case 'delete':
                        $model->whereIn('id', $ids)->delete();
                        break;
                }
            }    
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}