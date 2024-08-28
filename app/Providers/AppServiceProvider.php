<?php

namespace App\Providers;

use App\BusinessSetting;
use App\Order;
use Illuminate\Support\Facades\Auth;
use App\Conversation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('frontend.inc.nav', function ($view){
            if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
                if(Auth::check()){
                    $view->with('conversations_nav', Conversation::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderBy('updated_at', 'desc')->take(4)->get());
                }else {
                    $view->with('conversations_nav', []);
                }
            }
        });
        View::composer('frontend.inc.nav', function ($view){
            if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
                if(Auth::check()){
                    $view->with('unread_messages', Conversation::where('sender_id', Auth::user()->id)
                                                                        ->where('sender_viewed', 0)
                                                                        ->orWhere('receiver_id', Auth::user()->id)
                                                                        ->where('receiver_viewed', 0)
                                                                        ->get()->count());
                }else {
                    $view->with('unread_messages', 0);
                }
            }
        });

        View::composer('frontend.inc.nav', function ($view){
           if(Auth::check()){
               $orders = Order::where('user_id', Auth::user()->id)->orderBy('code','desc')
                   ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                   ->where('orders.viewed', 0)
                   ->select('orders.id')
                   ->distinct()
                   ->count();
               $view->with('count_orders', $orders);
           }else {
               $view->with('count_orders', 0);
           }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
