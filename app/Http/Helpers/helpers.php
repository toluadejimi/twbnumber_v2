<?php

use App\Constants\Status;
use App\Models\Extension;
use App\Models\User;
use App\Models\Verification;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Auth;


function session_resolve_others($session_id, $ref){

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve-others',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $message = $var->message ?? null;
    $status = $var->status ?? null;

    $amount = $var->amount ?? null;

    return array([
        'status' => $status,
        'amount' => $amount,
        'message' => $message
    ]);


}


function resolve_complete($order_id)
{

    $curl = curl_init();

    $databody = array('order_id' => "$order_id");

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve-complete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $status = $var->status ?? null;
    if ($status == true) {
        return 200;
    } else {
        return 500;
    }
}



function send_notification($message)
{

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot6140179825:AAGfAmHK6JQTLegsdpnaklnhBZ4qA1m2c64/sendMessage?chat_id=986615350',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "986615350",
                'text' => $message,
            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
}






    function send_notification2($message)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot6493243183:AAHpZ97GioBOLayRCob64HKqe-pzUOmKntc/sendMessage?chat_id=986615350',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "986615350",
                'text' => $message,

            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
    }



function session_resolve($session_id, $ref){

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $message = $var->message ?? null;
    $status = $var->status ?? null;

    $amount = $var->amount ?? null;

    return array([
        'status' => $status,
        'amount' => $amount,
        'message' => $message
    ]);


}




function get_services(){

    $APIKEY = env('KEY');

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPricesVerification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $services = $var ?? null;

        if ($var == null) {
            $services = null;
        }

        return $services;

}

function getOnlineSimServices() {
    $APIKEY = env('ONLINESIM');
    $url = "https://onlinesim.io/api/getTariffs.php";
    $requestHeaders = array(  );
    $requestCookies = array(  );
    $requestBody = null; if ("GET" === "POST") {


    $postData = array(  );
    $requestBody = http_build_query($postData);
     } $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_COOKIE, implode("; ", $requestCookies));
    if ("GET" === "POST") {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody); }
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);


}


function get_d_price($service){
    $APIKEY = env('KEY');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPrices&service=$service",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    foreach($var as $key => $value){
        $service2['data'] =  $value;
    }



    $data['cost'] = $service2["data"]->$service->cost;
    $data['name'] = $service2["data"]->$service->name;


    return $data;

}



function create_order($service, $price, $cost, $service_name){


    // $verification = Verification::where('user_id', Auth::id())->where('status', 1)->first() ?? null;

    // if($verification != null || $verification == 1){
    //     return 9;
    // }

   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$cost",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

   $var = curl_exec($curl);
   curl_close($curl);
   $result = $var ??  null;

    if(strstr($result, "ACCESS_NUMBER") !== false) {

        User::where('id', Auth::id())->decrement('wallet', $price);

        $parts = explode(":", $result);
        $accessNumber = $parts[0];
        $id = $parts[1];
        $phone = $parts[2];

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->type = 'dailysms';
        $ver->save();
        return 1;

    }elseif($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }else{
        return 0;
    }




}

function create_tellbot_order($service, $price, $cost){





    $states = [
        'CA', 'TX', 'FL', 'NY', 'PA', 'IL', 'OH', 'GA', 'NC', 'MI',
        'NJ', 'VA', 'WA', 'AZ', 'MA', 'TN', 'IN', 'MO', 'MD', 'WI'
    ];

    $randomState = $states[array_rand($states)];

    $APIKEY = env('TELLABOT_KEY');
    $state = $randomState;
    $user = 'twbnumbers';
    $curl = curl_init();

    $markup = 20;

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=request&user={$user}&api_key={$APIKEY}&service={$service}&&markup={$markup}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ??  null;
    $result = json_decode($result, true);
    $result_d = $result['message'][0];
    $mdn = $result['message'][0]['mdn'] ?? null;






     if($result['status'] == "ok" && $mdn != null ) {

         User::where('id', Auth::id())->decrement('wallet', (int)$price);

         //  $parts = explode(":", $result);
         $accessNumber = $result_d['mdn'];
         $id = $result_d['id'];
         $phone = $result_d['mdn'];

        //  dd($accessNumber);

         $ver = new Verification();
         $ver->user_id = Auth::id();
         $ver->phone = $accessNumber;
         $ver->order_id = $id;
         $ver->country = $state;
         $ver->service = $service;
         $ver->cost = $price;
         $ver->api_cost = $cost;
         $ver->status = 1;
         $ver->type = 'tella';
         $ver->save();
         return 1;

     }elseif($result['status'] == "error") {
         return 0;
     }else{
         return 9;
     }




 }

