<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cms;
use App\Models\ActivityLog;

use App\Traits\ControllerTrait;

class CmsController extends BaseController
{
    use ControllerTrait;

    public function index() {
        try {
            $authUser = request()->fetchGlobal('user');
            $type = request()->getGet('type');
            $model = new Cms();

            // To check permission
            if(!$this->checkPermission($type)) {
                return response()->setJSON([
                    'message' => 'Unauthorized request'
                ])->setStatusCode(403);
            }

            // where condition
            $model->where('epos_cms.type', $type)->where('epos_cms.deleted !=', 1);
            if($authUser['role'] != ManagerRole['superadmin']) {
                $model->where('epos_cms.organization_id', $authUser['organization_id']);
            }

            // with clause
            $with = [
                [
                    'table' => 'epos_organizations',
                    'local_field' => 'organization_id',
                    'remote_field' => 'id',
                ], [
                    'table' => 'epos_managers',
                    'local_field' => 'editor',
                    'remote_field' => 'manager_id',
                ], [
                    'table' => 'epos_cms_templates',
                    'local_field' => 'template_id',
                    'remote_field' => 'id',
                ]
            ];
            $model->with($with);

            // To select fields
            $model->select([
                'epos_cms.*',
                'epos_organizations.name as organization',
                'epos_managers.username as editor_username',
                'epos_cms_templates.internal_name as template_name',
                '(SELECT COUNT(*) FROM epos_cms_items WHERE epos_cms_items.cms_id = epos_cms.id AND epos_cms_items.deleted = 0) AS item_count',
            ]);
            $model->groupBy('epos_cms.id');

            return response()->setJSON($this->dataTable($model));
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function expires() {
        try {
            $authUser = request()->fetchGlobal('user');
            $model = new Cms();

            // where condition
            $model->where('epos_cms.deleted !=', 1);
            if($authUser['role'] != ManagerRole['superadmin']) {
                $model->where('epos_cms.organization_id', $authUser['organization_id']);
            }

            // with clause
            $with = [
                [
                    'table' => 'epos_organizations',
                    'local_field' => 'organization_id',
                    'remote_field' => 'id',
                ], [
                    'table' => 'epos_managers',
                    'local_field' => 'editor',
                    'remote_field' => 'manager_id',
                ]
            ];
            $model->with($with);

            // To select fields
            $select = 'epos_cms.*, epos_organizations.name as organization, epos_managers.username as editor';
            $model->select($select);

            return response()->setJSON($this->dataTable($model));
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    public function show($id) {
        try {
            $model = new Cms();

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
            $model = new Cms();

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
                'activity_model' => Cms::class,
                'activity_model_id' => $row['id'],
                'activity_action' => 'delete',
                'organization_id' => $row['organization_id'] ?? '',
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
        $model = new Cms();
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

    public function checkDateRange() {
        $id = request()->getVar('id');
        $type = request()->getVar('type');
        $start_date = new \DateTime(request()->getVar('start_date'));
        $end_date = new \DateTime(request()->getVar('end_date'));
        
        $model = new Cms();
        $query = $model->where('type', $type);
        if( !empty($id) ) {
            $query->where('id !=', $id);
        }

        $rows = $query->findAll();

        foreach($rows as $row) {
            if(checkDateRangeCross($start_date, $end_date, new \DateTime($row['start_date']), new \DateTime($row['end_date']))) {
                return response()->setJSON([
                    'success' => 0,
                    'message' => 'Invalid Date Range'
                ])->setStatusCode(400);
            }
        }

        return response()->setJSON([
            'success' => 1
        ]);
    }

    public function moveUpDown($id, $dir) {
        if(empty($id) || empty($dir)) {
            return response()->setJSON([
                'message' => 'Bad Request',
            ]);
        }

        try {
            // To get CMS record.
            $model = new Cms();
            $one = $model->find($id);
            if(empty($one)) {
                return response()->setJSON([
                    'message' => 'No found CMS record'
                ]);
            }

            if($dir == 'up') {
                $swapOne = $model->where('type', $one['type'])
                    ->where('page_pos <', $one['page_pos'])
                    ->orderBy('page_pos', 'DESC')
                    ->first();
            } else {
                $swapOne = $model->where('type', $one['type'])
                    ->where('page_pos >', $one['page_pos'])
                    ->orderBy('page_pos', 'ASC')
                    ->first();
            }

            if( !empty($swapOne) ) {
                $seq = $one['page_pos'];
                $one['page_pos'] = $swapOne['page_pos'];
                $model->save($one);

                $swapOne['page_pos'] = $seq;
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
        $model = new Cms();
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
                case 'set_startdate':
                    $model->set('start_date', $jsonData['value'])->whereIn('id', $ids)->update();
                    break;
                case 'set_enddate':
                    $model->set('end_date', $jsonData['value'])->whereIn('id', $ids)->update();
                    break;
                case 'set_dwelltime':
                    $model->set('dwell_time', $jsonData['value'])->whereIn('id', $ids)->update();
                    break;
                case 'set_page_position':
                    $model->set('page_pos', $jsonData['value'])->whereIn('id', $ids)->update();
            }
        }
    }
 
    public function copy($id) {
        try {
            $model = new Cms();
            $row = $model->find($id);
            if(empty($row)) {
                return response()->setJSON([
                    'message' => 'No found Content'
                ])->setStatusCode(400);
            }

            unset($row['id']);
            $model->save($row);

            return response()->setJSON([
                'message' => 'Copied successfully'
            ]);
        } catch(\Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function save($id=null) {
        $authUser = request()->fetchGlobal('user');

        $model = new Cms();
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
                    'activity_model' => Cms::class,
                    'activity_model_id' => $id,
                    'activity_action' => 'create',
                    'organization_id' => $jsonData['organization_id'] ?? '',
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
                    'activity_model' => Cms::class,
                    'activity_model_id' => $id,
                    'activity_action' => 'update',
                    'organization_id' => $cms['organization_id'] ?? '',
                ]);
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

    public function getMaxPagePostion() {
        $max_page_pos = Cms::getMaxPagePosition();
        return response()->setJSON([
            'max_page_pos' => $max_page_pos
        ]);
    }
}