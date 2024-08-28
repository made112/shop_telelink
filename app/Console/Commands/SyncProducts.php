<?php

namespace App\Console\Commands;

use App\Http\Controllers\BisanController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all items on bisan system with products iBuy e-commerce';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bisan = new BisanController();

//        Sync Units
        $units = $bisan->fetchUnit();
        if (isset($units)){
            foreach ($units->getData() as $key => $unit) {
                $update_unit = DB::table('units')->where('code', '=', $unit->code)->first();
                if($update_unit === null){
                    DB::table('units')->insert([
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'nameAR' => $unit->nameAR,
                        'status' => 0,
                        'symbol' => $unit->symbol
                    ]);
                } else {
                    DB::table('units')->where('code', $unit->code)->update([
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'nameAR' => $unit->nameAR,
                        'status' => 1,
                        'symbol' => $unit->symbol
                    ]);
                }
            }
        }

//        Sync Item Categories
        $categories = DB::table('sub_categories')->where('category_id', '=', '15')->get(['code']);
        $arr_categories_codes = array();


        foreach ($categories as $c ) {
            if ($c->code) {
                array_push($arr_categories_codes, $c->code);
            }
        }
        $itemCategories = $bisan->fetchItemCategories();
        if (isset($itemCategory)){
            foreach ($itemCategories->getData() as $key => $itemCategory) {
                if(! in_array($itemCategory->code, $arr_categories_codes)) {
                    DB::table('sub_categories')->insert([
                        'name' => $itemCategory->name,
                        'category_id' => '15',
                        'slug' =>  preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $itemCategory->name)) . '-' . Str::random(5),
                        'code' => $itemCategory->code
                    ]);
                }
            }
        }

//        Sync Products
        $products = $bisan->fetchProducts();
        $inhouseProducts = DB::table('products')->where('added_by', 'admin')->select(['bsn_code', 'name', 'id'])->get();
        $arr_codes = array();
        foreach ($inhouseProducts as $p) {
            if ($p->bsn_code) {
                array_push($arr_codes, $p->bsn_code);
            }
        }
        if (isset($products)){
            foreach ($products->getData() as $product) {
                if (! isset($product->isAssetItem)) {
                    if (! in_array($product->code, $arr_codes)) {
                        $unit = DB::table('units')->where('code', '=', $product->smallestUnit)->first(['id', 'code']);
                        $default_unit = 'PCS';
                        if($unit !== null and $unit->code !== null) {
                            $default_unit = $unit->code;
                        }
                        $category_id = 15;
                        $subcategory_id = 54;
                        if(isset($product->itemCategory) && $product->itemCategory !== null) {
                            $subcategory_id = DB::table('sub_categories')->where('code', '=', $product->itemCategory)->first('id');
                        }
                        $price = $bisan->fetchPrice($product->code);
                        $unit_price = null;
                        $purchase_price = null;
                        if (isset($price)) {
                            if (is_array($price->getData()) and $price->getData() !== null and count($price->getData()) > 0) {
                                foreach ($price->getData() as $key => $p) {
                                    if($p->priceList === 'P') {
                                        $purchase_price = $p->rawPrice;
                                    }elseif ($p->priceList === 'S') {
                                        $unit_price = $p->rawPrice;
                                    }
                                }
                            }
                        }
                        DB::table('products')->insert([
                            'user_id' => isset($inhouseProducts->first()->id) ? $inhouseProducts->first()->id : 12,
                            'name' => $product->name,
                            'added_by' => 'admin',
                            'bsn_code' => $product->code,
                            'published' => false,
                            'unit' => $default_unit,
                            'category_id' => $category_id,
                            'subcategory_id' => isset($subcategory_id->id) ? $subcategory_id->id : 59,
                            'tax' => isset($product->commission) ? floatval($product->commission) : 0,
                            'tax_type' => 'percent',
                            'refundable' => '1',
                            'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $product->name)) . '-' . Str::random(5),
                            'unit_price' => $unit_price ? $unit_price : 0,
                            'purchase_price' => $purchase_price ? $purchase_price : 0
                        ]);

                    }
                }else {
                    DB::table('products')->where('bsn_code', $product->code)->delete();
                }
            }
        }

//        Sync Quantities
        $quantities = $bisan->getAllQuantities();
        if (isset($quantities)) {
            foreach ($quantities->getData() as $key => $qty) {
                if(in_array($qty->item, $arr_codes)) {
                   DB::table('products')->where('bsn_code', strval($qty->item))->update([
                        'current_stock' => intval(str_replace(',', '', strval($qty->endBalance))),
                    ]);
                }
            }
        }
    }
}
