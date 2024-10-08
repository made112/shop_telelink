<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;
use App\BusinessSetting;
use Artisan;
use CoreComponentRepository;
use Illuminate\Support\Facades\Validator;


class BusinessSettingsController extends Controller
{
    public function activation(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
    	return view('business_settings.activation');
    }

    public function social_login(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.social_login');
    }

    public function smtp_settings(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.smtp_settings');
    }
    public function bisan_settings(Request $request)
    {
        return view('bisan.configuration');
    }

    public function google_analytics(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.google_analytics');
    }

    public function google_recaptcha(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.google_recaptcha');
    }

    public function facebook_chat(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.facebook_chat');
    }

    public function payment_method(Request $request)
    {
//        CoreComponentRepository::instantiateShopRepository();
        return view('business_settings.payment_method');
    }

    /**
     * Update the API key's for payment methods.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payment_method_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        $business_settings = BusinessSetting::where('type', $request->payment_method.'_sandbox')->first();
        // dd($business_settings->type);
        if($business_settings != null){
            if ($request->has($request->payment_method.'_sandbox')) {
                $business_settings->value = 1;
                $business_settings->save();
            }
            else{
                $business_settings->value = 0;
                $business_settings->save();
            }
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    /**
     * Update the API key's for GOOGLE analytics.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function google_analytics_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        $business_settings = BusinessSetting::where('type', 'google_analytics')->first();

        if ($request->has('google_analytics')) {
            $business_settings->value = 1;
            $business_settings->save();
        }
        else{
            $business_settings->value = 0;
            $business_settings->save();
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    public function google_recaptcha_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
            $this->overWriteEnvFile($type, $request[$type]);
        }

        $business_settings = BusinessSetting::where('type', 'google_recaptcha')->first();

        if ($request->has('google_recaptcha')) {
            $business_settings->value = 1;
            $business_settings->save();
        }
        else{
            $business_settings->value = 0;
            $business_settings->save();
        }

        flash("Settings updated successfully")->success();
        return back();
    }


    /**
     * Update the API key's for GOOGLE analytics.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function facebook_chat_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        $business_settings = BusinessSetting::where('type', 'facebook_chat')->first();

        if ($request->has('facebook_chat')) {
            $business_settings->value = 1;
            $business_settings->save();
        }
        else{
            $business_settings->value = 0;
            $business_settings->save();
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    public function facebook_pixel_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        $business_settings = BusinessSetting::where('type', 'facebook_pixel')->first();

        if ($request->has('facebook_pixel')) {
            $business_settings->value = 1;
            $business_settings->save();
        }
        else{
            $business_settings->value = 0;
            $business_settings->save();
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    /**
     * Update the API key's for other methods.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function env_key_update(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    /**
     * overWrite the Env File values.
     * @param  String type
     * @param  String value
     * @return \Illuminate\Http\Response
     */
    public function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"'.trim($val).'"';
            if(is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0){
                file_put_contents($path, str_replace(
                    $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
                ));
            }
            else{
                file_put_contents($path, file_get_contents($path)."\r\n".$type.'='.$val);
            }
        }
    }

    public function seller_verification_form(Request $request)
    {
    	return view('business_settings.seller_verification_form');
    }

    /**
     * Update sell verification form.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function seller_verification_form_update(Request $request)
    {
        $form = array();
        $select_types = ['select', 'multi_select', 'radio'];
        $j = 0;
        for ($i=0; $i < count($request->type); $i++) {
            $item['type'] = $request->type[$i];
            $item['label'] = $request->label[$i];
            if(in_array($request->type[$i], $select_types)){
                $item['options'] = json_encode($request['options_'.$request->option[$j]]);
                $j++;
            }
            array_push($form, $item);
        }
        $business_settings = BusinessSetting::where('type', 'verification_form')->first();
        $business_settings->value = json_encode($form);
        if($business_settings->save()){
            flash("Verification form updated successfully")->success();
            return back();
        }
    }

    public function update(Request $request)
    {
        foreach ($request->types as $key => $type) {
            $business_settings = BusinessSetting::where('type', $type)->first();
            if($business_settings!=null){
                $business_settings->value = $request[$type];
                $business_settings->save();
            }
            else{
                $business_settings = new BusinessSetting;
                $business_settings->type = $type;
                $business_settings->value = $request[$type];
                $business_settings->save();
            }
        }
        flash("Settings updated successfully")->success();
        return back();
    }

    public function updateActivationSettings(Request $request)
    {
        $env_changes = ['FORCE_HTTPS'];
        if (in_array($request->type, $env_changes)) {

            return $this->updateActivationSettingsInEnv($request);
        }


        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if($business_settings!=null){
            if ($request->type == 'maintenance_mode' && $request->value == '1') {
                Artisan::call('down');
            }
            elseif ($request->type == 'maintenance_mode' && $request->value == '0') {
                Artisan::call('up');
            }
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        else{
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        return '1';
    }

    public function updateActivationSettingsInEnv($request)
    {
        if ($request->type == 'FORCE_HTTPS' && $request->value == '1') {
            $this->overWriteEnvFile($request->type, 'On');

            if(strpos(env('APP_URL'), 'http:') !== FALSE) {
                $this->overWriteEnvFile('APP_URL', str_replace("http:", "https:", env('APP_URL')));
            }

        }
        elseif ($request->type == 'FORCE_HTTPS' && $request->value == '0') {
            $this->overWriteEnvFile($request->type, 'Off');
            if(strpos(env('APP_URL'), 'https:') !== FALSE) {
                $this->overWriteEnvFile('APP_URL', str_replace("https:", "http:", env('APP_URL')));
            }

        }

        return '1';
    }
    protected function validator(array $data, $type = 'add')
    {
        $validation_array = [
            'value'=>"required|numeric"
        ];
        $validation_messages = [
            "value.required"=>"the value must be required",
            "value.numeric"=>"the value must be numbers"
        ];
        if ($type === 'levels') {
            $validation_array = [
                'bronze' => 'required|numeric',
                'silver' => 'required|numeric',
                'gold' => 'required|numeric',
                'diamond' => 'required|numeric',
            ];
            $validation_messages = [];
        }
        return Validator::make($data, $validation_array, $validation_messages);
    }

    public function vendor_commission(Request $request)
    {
        $business_settings = BusinessSetting::where('type', 'vendor_commission')->first();
        return view('business_settings.vendor_commission', compact('business_settings'));
    }

    public function vendor_levels(Request $request)
    {
        return view('business_settings.vendor_levels');
    }
    public function vendor_levels_update(Request $request){
        $this->validator($request->all(),'levels')->validate();

        $bronze = BusinessSetting::where('type', $request->type_bronze)->first();
        $silver = BusinessSetting::where('type', $request->type_silver)->first();
        $gold = BusinessSetting::where('type', $request->type_gold)->first();
        $diamond = BusinessSetting::where('type', $request->type_diamond)->first();

        if ($bronze != null) {
            $bronze->value = $request->bronze;
        }else {
            $bronze = new BusinessSetting;
            $bronze->type = $request->type_bronze;
            $bronze->value = $request->bronze;
        }
        $bronze->save();

        if ($silver != null) {
            $silver->value = $request->silver;
        }else {
            $silver = new BusinessSetting;
            $silver->type = $request->type_silver;
            $silver->value = $request->silver;
        }
        $silver->save();

        if ($gold != null) {
            $gold->value = $request->gold;
        }else {
            $gold = new BusinessSetting;
            $gold->type = $request->type_gold;
            $gold->value = $request->gold;
        }
        $gold->save();

        if ($diamond != null) {
            $diamond->value = $request->diamond;
        }else {
            $diamond = new BusinessSetting;
            $diamond->type = $request->type_diamond;
            $diamond->value = $request->diamond;
        }
        $diamond->save();
        flash('Seller levels updated successfully')->success();
        return back();
    }
    public function vendor_commission_update(Request $request){
        $this->validator($request->all(),'update')->validate();

        $business_settings = BusinessSetting::where('type', $request->type)->first();
        $business_settings->type = $request->type;
        $business_settings->value = $request->value;
        $business_settings->save();

        flash('Seller Commission updated successfully')->success();
        return back();
    }

    public function shipping_configuration(Request $request){
        return view('shipping_configuration.index');
    }

    public function shipping_configuration_update(Request $request){
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        $business_settings->value = $request[$request->type];
        $business_settings->save();
        return back();
    }
}
