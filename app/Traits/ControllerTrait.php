<?php
namespace App\Traits;  

trait ControllerTrait {
    public function checkPermission($permission) {
        $authUser = request()->fetchGlobal('user');
        if(empty($authUser)) return false;

        return $authUser['role'] == ManagerRole['superadmin'] || 
            $authUser['role'] == ManagerRole['cms_admin'] || 
            in_array($permission, $authUser['permissions']);
    }

    public function dataTable($model) {
        // search
        $search = request()->getGet('search');
        if(!empty($search)) {
            $search = json_decode($search, true);
            foreach($search as $k => $v) {
                if(empty($v) || empty($v['op']) || empty($v['value'])) continue;
                if($v['op'] == 'like') {
                    $model->like($k, $v['value']);
                } else {
                    $model->where($k . $v['op'], $v['value']);
                }
            }
            // $model->groupStart()->like('data', $search)->orLike('memo', $search)->groupEnd();
        }

        // sort
        $sort = request()->getGet('sort');
        if(!empty($sort)) {
            $sort = json_decode($sort, true);
            if(!empty($sort['name'])) {
                $model->orderBy($sort['name'], !empty($sort['dir']) ? $sort['dir'] : 'ASC');
            }
        }

        // pagination
        $perPage = intVal(request()->getGet('perPage') ?? 10);
        $page = intVal(request()->getGet('pageNum') ?? 1);
        $offset = ($page - 1) * $perPage;
        $rows = $model->paginate($perPage, 'default', $page);
        $pager = $model->pager;

        // total count
        $total = $pager->getTotal();

        return [
            'total' => $pager->getTotal(),
            'rows' => $rows
        ];
    }
}