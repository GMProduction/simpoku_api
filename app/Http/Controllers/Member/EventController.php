<?php


namespace App\Http\Controllers\Member;


use App\Helper\CustomController;
use App\Models\Event;
use App\Models\EventRegistrant;
use App\Models\EventRegistrantMember;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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
            $member = Member::with('user.specialists')->where('user_id', '=', Auth::id())
                ->first();
            $my_specialists = $member->user->specialists->pluck('id');
            if ($q === 'recommend') {
                $query->whereIn('specialist_id', $my_specialists);
            }
            $data = $query->get()->append(['status']);
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $data = Event::with(['specialist'])->where('id', '=', $id)
                ->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('event not found');
            }
            if ($this->request->method() === 'POST') {
                return $this->register_event($data);
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function register_event($event)
    {
        DB::beginTransaction();
        try {
            $registrants = json_decode($this->postField('registrants'));
            if ($registrants === null || !is_array($registrants)) {
                return $this->jsonBadRequestResponse('invalid registrants format');
            }
            $user_id = Auth::id();
            $sub_total = $event->price * count($registrants);
            $reference_id = 'SM-' . date('YmdHis');
            $discount = 0;
            $total = $sub_total - $discount;
            $event_registrant_data = [
                'user_id' => $user_id,
                'reference_id' => $reference_id,
                'sub_total' => $sub_total,
                'discount' => $discount,
                'total' => $total,
                'status' => 0
            ];
            $event_registrant = EventRegistrant::create($event_registrant_data);
            foreach ($registrants as $registrant) {
                $event_registrant_member_data = [
                    'event_registrant_id' => $event_registrant->id,
                    'code' => Uuid::uuid4()->toString(),
                    'name' => $registrant->name,
                    'phone' => $registrant->phone
                ];
                EventRegistrantMember::create($event_registrant_member_data);
            }
            DB::commit();
            return $this->jsonSuccessResponse('success', $event_registrant);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
