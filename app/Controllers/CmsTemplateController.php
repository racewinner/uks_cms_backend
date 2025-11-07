<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cms;
use App\Models\CmsTemplate;

use App\Traits\ControllerTrait;

class CmsTemplateController extends BaseController
{
    use ControllerTrait;

    public function index() {
        try {
            $model = new CmsTemplate();
            $query = $model->where('deleted != 1');

            $result = $this->dataTable($query);
            return response()->setJSON($result);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function all() {
        try {
            $model = new CmsTemplate();
            $templates = $model->where('deleted != 1')->findAll();
            return response()->setJSON([
                'templates' => $templates
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function show($id) {
        try {
            $model = new CmsTemplate();
            $row = $model->find($id);

            return response()->setJSON([
                'template' => $row
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function delete($id) {
        try {
            $model = new CmsTemplate();

            $model->update($id, [
                'deleted' => 1
            ]);

            return response()->setJSON([
                'success' => 1,
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function save($id=null) {
        try {
            $authUser = request()->fetchGlobal('user');

            $model = new CmsTemplate();
            $jsonData = json_decode(request()->getBody(), true);

            if(empty($id)) {
                $model->save([
                    ...$jsonData,
                    'editor' => $authUser['id'],
                    'deleted' => 0,
                ]);
                $id = $model->insertID();
            } else {
                $row = $model->find($id);
                if(empty($row)) throw new \Exception("No found template");
                $row = [
                    ...$row,
                    ...$jsonData,
                    'editor' => $authUser['id'],
                ];
                $model->save($row);
            }

            return response()->setJSON([]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}