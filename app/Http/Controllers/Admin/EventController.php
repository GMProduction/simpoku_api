<?php


namespace App\Http\Controllers\Admin;


use App\Helper\CustomController;
use App\Models\Event;

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
        Event::created($request);
        return $this->jsonSuccessResponse('success');
    }
}
