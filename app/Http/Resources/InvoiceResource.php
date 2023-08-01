<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Session;
use App\Models\Invoice;
use App\Models\InvoicedSession;
use DateTime;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $session_prices = $this->getUsers()->toArray();
        $invoice_total_sum = array_sum(array_column($session_prices, 'max_price'));

        return [
            'invoice_id' => $this->id,
            'invoice_period' => $this->dateFormatter($this->start_date).' to '.$this->dateFormatter($this->end_date),
            'customer_details' => new CustomerResource($this->customer),
            'users' => UserResource::collection($this->getUsers()),
            'invoice_total' => $invoice_total_sum.' SAR'
        ];
    }

    private function getUsers(){
        $invoice = Invoice::find($this->id);
        $users = $invoice->customer->users()->with(['sessions' => function($q) use ($invoice) {
            $q->where(function($query) use ($invoice) {
                $query->whereBetween('activated', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)])
                ->orWhereBetween('appointment', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)]);
            });
            $q->whereHas('invoicedSessions', function($i) use($invoice){
                $i->where('invoice_id',$invoice->id);
            });
        }])->get();

        foreach ($users as $key => $user) {
            $activations = [];
            $appointments = [];
            $prices = [];
            $before = false;

            $startDate = $this->toDateTime($invoice->start_date);
            $endDate = $this->toDateTime($invoice->end_date);
            $dateToCheck = $this->toDateTime($user->created_at);

            if ($dateToCheck >= $startDate && $dateToCheck <= $endDate) {
                $before = true;
            }

            if(count($user->sessions) > 0){
                foreach($user->sessions as $session){
                    if($session->registered == null && $session->activated != null && $session->appointment == null){
                        array_push($activations, $this->dateFormatter($session->activated));
                        array_push($prices, $session->price);
                    }
                    if($session->registered == null && $session->activated == null && $session->appointment != null){
                        array_push($appointments, $this->dateFormatter($session->appointment));
                        array_push($prices, $session->price);
                    }
                }
            }else {
                unset($users[$key]);
            }

            $user->activations = $this->sortDates($activations);
            $user->appointments = $this->sortDates($appointments);

            if(is_array($prices) && count($prices) > 0){
                $max_price = max($prices);
            }

            if($before === false && count($activations) > 0){
                $max_price = $max_price-50;
            }

            $user->max_price = $max_price;
        }

        return $users;
    }

    private function checkOldSession($user_id, $type){
        $total_sessions = Session::where('user_id', $user_id)->where($type, '!=', null)->count();
        return $total_sessions;
    }

    private function sortDates($arr){
        usort($arr, function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });
        return $arr;
    }

    private function toDateTime($date){
        return new DateTime($date);
    }

    private function dateFormatter($date){
        return date('Y-m-d', strtotime($date));
    }
}
