<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ReviewCollection;
use App\Product;
use App\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($id)
    {
        return new ReviewCollection(Review::where('product_id', $id)->latest()->get());
    }

    public function createReview(Request $request)
    {
        try {
            $product = Product::find($request->product_id);
            $orderDetails = $product->orderDetails;
            $commentable = false;
            foreach ($orderDetails as $orderDetail) {
                if ($orderDetail->order != null && $orderDetail->order->user_id == $request->user_id && $orderDetail->delivery_status == 'delivered') {
                    $commentable = true;
                }
            }
            if (Review::where('user_id', $request->user_id)->where('product_id', $product->id)->first() != null) {
                return response()->json('You have rated this product', 401);
            }
            if (!$commentable) {
                return response()->json('You should purchase this product and received it, then you can rating', 401);
            }

            $review = new Review;
            $review->product_id = $request->product_id;
            $review->user_id = $request->user_id;
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->viewed = '0';
            if ($review->save()) {
                $product = Product::findOrFail($request->product_id);
                if (count(Review::where('product_id', $product->id)->where('status', 1)->get()) > 0) {
                    $product->rating = Review::where('product_id', $product->id)->where('status', 1)->sum('rating') / count(Review::where('product_id', $product->id)->where('status', 1)->get());
                } else {
                    $product->rating = 0;
                }
                $product->save();
                return response()->json('Review has been submitted successfully', 200);
            }
            return response()->json('Something went wrong', 400);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
    }
}
