<?php

namespace App\Jobs;

use App\Mail\InvoiceEmailManager;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use Mail;
class InvoiceOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $order;
    protected $seller_products;
    protected $array;
    public function __construct($order, $seller_products, $array)
    {
        $this->order = $order;
        $this->seller_products = $seller_products;
        $this->array = $array;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('invoices.customer_invoice', compact('order'));
        $output = $pdf->output();
        file_put_contents('public/invoices/'.'Order#'.$order->code.'.pdf', $output);

        foreach($this->seller_products as $key => $seller_product){
            try {
                Mail::to(User::find($key)->email)->queue(new InvoiceEmailManager($this->array));
            } catch (\Exception $e) {

            }
        }

        unlink($this->array['file']);
    }
}
