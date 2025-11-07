<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivityLog;

use App\Traits\ControllerTrait;

class ActivityLogController extends BaseController
{
    use ControllerTrait;

    public function index() {
        try {
            $authUser = request()->fetchGlobal('user');
            $model = new ActivityLog();

            // condition
            switch($authUser['role']) {
                case ManagerRole['cms_admin']:
                    $model->where('epos_activity_logs.organization_id', $authUser['organization_id']);
                    break;
                case ManagerRole['cms_user']:
                    $model->where('editor', $authUser['id']);
                    break;
            }

            // with clause
            $model->with([
                [
                    'table' => 'epos_managers',
                    'local_field' => 'editor',
                    'remote_field' => 'manager_id'
                ], [
                    'table' => 'epos_organizations',
                    'local_field' => 'organization_id',
                    'remote_field' => 'id'
                ], [
                    'table' => 'epos_cms',
                    'local_field' => 'activity_model_id',
                    'remote_field' => 'id'
                ]
            ]);

            // select
            $select = 'epos_activity_logs.*, epos_managers.username as editor_name, epos_organizations.name as organization, 
                    epos_cms.internal_name, epos_cms.branches, epos_cms.start_date, epos_cms.end_date, epos_cms.active';
            $model->select($select);

            $result = $this->dataTable($model);
            return response()->setJSON($result);
        } catch (\Exception $e) {
            return response()->setJSON([
                "message" => $e->getMessage()
            ])->setStatusCode(400);
        }
    }
}