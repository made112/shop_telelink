<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use PDF;
use Auth;
use Spipu\Html2Pdf\Html2Pdf;

class InvoiceController extends Controller
{
    //downloads customer invoice
    public function customer_invoice_download($id)
    {
        $order = Order::findOrFail($id);
//        $pdf = PDF::setOptions([
//                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
//                        'logOutputFile' => storage_path('logs/log.htm'),
//                        'tempDir' => storage_path('logs/')
//                    ])->loadView('invoices.customer_invoice', compact('order'));
//        return $pdf->download('order-'.$order->code.'.pdf');

        $title = ucwords(str_replace('_',' ', config('app.name')));
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
        if (app()->getLocale() == 'sa') {
            $content = view('invoices.customer_invoice_sa', compact('order'))->render();
        }else {
            $content = view('invoices.customer_invoice', compact('order'))->render();
        }
        if ($content && str_contains($content, 'الله')) {
            $content =  str_replace('الله','اللـه', $content);
        }
        $html2pdf->writeHTML($content);
        $filename = $title.' '.date("d/m/Y") . '.pdf';
        $html2pdf->output($filename);
    }

    //downloads seller invoice
    public function seller_invoice_download($id)
    {
        $order = Order::findOrFail($id);
//        $pdf = PDF::setOptions([
//                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
//                        'logOutputFile' => storage_path('logs/log.htm'),
//                        'tempDir' => storage_path('logs/')
//                    ])->loadView('invoices.seller_invoice', compact('order'));
//        return $pdf->download('order-'.$order->code.'.pdf');

        $title = ucwords(str_replace('_',' ', config('app.name')));
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
        if (app()->getLocale() == 'sa') {
            $content = view('invoices.seller_invoice_sa', compact('order'))->render();
            if ($content && str_contains($content, 'الله')) {
                $content =  str_replace('الله','اللـه', $content);
            }
        }else {
            $content = view('invoices.seller_invoice', compact('order'))->render();
        }
        if ($content && str_contains($content, 'الله')) {
            $content =  str_replace('الله','اللـه', $content);
        }
        $html2pdf->writeHTML($content);
        $filename = $title.' '.date("d/m/Y") . '.pdf';
        $html2pdf->output($filename);
    }

    //downloads admin invoice
    public function admin_invoice_download($id)
    {

        $order = Order::findOrFail($id);
//        $pdf = PDF::setOptions([
//                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
//                        'logOutputFile' => storage_path('logs/log.htm'),
//                        'tempDir' => storage_path('logs/')
//                    ])->loadView('invoices.admin_invoice', compact('order'));
//        return $pdf->download('order-'.$order->code.'.pdf');
        $title = ucwords(str_replace('_',' ', config('app.name')));
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
        $content = view('invoices.seller_invoice', compact('order'))->render();
        if ($content && str_contains($content, 'الله')) {
            $content =  str_replace('الله','اللـه', $content);
        }
        $html2pdf->writeHTML($content);
        $filename = $title.' '.date("d/m/Y") . '.pdf';
        $html2pdf->output($filename);
    }
}
