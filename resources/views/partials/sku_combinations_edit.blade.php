@if(count($combinations[0]) > 0)
	<table class="table table-bordered">
		<thead>
		<tr>
			<td class="text-center">
				<label for="" class="control-label">{{__('Variant')}}</label>
			</td>
			<td class="text-center">
				<label for="" class="control-label">{{__('Variant Price')}}</label>
			</td>
            <td class="text-center">
				<label for="" class="control-label">{{__('Price After Discount')}}</label>
			</td>
			<td class="text-center">
				<label for="" class="control-label">{{__('SKU')}}</label>
			</td>
			<td class="text-center">
				<label for="" class="control-label">{{__('Quantity')}}</label>
			</td>
		</tr>
		</thead>
		<tbody>

		@foreach ($combinations as $key => $combination)
			@php
				$sku = $product_id;
                foreach (explode(' ', $product_name) as $key => $value) {

                   // $sku .= mb_substr($value, 0, 1, "utf-8");
                }

                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                        $sku .='-'.str_replace(' ', '', $item);
                    }
                    else{
                        if($colors_active == 1){
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                            $sku .='-'.$color_name;
                        }
                        else{
                            $str .= str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                        }
                    }
                }
			@endphp
			@if(strlen($str) > 0)
				<tr>
					<td>
						<label for="" class="control-label">{{ $str }}</label>
					</td>
					<td>
						<input type="number" name="price_{{ $str }}" value="@php
							if ($product->unit_price == $unit_price) {
                                if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                    echo $stock->getOriginal('price');
                                }
                                else{
                                    echo $unit_price;
                                }
                            }
                            else{
                                echo $unit_price;
                            }
						@endphp" min="0" step="0.01" class="form-control" required>
					</td>
                    <td>
						<input type="number" name="discount_{{ $str }}" value="@php
                                if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                    echo $stock->discount;
                                }
                                else{
                                    echo $discount;
                                }
						@endphp" min="0" step="0.01" class="form-control" required>
					</td>
					<td>
						<input type="text" name="sku_{{ $str }}" value="{{ $sku }}" class="form-control" required>
					</td>
					<td>
						<input type="number" name="qty_{{ $str }}" value="@php
							if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                echo $stock->qty;
                            }
                            else{
                                echo '10';
                            }
						@endphp" min="0" step="1" class="form-control" required>
					</td>
				</tr>
			@endif
		@endforeach

		</tbody>
	</table>
@endif
