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
        $dd = $this->getUsers()->toArray();
        $arrSum = array_sum(array_column($dd, 'max_price'));

        return [
            'invoice_id' => $this->id,
            'invoice_period' => $this->dateFormatter($this->start_date).' to '.$this->dateFormatter($this->end_date),
            'customer_details' => new CustomerResource($this->customer),
            'users' => UserResource::collection($this->getUsers()),
            'invoice_total' => $arrSum.' SAR'
        ];
    }

    private function getUsers(){
        $invoice = Invoice::find($this->id);
        $users = $invoice->customer->users()->with(['sessions' => function($q) use ($invoice) {
            $q->where(function($query) use ($invoice) {
                $query->whereBetween('registered', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)])
                ->orWhereBetween('activated', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)])
                ->orWhereBetween('appointment', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)]);
            });
            $q->whereHas('invoicedSessions', function($i) use($invoice){
                $i->where('invoice_id',$invoice->id);
            });
        }])->get();

        foreach ($users as $key => $user) {
            $registrations = [];
            $activations = [];
            $appointments = [];
            $prices = [];

            $startDate = $this->toDateTime($invoice->start_date);
            $endDate = $this->toDateTime($invoice->end_date);
            $dateToCheck = $this->toDateTime($user->created_at);

            if(count($user->sessions) > 0){
                foreach($user->sessions as $session){

                    if($session->registered != null && $session->activated == null && $session->appointment == null){
                        array_push($registrations, $this->dateFormatter($session->registered));
                        array_push($prices, $session->price);
                    }
                    if($session->registered == null && $session->activated != null && $session->appointment == null){
                        array_push($activations, $this->dateFormatter($session->activated));
                        $dateToCheckActivate = $this->toDateTime($session->activated);
                        if ($dateToCheckActivate >= $startDate && $dateToCheckActivate <= $endDate) {
                            array_push($prices, $session->price);
                        }else{
                            array_push($prices, $session->price-50);
                        }
                    }
                    if($session->registered == null && $session->activated == null && $session->appointment != null){
                        array_push($appointments, $this->dateFormatter($session->appointment));
                        array_push($prices, $session->price);
                    }
                }
            }else {
                unset($users[$key]);
            }

            $user->registrations = $this->sortDates($registrations);
            $user->activations = $this->sortDates($activations);
            $user->appointments = $this->sortDates($appointments);

            $max_price = count($prices) > 1 ? max($prices) : $prices[0];

            if ($dateToCheck >= $startDate && $dateToCheck <= $endDate) {
                $max_price = $max_price-50;
            }

            $user->max_price = $max_price;
        }

        return $users;
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
