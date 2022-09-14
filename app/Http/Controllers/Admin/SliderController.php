<?php


namespace App\Http\Controllers\Admin;


use App\Helper\CustomController;
use App\Helper\ValidationRules;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            if ($this->request->method() === 'POST') {
                return $this->post_new_slider();
            }
            $data = Slider::all();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('failed ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $data = Slider::where('id', '=', $id)->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('item not found');
            }
            if ($this->request->method() === 'POST') {
                return $this->patch_slider($data);
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('failed ' . $e->getMessage());
        }
    }

    public function activate($id)
    {
        try {
            $data = Slider::where('id', '=', $id)->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('item not found');
            }
            $status = !$data->is_active;
            $data->update([
                'is_active' => $status
            ]);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('failed ' . $e->getMessage());
        }
    }

    private function post_new_slider()
    {
        $validator = Validator::make($this->request->all(), ValidationRules::SLIDER_CREATE_RULE);
        if ($validator->fails()) {
            return $this->jsonBadRequestResponse('invalid request', $validator->errors());
        }
        $request = [
            'url' => $this->postField('url')
        ];
        $disk = '/assets/image/sliders/';
        $icon_name = $this->upload('image', $disk);
        $request['image'] = $disk . $icon_name;
        Slider::create($request);
        return $this->jsonSuccessResponse('success');
    }


    private function patch_slider($data)
    {
        $validator = Validator::make($this->request->all(), ValidationRules::SLIDER_PATCH_RULE);
        if ($validator->fails()) {
            return $this->jsonBadRequestResponse('invalid request', $validator->errors());
        }
        $request = [
            'url' => $this->postField('url')
        ];
        if ($this->request->hasFile('image')) {
            $disk = '/assets/image/sliders/';
            $icon_name = $this->upload('image', $disk);
            $request['image'] = $disk . $icon_name;
            if (storage_path($data->image)) {
                unlink(storage_path($data->image));
            }
        }
        $data->update($request);
        return $this->jsonSuccessResponse('success');
    }
}
