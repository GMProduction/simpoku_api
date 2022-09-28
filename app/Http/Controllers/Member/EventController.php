<?php


namespace App\Http\Controllers\Member;


use App\Helper\CustomController;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $q = $this->field('q');
            $query = Event::with(['specialist']);
            if ($q === 'recommend') {
//                $query->
            }
            $data = $query->get()->append(['status']);
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