function create_online_sms_number($service, $price, $cost, $country, $countryText){

    // $verification = Verification::latest()->where('user_id', Auth::id())->where('status', 1)->first() ?? null;

    // if($verification != null || $verification == 1){
    //     return 9;
    // }

    // dd($verification);

//    $randomState = $states[array_rand($states)];

    $APIKEY = env('ONLINESIM');
    $curl = curl_init();
    $url = "https://onlinesim.io/api/getNum.php?service={$service}&country={$country}&number=true&lang=en&key={$APIKEY}";
    $requestHeaders = array(  );
    $requestCookies = array(  );
    $requestBody = null; if ("GET" === "POST") {


        $postData = array(  );
        $requestBody = http_build_query($postData);
    } $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_COOKIE, implode("; ", $requestCookies));
    if ("GET" === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody); }
    $response = curl_exec($ch);
    $result = json_decode($response, true);
//    dd($result);

    // dd($result['mdn']);



    if($result['response'] == 1) {

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $result['number'];
        $ver->order_id = $result['tzid'];
        $ver->country = $countryText;
        $ver->service = $service;
        $ver->cost = $cost;
        $ver->api_cost = $price;
        $ver->status = 1;
        $ver->type = 'online_sms';
        $ver->save();
        return 1;

    }else{
        return 0;
    }




}

function cancel_order($orderID){


   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=setStatus&id=$orderID&status=8",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if(strstr($result, "ACCESS_CANCEL") !== false){

        return 1;

    }else{

        return 0;

    }




}


function cancel_tella_order($orderID){


    $APIKEY = env('TELLABOT_KEY');
    $user = 'twbnumbers';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=reject&user={$user}&api_key={$APIKEY}&id={$orderID}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

     $var = curl_exec($curl);
     curl_close($curl);
     $result = $var ?? null;
     $result = json_decode($result, true);

     if($result['status'] == "ok") {

         return 1;

     }else{

         return 0;

     }




 }

function cancel_online_sms_number($orderID){


    $url = "https://onlinesim.io/api/setOperationOk.php?tzid={$orderID}&ban=1";
    $requestHeaders = array(  );
    $requestCookies = array(  );
    $requestBody = null; if ("GET" === "POST") {


        $postData = array(  );
        $requestBody = http_build_query($postData);
    } $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_COOKIE, implode("; ", $requestCookies));
    if ("GET" === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody); }
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);

    if($response['response'] == "NO_COMPLETE_TZID") {

        return 1;

    }else{

        return 0;

    }




}

function check_sms($orderID){



   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getStatus&id=$orderID",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "STATUS_WAIT_CODE") !== false){

        return 2;

    }

    if(strstr($result, "STATUS_CANCEL") !== false){

        return 4;

    }




    if(strstr($result, "STATUS_OK") !== false) {


    $parts = explode(":", $result);
    $text = $parts[0];
    $sms = $parts[1];

        $data['sms'] = $sms;
        $data['full_sms'] = $sms;

        Verification::where('order_id', $orderID)->update([
            'status' => 2,
            'sms' => $sms,
            'full_sms' => $sms,
        ]);

        return 3;

    }


}

function get_tellbot_service() {
    $APIKEY = env('TELLABOT_KEY');
    $user = 'twbnumbers';

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=list_services&user={$user}&api_key={$APIKEY}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $services = $var ?? null;

        if ($var == null) {
            $services = null;
        }

        return $services;
}


function check_tella_sms($mdn){

    $APIKEY = env('TELLABOT_KEY');
    $state = 'NY';
    $user = 'twbnumbers';
    $mdn = $mdn;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=read_sms&user={$user}&api_key={$APIKEY}&mdn={$mdn}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

     $var = curl_exec($curl);
     curl_close($curl);
     $result = $var ?? null;
     $result = json_decode($result, true);
    $result_d = $result['message'][0];

     if($result['status'] == "error") {
        return 1;
     }


     if($result['status'] == "ok") {


         Verification::where('phone', $mdn)->update([
             'status' => 2,
             'sms' => $result_d['pin'],
             'full_sms' => $result_d['reply'],
         ]);

         return 3;

     }


 }
