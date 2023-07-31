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
            'invoice_period' => date('Y-m-d', strtotime($this->start_date)).' to '.date('Y-m-d', strtotime($this->end_date)),
            'customer_details' => new CustomerResource($this->customer),
            'users' => UserResource::collection($this->getUsers()),
            'invoice_total' => $arrSum.' SAR'
        ];
    }

    private function getUsers(){
        $invoice = Invoice::find($this->id);
        $users = $invoice->customer->users()->with(['sessions' => function($q) use ($invoice) {
            $q->where(function($query) use ($invoice) {
                $query->whereBetween('registered', [new DateTime($invoice->start_date), new DateTime($invoice->end_date)])
                ->orWhereBetween('activated', [new DateTime($invoice->start_date), new DateTime($invoice->end_date)])
                ->orWhereBetween('appointment', [new DateTime($invoice->start_date), new DateTime($invoice->end_date)]);
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

            if(count($user->sessions) > 0){
                foreach($user->sessions as $session){

                    if($session->registered != null && $session->activated == null && $session->appointment == null){
                        array_push($registrations, date('Y-m-d', strtotime($session->registered)));
                        array_push($prices, $session->price);
                    }
                    if($session->registered == null && $session->activated != null && $session->appointment == null){
                        array_push($activations, date('Y-m-d', strtotime($session->activated)));
                        array_push($prices, $session->price);
                    }
                    if($session->registered == null && $session->activated == null && $session->appointment != null){
                        array_push($appointments, date('Y-m-d', strtotime($session->appointment)));
                        array_push($prices, $session->price);
                    }
                }
            }else {
                unset($users[$key]);
            }

            $user->registrations = $this->sortDates($registrations);
            $user->activations = $this->sortDates($activations);
            $user->appointments = $this->sortDates($appointments);

            $startDate = new DateTime($invoice->start_date);
            $endDate = new DateTime($invoice->end_date);
            $dateToCheck = new DateTime($user->created_at);
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
}
