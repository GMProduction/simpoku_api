<?php


namespace App\Http\Controllers\Admin;


use App\Helper\CustomController;
use App\Models\Specialist;

class SpecialistController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            if ($this->request->method() === 'POST') {
                $name = $this->postField('name');
                $request = [
                    'name' => ucwords($name),
                    'slug' => $this->make_slug($name)
                ];
                Specialist::create($request);
                return $this->jsonSuccessResponse('success');
            }
            $q = $this->field('q');
            $data = Specialist::where('name', 'LIKE', '%' . $q . '%')->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('internal server error : ' . $e->getMessage());
        }
    }
}
