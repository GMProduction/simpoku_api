<?php


namespace App\Http\Controllers\Admin;


use App\Helper\CustomController;
use App\Helper\ValidationRules;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;

class EventController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            if ($this->request->method() === 'POST') {
                return $this->post_new_event();
            }
            $data = Event::with(['specialist'])
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $data = Event::with(['specialist'])
                ->where('id', '=', $id)
                ->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('event not found!');
            }
            if ($this->request->method() === 'POST') {
                return $this->patch_event($data);
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function event_patch_request()
    {
        return [
            'specialist_id' => $this->postField('specialist'),
            'title' => ucwords(strtolower($this->postField('title'))),
            'description' => $this->postField('description'),
            'start_at' => $this->postField('start_at'),
            'finish_at' => $this->postField('finish_at'),
            'location' => $this->postField('location'),
            'latitude' => $this->postField('latitude'),
            'longitude' => $this->postField('longitude'),
        ];
    }

    private function event_image_upload()
    {
        if ($this->request->hasFile('image')) {
            $disk = '/assets/image/events/';
            $icon_name = $this->upload('image', $disk);
            return $disk . $icon_name;
        }
        return null;
    }

    private function event_announcement_upload()
    {
        if ($this->request->hasFile('announcement')) {
            $disk = '/assets/announcement/events/';
            $icon_name = $this->upload('announcement', $disk);
            return $disk . $icon_name;
        }
        return null;
    }

    private function post_new_event()
    {
        $validator = Validator::make($this->request->all(), ValidationRules::EVENT_CREATE_RULE);
        if ($validator->fails()) {
            return $this->jsonBadRequestResponse('invalid request', $validator->errors());
        }
        $request = $this->event_patch_request();
        $request['image'] = $this->event_image_upload();
        $request['announcement'] = $this->event_announcement_upload();
        Event::create($request);
        return $this->jsonSuccessResponse('success insert', $request);
    }

    private function patch_event($data)
    {
        $validator = Validator::make($this->request->all(), ValidationRules::EVENT_PATCH_RULE);
        if ($validator->fails()) {
            return $this->jsonBadRequestResponse('invalid request', $validator->errors());
        }
        $request = $this->event_patch_request();
        $has_image = $this->event_image_upload();
        $has_announcement = $this->event_announcement_upload();
        if ($has_image !== null) {
            $request['image'] = $has_image;
            $this->remove_file($data->image);
        }
        if ($has_announcement !== null) {
            $request['announcement'] = $has_announcement;
            $this->remove_file($data->announcement);
        }
        $data->update($request);
        return $this->jsonSuccessResponse('success');
    }
}
