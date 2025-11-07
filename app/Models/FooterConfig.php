<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Traits\ModelTrait;

class FooterConfig extends Model
{
    use ModelTrait;

	protected $table            = 'epos_siteconfig_footer';
    protected $useTimestamps    = true;

    protected $allowedFields    = [
        'style',
        'logo_web',
        'logo_mobile',
        'content_html',
        'column1',
        'column2',
        'bottom_html',
        'editor',
    ];    
}