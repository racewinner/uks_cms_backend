<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class ActivityLog extends Model
{
    use ModelTrait;

    protected $table            = 'epos_activity_logs';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['editor', 'activity_model', 'activity_action', 'activity_model_id', 'organization_id', 'created_at', 'updated_at'];
}