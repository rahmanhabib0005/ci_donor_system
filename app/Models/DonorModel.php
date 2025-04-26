<?php

namespace App\Models;

use CodeIgniter\Model;

class DonorModel extends Model
{
    protected $table = 'donors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'phone', 'blood_group', 'district', 'thana', 'last_donate'];

    
}
