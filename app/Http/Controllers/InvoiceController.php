<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Payment;
use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Requests\Invoice as InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::with(['payment', 'items'])->get();
        $apiResponse = InvoiceResource::collection($invoices);
        return $apiResponse->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $data = json_decode($request->getContent(), true);


        $invoice = DB::transaction(function () use ($data) {
            $date = new \DateTime($data['invoice']['created_at']);
            $result = $date->format('Y-m-d H:i:s');

            $invoice = new Invoice();
            $invoice->sold_to = $data['invoice']['sold_to'];
            $invoice->business_style = $data['invoice']['business_style'];
            $invoice->address = $data['invoice']['address'];
            $invoice->created_at = $result;
            $invoice->save();

            $grand_total = 0;
            foreach ($data['lines'] as $line) {
                $item = new Item();
                $item->invoice_id = $invoice->id;
                $item->description = $line['description'];
                $item->quantity = $line['quantity'];
                $item->price = $line['price'];
                $item->save();

                $grand_total = $grand_total + ($line['quantity'] * $line['price']);
            }

            $payment = new Payment();
            $payment->invoice_id = $invoice->id;
            $payment->receipt_no = $this->generateRandomString(2, 10);
            $payment->in_payment_of = 'Items';
            $payment->amount = $grand_total;
            $payment->save();

            return $invoice;

        }, 5);

        $apiResponse = new InvoiceResource($invoice);
        return response($apiResponse->toJson(), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $invoice = Invoice::findOrFail($request->id);
        $apiResponse = new InvoiceResource($invoice);
        return response($apiResponse->toJson(), Response::HTTP_CREATED);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $invoice = Invoice::findOrFail($data['id']);
        $invoice->delete();
        $items = Item::where('invoice_id', '=', $data['id'])->delete();
        $items = Payment::where('invoice_id', '=', $data['id'])->delete();

        
        return response(null, Response::HTTP_NO_CONTENT);
    }

    function generateRandomString($set = 1, $length = 10) {
        if($set == 1) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else if ($set == 2) {
            $characters = '0123456789';
        } else if ($set == 3) {
            $characters = 'abcdefghijklmnopqrstuvwxyz';
        } else {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
