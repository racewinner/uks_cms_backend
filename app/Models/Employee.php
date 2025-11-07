<?php
namespace App\Models;
use CodeIgniter\Model;

class Employee extends Model
{
	protected $table            = 'epos_employees';
    protected $primaryKey       = 'person_id';

	protected $useTimestamps    = false;
    protected $allowedFields    = [];

}