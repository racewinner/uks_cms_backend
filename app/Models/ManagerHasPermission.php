<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class ManagerHasPermission extends Model
{
    use ModelTrait;

	protected $table            = 'epos_manager_has_permissions';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['manager_id','permission'];
}