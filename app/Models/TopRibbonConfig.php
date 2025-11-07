<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class TopRibbonConfig extends Model
{
    use ModelTrait;

	protected $table            = 'epos_siteconfig_topribbon';
    protected $useTimestamps    = true;

    protected $allowedFields    = [
        'style',
        'content_html',
        'editor',
    ];    
}