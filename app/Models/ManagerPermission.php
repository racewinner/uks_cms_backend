<?php
namespace App\Models;
use CodeIgniter\Model;

class ManagerPermission extends Model
{
	protected $table            = 'epos_manager_permissions';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['value','name'];
}