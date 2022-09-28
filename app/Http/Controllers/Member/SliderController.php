<?php


namespace App\Http\Controllers\Member;


use App\Helper\CustomController;
use App\Models\Slider;

class SliderController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $data = Slider::where('is_active', '=', true)->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $data = Slider::find($id);
            if (!$data) {
                return $this->jsonNotFoundResponse('item not found');
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
