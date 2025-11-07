<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cms;
use App\Models\CmsItem;
use App\Models\ActivityLog;

use App\Traits\ControllerTrait;

class CmsItemController extends BaseController
{
    use ControllerTrait;

    public function index() {
        try {
            $cms_id = request()->getVar('cms_id');

            $cmsModel = new Cms();
            $cms = $cmsModel->find($cms_id);
            if(empty($cms)) throw new \Exception("No found CMS.");

            // To check permission
            if(!$this->checkPermission($cms['type'])) {
                return response()->setJSON([
                    'message' => 'Unauthorized request'
                ])->setStatusCode(403);
            }

            $model = new CmsItem();

            // where condition
            $model->where('cms_id', $cms_id)->where('epos_cms_items.deleted !=', 1);

            // with clause
            $with = [
                [
                    'table' => 'epos_managers',
                    'local_field' => 'editor',
                    'remote_field' => 'manager_id',
                ]
            ];

            $detail_table = Cms::getDetailTable($cms['type']);
            if(!empty($detail_table)) {
                $with[] = [
                    'table' => $detail_table,
                    'local_field' => 'id',
                    'remote_field' => 'cms_item_id',
                ];
            }
            $model->with($with);

            // To select fields
            $select = 'epos_cms_items.*, epos_managers.username as editor_username';
            if(!empty($detail_table)) {
                $select .= ", $detail_table.*";
            }
            $model->select($select);

            return response()->setJSON([
                'cms' => $cms,
                ...$this->dataTable($model)
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function show($id) {
        try {
            $type = request()->getVar('type');
            $model = new CmsItem();

            // To fetch row
            $detail_table = Cms::getDetailTable($type);
            if(!empty($detail_table)) {
                $model->with([
                    [
                        'table' => $detail_table,
                        'local_field' => 'id',
                        'remote_field' => 'cms_item_id'
                    ]
                ]);
            }
            
            $row = $model->find($id);

            if(!empty($row)) {
                return response()->setJSON([
                    'row' => $row,
                ]);
            } else {
                return response()->setJSON([
                    'message'=>'No found CMS'
                ])->setStatusCode(403);
            }
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    // To delete
    public function delete($id) {
        try {
            $authUser = request()->fetchGlobal('user');
            $model = new CmsItem();

            // To find row
            $row = $model->find($id);
            if(empty($row)) {
                return response()->setJSON([
                    'message' => 'No found CMS'
                ])->setStatusCode(400);
            }

            // To delete row
            $model->update($id, [
                'deleted' => 1
            ]);

            // To add activity log
            $logModel = new ActivityLog();
            $logModel->save([
                'editor' => $authUser['id'],
                'activity_model' => CmsItem::class,
                'activity_model_id' => $row['id'],
                'activity_action' => 'delete',
            ]);

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // To activate/deactivate cms
    public function activate($id) {
        $model = new CmsItem();
        $active = request()->getVar('active') ?? 0;

        try {
            $updateData = [
                "active" => $active
            ];
            $model->update($id, $updateData);

            return response()->setJSON([]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    public function moveUpDown($id, $dir) {
        if(empty($id) || empty($dir)) {
            return response()->setJSON([
                'message' => 'Bad Request',
            ]);
        }

        try {
            // To get CMS record.
            $model = new CmsItem();
            $one = $model->find($id);
            if(empty($one)) {
                return response()->setJSON([
                    'message' => 'No found CMS record'
                ]);
            }

            if($dir == 'up') {
                $swapOne = $model->where('cms_id', $one['cms_id'])
                    ->where('sequence <', $one['sequence'])
                    ->orderBy('sequence', 'DESC')
                    ->first();
            } else {
                $swapOne = $model->where('cms_id', $one['cms_id'])
                    ->where('sequence >', $one['sequence'])
                    ->orderBy('sequence', 'ASC')
                    ->first();
            }

            if( !empty($swapOne) ) {
                $seq = $one['sequence'];
                $one['sequence'] = $swapOne['sequence'];
                $model->save($one);

                $swapOne['sequence'] = $seq;
                $model->save($swapOne);
            }

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // Bulk Action
    public function bulkAction() {
        $model = new CmsItem();
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
                    $model->set('deleted', 1)->whereIn('id', $ids)->update();
                    break;
                case 'activate':
                    $model->set('active', 1)->whereIn('id', $ids)->update();
                    break;
                case 'deactivate':
                    $model->set('active', 0)->whereIn('id', $ids)->update();
                    break;
                case 'set_dwelltime':
                    $model->set('dwell_time', $jsonData['value'])->whereIn('id', $ids)->update();
                    break;
            }
        }
    }

    public function save($id=null) {
        $authUser = request()->fetchGlobal('user');

        $model = new CmsItem();
        $logModel = new ActivityLog();
        
        try {
            $jsonData = json_decode(request()->getBody(), true);

            // To save main Cms
            if(empty($id)) {
                $model->save([
                    ...$jsonData,
                    'editor' => $authUser['id'],
                    'deleted' => 0,
                ]);
                $id = $model->insertID();

                // To add activity_log
                $logModel->save([
                    'editor' => $authUser['id'],
                    'activity_model' => CmsItem::class,
                    'activity_model_id' => $id,
                    'activity_action' => 'create',
                ]);
            } else {
                $cms = $model->find($id);
                if(empty($cms)) {
                    return response()->setJSON([
                        'message' => "No found Cms Content: id=$id"
                    ])->setStatusCode(400);
                }
                $cms = [
                    ...$cms,
                    ...$jsonData,
                    "editor" => $authUser['id']
                ];
                $model->save($cms);

                // To add activity_log
                $logModel->save([
                    'editor' => $authUser['id'],
                    'activity_model' => CmsItem::class,
                    'activity_model_id' => $id,
                    'activity_action' => 'update',
                ]);
            }

            // To save detail CMS
            $detail_model = Cms::getDetailModel($jsonData['type']);
            if(!empty($detail_model)) {
                $detail_one = $detail_model->where('cms_item_id', $id)->first();
                if(empty($detail_one)) {
                    unset($jsonData['detail_id']);
                    $detail_model->save([
                        ...$jsonData,
                        'cms_item_id' => $id,
                    ]);
                } else {
                    $detail_model->save([
                        ...$detail_one,
                        ...$jsonData
                    ]);
                }
            }

            return response()->setJSON([
                'message' => 'Operation Success'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function getLastSequence($cms_id) {
        try {
            $last_sequence = CmsItem::getLastSequence($cms_id);
            return response()->setJSON([
                'last_sequence' => $last_sequence
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}