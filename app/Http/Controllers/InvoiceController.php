<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Response;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserCollection;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicedSession;
use App\Models\User;
use Illuminate\Support\Carbon;
use DateTime;

class InvoiceController extends Controller
{
    use Response;

    public function invoices(InvoiceRequest $request)
    {
        if(!$this->customerExists($request->customer_id))
            return $this->response([], 404, false, ['Customer not found']);

        $attributes = $this->attributes($request);
        $created = Invoice::create($attributes);

        return $this->response(['invoice_id' => $created->id], 201, false, ['Invoice created']);
    }

    public function getInvoices($id)
    {
        if(!$this->invoiceExists($id))
            return $this->response([], 404, false, ['Invoice not found']);


        $invoice = Invoice::find($id);

        $users = $invoice->customer->users()->with(['sessions' => function($q) use ($invoice) {
            $q->where(function($query) use ($invoice) {
                $query->whereBetween('activated', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)])
                      ->orWhereBetween('appointment', [$this->toDateTime($invoice->start_date), $this->toDateTime($invoice->end_date)]);
            });
            $q->doesntHave('invoicedSessions');
        }])->get();

        foreach ($users as $user) {
            foreach($user->sessions as $session){
                InvoicedSession::create(['invoice_id' =>$id, 'session_id' => $session->id, 'user_id' => $user->id ]);
            }
        }

        return $this->response(['invoice_data' => new InvoiceResource($invoice)], 200, false, ['Retreived Successfully']);
    }

    public function customerExists($id)
    {
        return Customer::where('id', $id)->exists();
    }

    public function invoiceExists($id)
    {
        return Invoice::where('id', $id)->exists();
    }

    private function toDateTime($date){
        return new DateTime($date);
    }

    protected function attributes($request)
    {
        return $request->only([
            'customer_id',
            'start_date',
            'end_date'
        ]);
    }
}
