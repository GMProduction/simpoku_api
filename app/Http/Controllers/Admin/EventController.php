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

    private function post_new_event()
    {
        $validator = Validator::make($this->request->all(), ValidationRules::EVENT_CREATE_RULE);
        if ($validator->fails()) {
            return $this->jsonBadRequestResponse('invalid request', $validator->errors());
        }
        $request = [
            'specialist_id' => $this->postField('specialist'),
            'title' => ucwords(strtolower($this->postField('title'))),
            'description' => $this->postField('description'),
            'start_at' => $this->postField('start_at'),
            'finish_at' => $this->postField('finish_at'),
            'location' => $this->postField('location'),
            'latitude' => $this->postField('latitude'),
            'longitude' => $this->postField('longitude'),
        ];
        if ($this->request->hasFile('image')) {
            $disk = '/assets/image/events/';
            $icon_name = $this->upload('image', $disk);
            $request['image'] = $disk . $icon_name;
        }
        if ($this->request->hasFile('announcement')) {
            $disk = '/assets/announcement/events/';
            $icon_name = $this->upload('image', $disk);
            $request['image'] = $disk . $icon_name;
        }
        Event::create($request);
        return $this->jsonSuccessResponse('success insert', $request);
    }
}
