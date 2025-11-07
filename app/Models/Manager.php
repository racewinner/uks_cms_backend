<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class Manager extends Model
{
    use ModelTrait;

	protected $table            = 'epos_managers';
    protected $primaryKey       = 'manager_id';

	protected $useTimestamps    = false;
    protected $allowedFields    = ['username', 'password', 'email', 'organization_id', 'role', 'deleted'];

    public function getPermissions(&$manager) {
        $model = new ManagerHasPermission();
        $permissions = $model->where('manager_id', $manager['manager_id'])->findAll();
        $manager['permissions'] = array_map(function($p) {
            return $p['permission'];
        }, $permissions);
    }

    public function setPermissions($manager_id, $permissions) {
        $model = new ManagerHasPermission();

        // To remove all permissions for manager.
        $model->where('manager_id', $manager_id)->delete();

        // To check if the permission already was added for the manager.
        foreach($permissions as $p) {
            $model->save([
                'manager_id' => $manager_id,
                'permission' => $p
            ]);
        }
    }
}

