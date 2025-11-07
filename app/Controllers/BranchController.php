<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Branch;

use App\Traits\ControllerTrait;

class BranchController extends BaseController
{
    use ControllerTrait;

    public function all() {
        try {
            $model = new Branch();
            $branches = $model->findAll();
            return response()->setJSON([
                'branches' => $branches
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function index() {
        try {
            $model = new Branch();
            $result = $this->dataTable($model);
            return response()->setJSON($result);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function delete($id) {
        if(!$this->checkPermission('branch')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }

        try {
            $model = new Branch();
            if($model->isInUse($id)) {
                return response()->setJSON([
                    'message' => 'This branch is in use now'
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
            $model = new Branch();
            $branch = $model->find($id);
            return response()->setJSON([
                'branch' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function save($id=null) {
        if(!$this->checkPermission('branch')) {
            return response()->setJSON([
                'message' => 'Unauthorized request'
            ])->setStatusCode(403);
        }
        
        try {
            $model = new Branch();
            $jsonData = json_decode(request()->getBody(), true);

            if(!empty($id)) {
                $branch = $model->find($id);
                if(empty($branch)) {
                    return response()->setJSON([
                        'message' => 'No found branch'
                    ])->setStatusCode(403);
                }

                $branch = [
                    ...$branch,
                    ...$jsonData
                ];
                $model->save($branch);
            } else {
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
}