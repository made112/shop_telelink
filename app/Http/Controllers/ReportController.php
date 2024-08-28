<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exports\BestCategoriesExport;
use App\Exports\BisanOrdersExport;
use App\Exports\BisanProductsExport;
use App\Exports\CancelledOrdersExport;
use App\Exports\CustomerOrderExport;
use App\Exports\InHouseSaleExport;
use App\Exports\PreferredProductsExport;
use App\Exports\ProductsSalesExport;
use App\Exports\SellerExport;
use App\Exports\SellerOrdersExport;
use App\Exports\SellerSaleExport;
use App\Exports\StockExport;
use App\Exports\WishExport;
use App\Order;
use App\OrderDetail;
use Illuminate\Http\Request;
use App\Product;
use App\Seller;
use App\User;
use View;
use PDF;
use Excel;
use Response;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
        if($request->has('category_id')){
            $products = Product::where('category_id', $request->category_id)->get();
        }
        else{
            $products = Product::all();
        }
        return view('reports.stock_report', compact('products'));
    }

    public function in_house_sale_report(Request $request)
    {
        if($request->has('category_id')){
            $products = Product::where('category_id', $request->category_id)->orderBy('num_of_sale', 'desc')->get();
        }
        else{
            $products = Product::orderBy('num_of_sale', 'desc')->get();
        }
        return view('reports.in_house_sale_report', compact('products'));
    }

    public function cancelled_orders(Request $request)
    {
        $orders = Order::whereHas('orderDetails', function ($q) {
            $q->where('delivery_status', 'cancelled');
        })->paginate(15);
        return view('reports.cancelled_orders', compact('orders'));
    }

    public function in_house_sale_report_pdf(Request $request)
    {
        $products = Product::orderBy('num_of_sale', 'desc')->get();
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('downloads.in_house_sale_report', compact('products'));

        return $pdf->download('in_house_sale_report.pdf');
    }

    public function seller_report(Request $request)
    {
        if($request->has('verification_status')){
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        }
        else{
            $sellers = Seller::all();
        }
        return view('reports.seller_report', compact('sellers'));
    }

    public function seller_report_export(Request $request)
    {
        $sellers = Seller::all();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.sellers_report', compact('sellers'));
                return $pdf->download('sellers.pdf');
            }
            if($request->export == 'excel') {
               return Excel::download(new SellerExport(), 'seller.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.sellers_report')->with('sellers', $sellers)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_sellers_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function cancelled_orders_export(Request $request)
    {
        if($request->has('export')){
            if ($request->export == 'pdf' || $request->export == 'word') {

                $orders = Order::whereHas('orderDetails', function ($q) {
                    $q->where('delivery_status', 'cancelled');
                })->get();

                if ($request->export == 'pdf') {
                    $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('downloads.cancelled_orders', compact('orders'));
                    return $pdf->download('cancelled_orders.pdf');
                }
                if($request->export == 'word') {
                    $view = View::make('downloads.cancelled_orders')->with('orders', $orders)->render();
                    $file_name = strtotime(date('Y-m-d H:i:s')) . '_canceller_orders_report.doc';
                    $headers = array(
                        "Content-type"=>"application/vnd.ms-word",
                        "Content-Disposition"=>"attachment;Filename=$file_name",
                        "Pragma"=> "no-cache",
                        "Expires"=> "0"
                    );
                    return Response::make($view,200, $headers);
                }
            } elseif($request->export == 'excel') {
                return Excel::download(new CancelledOrdersExport(), 'cancelled_orders.xlsx');
            }
            else {
                return ;
            }
        }
    }

    public function in_house_sale_report_export(Request $request)
    {
        $products = Product::orderBy('num_of_sale', 'desc')->get();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.in_house_sale_report', compact('products'));
                return $pdf->download('in_house_sale_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new InHouseSaleExport(), 'in_house_sale_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.in_house_sale_report')->with('products', $products)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_in_house_sale_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function products_bisan_export(Request $request)
    {
        if($request->has('export')){
                return Excel::download(new BisanProductsExport(), 'bisan_products_export.xlsx');
        }
    }

    public function orders_bisan_export(Request $request)
    {
        if($request->has('export')){
            return Excel::download(new BisanOrdersExport(), 'bisan_orders_export.xlsx');
        }
    }

    public function seller_selling_excel(Request $request)
    {
        return Excel::download(new SellerOrdersExport(decrypt($request->query->get('seller'))), 'seller_orders_export.xlsx');
    }

    public function order_customer_excel(Request $request)
    {
        return Excel::download(new CustomerOrderExport(decrypt($request->query->get('orderDetail'))), 'customer_order_export.xlsx');
    }

    public function wish_report_export(Request $request)
    {
        $products = Product::all();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.wish_report', compact('products'));
                return $pdf->download('wish_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new WishExport(), 'wish_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.wish_report')->with('products', $products)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_wish_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function preferred_products_export(Request $request)
    {
        $products = filter_products(Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(20)->get();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.preferred_products', compact('products'));
                return $pdf->download('preferred_products_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new PreferredProductsExport(), 'preferred_products_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.preferred_products')->with('products', $products)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_preferred_products_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function best_categories_export(Request $request)
    {
        $categories = Category::where('featured', 1)->get();
        $categories = collect($categories)->map(function ($category) {
            return [
                'banner' => $category->banner,
                'name' => $category->name,
                'num_of_sale' => $category->products->sum('num_of_sale')
            ];
        })->sortByDesc('num_of_sale');
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.best_categories', compact('categories'));
                return $pdf->download('best_categories_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new BestCategoriesExport(), 'best_categories_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.best_categories')->with('categories', $categories)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_best_categories_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }



    public function seller_sale_report_export(Request $request)
    {
        $sellers = Seller::all();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.seller_sale_report', compact('sellers'));
                return $pdf->download('seller_sale_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new SellerSaleExport(), 'seller_sale_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.seller_sale_report')->with('sellers', $sellers)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_seller_sale_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function stock_report_export(Request $request)
    {
        $products = Product::all();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.stock_report', compact('products'));
                return $pdf->download('stock_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new StockExport(), 'stock_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.stock_report')->with('products', $products)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_stock_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }

    public function seller_sale_report(Request $request)
    {
        if($request->has('verification_status')){
            $sellers = Seller::where('verification_status', $request->verification_status)->get();
        }
        else{
            $sellers = Seller::all();
        }
        return view('reports.seller_sale_report', compact('sellers'));
    }

    public function wish_report(Request $request)
    {
        if($request->has('category_id')){
            $products = Product::where('category_id', $request->category_id)->get();
        }
        else{
            $products = Product::all();
        }
        return view('reports.wish_report', compact('products'));
    }

    public function preferred_products() {
        return view('reports.preferred_products_report');
    }
    public function best_categories() {
        $categories = Category::where('featured', 1)->get();
        $categories = collect($categories)->map(function ($category) {
            return [
                'banner' => $category->banner,
                'name' => $category->name,
                'num_of_sale' => $category->products->sum('num_of_sale')
            ];
        })->sortByDesc('num_of_sale');
        return view('reports.best_categories_report', compact('categories'));
    }

    public function sales_report(Request $request)
    {
        $sales = $orders = Order::orderBy('code', 'desc')->select(['id', 'code', 'payment_status', 'user_id', 'grand_total'])->get();
        return view('reports.products_sales', compact('sales'));
    }

    public function products_sales_export(Request $request) {
        $sales = $orders = Order::orderBy('code', 'desc')->select(['id', 'code', 'payment_status', 'user_id', 'grand_total'])->get();
        if($request->has('export')){
            if ($request->export == 'pdf') {
                $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'logOutputFile' => storage_path('logs/log.htm'),
                    'tempDir' => storage_path('logs/')
                ])->loadView('downloads.sales_report', compact('sales'));
                return $pdf->download('products_sales_report.pdf');
            }
            if($request->export == 'excel') {
                return Excel::download(new ProductsSalesExport(), 'products_sales_report.xlsx');
            }
            if($request->export == 'word') {
                $view = View::make('downloads.sales_report')->with('sales', $sales)->render();
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_products_sales_report.doc';
                $headers = array(
                    "Content-type"=>"application/vnd.ms-word",
                    "Content-Disposition"=>"attachment;Filename=$file_name",
                    "Pragma"=> "no-cache",
                    "Expires"=> "0"
                );
                return Response::make($view,200, $headers);
            }
        }
    }
}
