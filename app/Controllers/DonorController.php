<?php

namespace App\Controllers;

use App\Models\DonorModel;
use CodeIgniter\RESTful\ResourceController;

class DonorController extends ResourceController
{
    protected $modelName = 'App\Models\DonorModel';
    protected $format    = 'json';

    public function create()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'name'        => 'required',
            'phone'       => 'required',
            'blood_group' => 'required',
            'district'    => 'required',
            'thana'       => 'required',
            'last_donate' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost([
            'name',
            'phone',
            'blood_group',
            'district',
            'thana',
            'last_donate'
        ]);

        $this->model->save($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Donor saved'
        ]);
    }

    public function index()
    {
        return view('donors/list');
    }

    public function datatable()
    {
        $request = service('request');
        $model = new DonorModel();

        $search = $request->getPost('search')['value'] ?? '';
        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $order  = $request->getPost('order');
        $orderColumn = $request->getPost('columns')[$order[0]['column']]['data'] ?? 'id';
        $orderDir = $order[0]['dir'] ?? 'asc';

        $filters = [
            'blood_group' => $request->getPost('blood_group'),
            'district'    => $request->getPost('district'),
            'thana'       => $request->getPost('thana'),
        ];

        $builder = $model->builder();

        // Apply filters
        foreach ($filters as $key => $val) {
            if (!empty($val)) {
                $builder->where($key, $val);
            }
        }

        // Filter by last_donate
        $lastDonate = $request->getPost('last_donate');
        if (!empty($lastDonate)) {
            $now = date('Y-m-d');

            if ($lastDonate === 'more') {
                // More than 12 months ago
                $cutoff = date('Y-m-d', strtotime('-12 months'));
                $builder->where('last_donate <', $cutoff);
            } else {
                // Within the last X months (from today back to X months ago)
                $months = (int) $lastDonate;
                $cutoff = date('Y-m-d', strtotime("-$months months"));

                $builder->where('last_donate >=', $cutoff);
            }
        }

        // Search
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        // Order and limit
        $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start);

        $data = $builder->get()->getResult();


        $json = [
            'draw'            => intval($request->getPost('draw')),
            'recordsTotal'    => $model->countAll(),
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ];

        return $this->response->setJSON($json);
    }

    public function getDistricts()
    {
        $model = new DonorModel();
        $districts = $model->distinct()->select('district')->findAll();
        return $this->response->setJSON($districts);
    }

    public function getFilterThanas()
    {
        $district = $this->request->getPost('district');
        $model = new DonorModel();
        $thanas = $model->distinct()->select('thana')->where('district', $district)->findAll();
        return $this->response->setJSON($thanas);
    }

    public function getThanas()
    {
        $district = $this->request->getPost('district');
        $thanas = [];

        // Define thanas for each district
        $thanaList = [
            'Dhaka' => ['Dhanmondi', 'Uttara', 'Mirpur'],
            'Chattogram' => ['Chandgaon', 'Pahartali', 'Halishahar'],
            // Add other districts and their thanas here
        ];

        if (array_key_exists($district, $thanaList)) {
            $thanas = $thanaList[$district];
        }

        return $this->response->setJSON($thanas);
    }
}
