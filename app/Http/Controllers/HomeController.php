<?php

namespace App\Http\Controllers;

// use App\Mail\VerifyEmail;
use DateTime;
use App\Models\Item;
use App\Models\User;
use App\Models\Country;
use App\Models\Setting;
use App\Models\SoldLog;
use App\Models\Category;
use Illuminate\Support\Str;

use App\Models\Transaction;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AccountDetail;
use App\Models\ManualPayment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index(request $request)
    {

        return view('welcome');
    }


    public function home(request $request)
    {

        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['get_rate2'] = Setting::where('id', 1)->first()->rate_2;
        $data['get_rate3'] = Setting::where('id', 1)->first()->rate_3;
        $data['tellbot_services'] = get_tellbot_service();
        $data['online_sim'] = getOnlineSimServices();
//        dd($data['online_sim']->countries);
//        dd($data['online_sim']);
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['margin2'] = Setting::where('id', 1)->first()->margin_2;
        $data['margin3'] = Setting::where('id', 1)->first()->margin_3;

        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->paginate('10');


        $data['order'] = 0;




        return view('home', $data);
    }


    public function pendng_sms(Request $request)
    {

        return view('receive-sms');

    }


    public function order_now(Request $request)
    {


        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {
            User::where('id', Auth::id())->update(['status' => 9]);

            Auth::logout();
            return redirect('/');

        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }





        $service = $request->service;
        $cost = $request->cost;
        $service_name = $request->name;


        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $gcost = get_d_price($service);
        $margin = Setting::where('id', 1)->first()->margin;
        $rate = Setting::where('id', 1)->first()->rate;

        $costs = $rate * $gcost['cost'] + $margin;

        $price = $costs;

        if (Auth::user()->wallet < $costs) {
            return back()->with('error', "Insufficient Funds");
        }


        $order = create_order($service, $price, $cost, $service_name);


        //dd($order);

        // if ($order == 9) {

        //     $ver = Verification::where('status', 1)->first() ?? null;
        //     if($ver != null){

        //         $data['sms_order'] = $ver;
        //         $data['order'] = 1;

        //         return view('receivesms', $data);

        //     }
        //     return redirect('home');
        // }

        if ($order == 0) {
            // User::where('id', Auth::id())->increment('wallet', $request->price);
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 0) {
            // User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "TWB VERIFY| Low balance";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 0) {
            // User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "TWB VERIFY | Error";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 1) {
            $data['services'] = get_services();
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('status' , 1)->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return redirect('home')->with('message', 'Order Placed');

            // return view('receivesms', $data);
        }
    }

    public function tellabot_order_now(Request $request)
    {


        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {
            User::where('id', Auth::id())->update(['status' => 9]);
            Auth::logout();
            return redirect('/');

        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }




        $service = $request->service;
        $cost = $request->cost;
        $rcost = $request->cost;

        if (Auth::user()->wallet < $rcost) {
            return back()->with('error', "Insufficient Funds");
        }

        $price = $request->rprice;

        $order = create_tellbot_order($service, $price, $cost);


        //dd($order);

        // if ($order == 9) {

        //     $ver = Verification::latest()->where('user_id', auth()->id())->where('status', 1)->first() ?? null;
        //     if($ver != null){

        //         $data['sms_order'] = $ver;
        //         $data['order'] = 1;

        //         return view('receivesmstella', $data);

        //     }
        //     return redirect('home');
        // }

        if ($order == 0) {
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 0) {
            $message = "TWBNUMBER | Low balance";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 0) {
            $message = "TWBNUMBER | Error";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 1) {

            User::where('id', Auth::id())->decrement('wallet', $request->price);

            $data['services'] = get_tellbot_service();
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('status' , 1)->latest()->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return redirect('home')->with('message', 'Order Placed');

            // return view('receivesmstella', $data);
        }
    }

    public function online_sms(Request $request)
    {

        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {
            User::where('id', Auth::id())->update(['status' => 9]);
            Auth::logout();
            return redirect('/');



        }

        if (Auth::user()->wallet < $request->cost) {
            return back()->with('error', "Insufficient Funds");
        }




        $service = $request->service;
        $price = $request->price;
        $cost = $request->cost;
        $country = $request->country;
        $countryText = $request->countryText;

        $cost = $request->cost;

        $order = create_online_sms_number($service, $price, $cost, $country, $countryText);



        //dd($order);

        // if ($order == 9) {

        //     $ver = Verification::latest()->where('user_id', auth()->id())->where('status', 1)->first() ?? null;
        //     if($ver != null){

        //         $data['sms_order'] = $ver;
        //         $data['order'] = 1;

        //         return view('receivesmstella', $data);

        //     }
        //     return redirect('home');
        // }

        if ($order == 0) {
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 0) {
            $message = "TWBNUMBER | Low balance";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 0) {
            $message = "TWBNUMBER | Error";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 1) {

            User::where('id', Auth::id())->decrement('wallet', $request->cost);

            $data['services'] = get_tellbot_service();
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('status' , 1)->latest()->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return redirect('home')->with('message', 'Order Placed');

            // return view('receivesmstella', $data);
        }
    }



    public function receive_sms(Request $request){

        $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id' , $request->id)->first();
        $data['order'] = 1;

        $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

        return view('receivesms', $data);

    }


    public function receive_tella_sms(Request $request){

        $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id' , $request->id)->first();
        $data['order'] = 1;

        $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

        return view('receivesmstella', $data);

    }





    public function cancle_sms(Request $request)
    {


        $order = Verification::where('id', $request->id)->first() ?? null;




        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if($request->delete == 1){

                if($order->status == 1){

                    $amount = number_format($order->cost, 2);
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    Verification::where('id', $request->id)->delete();
                    return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return redirect('home')->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }

    public function cancle_tella_sms(Request $request)
    {


        $order = Verification::where('order_id', $request->id)->first() ?? null;




        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_tella_order($orderID);

            if($request->delete == 1){

                if($order->status == 1){

                    $amount = number_format($order->cost, 2);
                    Verification::where('order_id', $request->id)->delete();
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return redirect('home')->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }


    public function cancel_online_sms(Request $request)
    {


        $order = Verification::where('order_id', $request->id)->first() ?? null;




        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_online_sms_number($orderID);

            if($request->delete == 1){

                if($order->status == 1){

                    $amount = number_format($order->cost, 2);
                    Verification::where('order_id', $request->id)->delete();
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return redirect('home')->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }

    public function check_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;


        if($request->count == 1){

            $status = $order->status;

            if($status == 1 || $status == 0){

                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");

            }
        }

        $orderID = $order->order_id;
        $chk = check_sms($orderID);
        if($chk == 3){
            return redirect('home')->with('message', 'Sms Received, order completed');
        }

        if($chk == 1){
            return back()->with('error', 'No order found');
        }

        if($chk == 2){
            return back()->with('message', 'Please wait we are getting your sms');
        }

        if($chk == 4){
            return back()->with('error', 'Order has been cancled');
        }

    }

    public function check_tella_sms(Request $request)
    {
        // dd($request->mdn);

        $order = Verification::where('phone', $request->mdn)->first() ?? null;

        // dd($order);


        // if($request->count == 1){

        //     $status = $order->status;

        //     if($status == 1 || $status == 0){

        //         $amount = number_format($order->cost, 2);
        //         User::where('id', Auth::id())->increment('wallet', $order->cost);
        //         Verification::where('id', $request->id)->delete();
        //         return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");

        //     }
        // }

        // $orderID = $order->order_id;
        $chk = check_tella_sms($order->phone);
        if($chk == 3){
            return redirect('home')->with('message', 'Sms Received, order completed');
        }

        if($chk == 1){
            return back()->with('error', 'No order found');
        }
    }

    public function  get_tella_smscode(request $request)
    {


        $sms =  Verification::where('phone', $request->mdn)->first()->sms ?? null;
        $phone =  Verification::where('phone', $request->mdn)->first()->phone;
        $order_id =  Verification::where('phone', $request->mdn)->first()->order_id ?? null;
        check_tella_sms($phone);


        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            return response()->json([
                'message' => $processedString
            ]);
        } else {

            return response()->json([
                'message' => $sms
            ]);
        }
    }

    public function fund_wallet(Request $request)
    {
        $user = Auth::id() ?? null;
        $pay = PaymentMethod::all();
        $transaction = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->paginate(10);

        return view('fund-wallet', compact('user', 'pay', 'transaction'));
    }


    public function fund_now(Request $request)
    {

        $request->validate([
            'amount'      => 'required|numeric|gt:0',
        ]);





        Transaction::where('user_id', Auth::id())->where('status', 1)->delete() ?? null;



        if ($request->type == 1) {

            if ($request->amount < 100) {
                return back()->with('error', 'You can not fund less than NGN 100');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }




            $key = env('WEBKEY');
            $ref = "VERF" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;

            $url = "https://web.sprintpay.online/pay?amount=$request->amount&key=$key&ref=$ref&email=$email";


            $data                  = new Transaction();
            $data->user_id         = Auth::id();
            $data->amount          = $request->amount;
            $data->ref_id          = $ref;
            $data->type            = 2;
            $data->status          = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund |  NGN " . number_format($request->amount) . " | with ref | $ref |  on TWB VERIFY";
            send_notification2($message);


            return Redirect::to($url);
        }



        if ($request->type == 2) {

            if ($request->amount < 100) {
                return back()->with('error', 'You can not fund less than NGN 100');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }




            $ref = "VERFM" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;


            $data                  = new Transaction();
            $data->user_id         = Auth::id();
            $data->amount          = $request->amount;
            $data->ref_id          = $ref;
            $data->type            = 2; //manual funding
            $data->status          = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund Manually |  NGN " . number_format($request->amount) . " | with ref | $ref |  on TWB VERIFY";
            send_notification2($message);







            $data['account_details'] = AccountDetail::where('id', 1)->first();
            $data['amount'] = $request->amount;

            return view('manual-fund', $data);
        }




    }


    public function fund_manual_now(Request $request)
    {



        if ($request->receipt == null) {
            return back()->with('error', "Payment receipt is required");
        }


        $file = $request->file('receipt');
        $receipt_fileName = date("ymis") . $file->getClientOriginalName();
        $destinationPath = public_path() . 'upload/receipt';
        $request->receipt->move(public_path('upload/receipt'), $receipt_fileName);


        $pay = new ManualPayment();
        $pay->receipt = $receipt_fileName;
        $pay->user_id = Auth::id();
        $pay->amount = $request->amount;
        $pay->save();


        $message = Auth::user()->email . "| submitted payment receipt |  NGN " . number_format($request->amount) . " | on TWB VERIFY";
        send_notification2($message);



        return view('confirm-pay');
    }


    public function confirm_pay(Request $request)
    {

        return view('confirm-pay');
    }



    public function verify_payment(request $request)
    {

        $trx_id = $request->trans_id;
        $ip = $request->ip();
        $status = $request->status;


        if ($status == 'failed') {


            $message = Auth::user()->email . "| Cancled |  NGN " . number_format($request->amount) . " | with ref | $trx_id |  on TWB VERIFY";
            send_notification2($message);


            Transaction::where('ref_id', $trx_id)->where('status', 1)->update(['status' => 3]);
            return redirect('fund-wallet')->with('error', 'Transaction Declined');
        }




        $trxstatus = Transaction::where('ref_id', $trx_id)->first()->status ?? null;

        if ($trxstatus == 2) {

            $message =  Auth::user()->email . "| is trying to fund  with | " . number_format($request->amount, 2) . "\n\n IP ====> " . $request->ip();
            send_notification($message);

            $message =  Auth::user()->email . "| on TWB VERIFY| is trying to fund  with | " . number_format($request->amount, 2) . "\n\n IP ====> " . $request->ip();
            send_notification2($message);

            return redirect('fund-wallet')->with('error', 'Transaction already confirmed or not found');
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.enkpay.com/api/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('trans_id' => "$trx_id"),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status1 = $var->detail ?? null;
        $amount = $var->price ?? null;




        if ($status1 == 'success') {

            $chk_trx = Transaction::where('ref_id', $trx_id)->first() ?? null;
            if ($chk_trx == null) {
                return back()->with('error', 'Transaction not processed, Contact Admin');
            }

            Transaction::where('ref_id', $trx_id)->update(['status' => 2]);
            User::where('id', Auth::id())->increment('wallet', $amount);

            $message =  Auth::user()->email . "| just funded NGN" . number_format($request->amount, 2) . " on TWB VERIFY";
            send_notification($message);





            $order_id = $trx_id;
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


            $message = Auth::user()->email . "| Just funded |  NGN " . number_format($request->amount) . " | with ref | $order_id |  on TWB VERIFY";
            send_notification2($message);






            return redirect('fund-wallet')->with('message', "Wallet has been funded with $amount");
        }

        return redirect('fund-wallet')->with('error', 'Transaction already confirmed or not found');
    }








    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {


            $user = Auth::id() ?? null;
            return redirect('home');
        }

        return back()->with('error', "Email or Password Incorrect");
    }


    public function register_index(Request $request)
    {
        return view('Auth.register');
    }


    public function login_index(Request $request)
    {
        return view('Auth.login');
    }







    public function forget_password(Request $request)
    {
        return view('Auth.forgot-password');
    }


    public function register(Request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'username' => 'required||string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'custom_price' => 0,
        ]);

        $token = Str::random(26);
        $id = $user->id;

        $details = [
            'username' => $request->username,
            'url' => 'https://oprimeverify.com/verify_email/'.$id.'/'.$token.'/'
        ];

        // Mail::to($request->email)->send(new VerifyEmail($details));

        // Log in the user after registration (optional)
        auth()->login($user);

        // Redirect the user to a protected route or dashboard
        return redirect('home');
    }

    // public function verify_email($id, $token) {
    //     $user = User::whereId($id)->whereIsVerified(0)->first();
    //     auth()->login($user);
    //     if($user) {
    //         User::whereId($id)->update(['is_verified' => 1]);
    //         return redirect('home')->with('message', 'Email verified successfully');
    //     }else{
    //         return redirect('home');
    //     }
    // }

    // public function sendMail()
    // {
    //     $token = Str::random(26);

    //     $details = [
    //         'username' => auth()->user()->username,
    //         'url' => 'https://oprimeverify.com/verify_email/'.auth()->id().'/'.$token.'/'
    //     ];

    //     Mail::to(auth()->user()->email)->send(new VerifyEmail($details));

    //     return redirect('home')->with('message', 'Email sent');
    // }





    public function profile(request $request)
    {


        $user = Auth::id();
        $orders = SoldLog::latest()->where('user_id', Auth::id())->paginate(5);


        return view('profile', compact('user', 'orders'));
    }




    public function logout(Request $request)
    {

        Auth::logout();
        return redirect('/');
    }


    public function session_resolve(request $request)
    {

   if ($request->bank_type == "providus") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }

        if ($request->bank_type == "opay") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve_others($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }

        if ($request->bank_type == "palmpay") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve_others($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }



       
    }



    public function change_password(request $request)
    {

        $user = Auth::id();


        return view('change-password', compact('user'));
    }



    public function faq(request $request)
    {
        $user = Auth::id();
        return view('faq', compact('user'));
    }

    public function terms(request $request)
    {
        $user = Auth::id();
        return view('terms', compact('user'));
    }

    public function rules(request $request)
    {
        $user = Auth::id();
        return view('rules', compact('user'));
    }






    public function update_password_now(request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($validatedData['password']),
        ]);

        // Redirect the user to a protected route or dashboard
        return back()->with('message', 'Password Changed Successfully');
    }


    // public function forget_password(request $request)
    // {

    //     $user = Auth::id() ?? null;

    //     return view('forget-password', compact('user'));
    // }

    public function reset_password(request $request)
    {

        $email = $request->email;
        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-password?code=$expiryTimestamp&email=$request->email";

        $ck = User::where('email', $request->email)->first()->email ?? null;
        $username = User::where('email', $request->email)->first()->username ?? null;


        if ($ck == $request->email) {

            User::where('email', $email)->update([
                'code' => $expiryTimestamp
            ]);

            $data = array(
                'fromsender' => 'info@oprimeverify.com', 'TWB VERIFY',
                'subject' => "Reset Password",
                'toreceiver' => $email,
                'url' => $url,
                'user' => $username,
            );


            Mail::send('reset-password-mail', ["data1" => $data], function ($message) use ($data) {
                $message->from($data['fromsender']);
                $message->to($data['toreceiver']);
                $message->subject($data['subject']);
            });



            return redirect('/forgot-password')->with('message', "A reset password mail has been sent to $request->email, if not inside inbox check your spam folder");
        } else {
            return back()->with('error', 'Email can not be found on our system');
        }
    }


    public function verify_password(request $request)
    {

        $code = User::where('email', $request->email)->first()->code;


        $storedExpiryTimestamp = $request->code;;

        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));
        } else {

            $user = Auth::id() ?? null;
            $email = $request->email;

            return view('verify-password', compact('user', 'email'));
        }
    }


    public function expired(request $request)
    {
        $user = Auth::id() ?? null;
        return view('expired', compact('user'));
    }

    public function reset_password_now(request $request)
    {

        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);


        $password = Hash::make($validatedData['password']);

        User::where('email', $request->email)->update([

            'password' => $password

        ]);

        return redirect('/login')->with('message', 'Password reset successful, Please login to continue');
    }




    public function resloveDeposit(Request $request)
    {
        $dep = Transaction::where('ref_id', $request->trx_ref)->first() ?? null;


        if ($dep == null) {
            return back()->with('error', "Transaction not Found");
        }

        if ($dep->status == 2) {
            return back()->with('error', "This Transaction has been successful");
        }


        if ($dep->status == 4) {
            return back()->with('error', "This Transaction has been resolved");
        }


        if ($dep == null) {
            return back()->with('error', "Transaction has been deleted");
        } else {

            $ref = $request->trx_ref;
            $user =  Auth::user() ?? null;
            return view('resolve-page', compact('ref', 'user'));
        }
    }


    public function  resolveNow(request $request)
    {

   if ($request->bank_type == "providus") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);
                
   
                return redirect('fund-wallet')->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }

        if ($request->bank_type == "opay") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve_others($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);

                return redirect('fund-wallet')->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }

        if ($request->bank_type == "palmpay") {

            $session_id = $request->session_id;
            $ref = $request->ref_id;

            $resolve = session_resolve_others($session_id, $ref);

            $status = $resolve[0]['status'];
            $amount = $resolve[0]['amount'];
            $message = $resolve[0]['message'];


            $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
            if ($trx == null) {

                $message = Auth::user()->email . "is trying to resolve from deleted transaction on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to reslove from deleted transaction on TWB VERIFY";
                send_notification2($message);


                return back()->with('error', "Transaction has been deleted");
            }


            $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

            if ($chk == 2 || $chk == 4) {

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "is trying to steal hits the endpoint twice on TWB VERIFY";
                send_notification2($message);

                return back()->with('message', "Error Occured");
            }


            if ($status == 'true') {

                User::where('id', Auth::id())->increment('wallet', $amount);
                Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


                $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


                $data = new Transaction();
                $data->user_id = Auth::id();
                $data->amount = $amount;
                $data->ref_id = $ref;
                $data->type = 2;
                $data->status = 2;
                $data->save();


                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification($message);

                $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on TWB VERIFY";
                send_notification2($message);

                return redirect('fund-wallet')->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$message");
            }

        }



       
    }


    public function  get_smscode(request $request)
    {


        $sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
        $order_id =  Verification::where('phone', $request->num)->first()->order_id ?? null;
        check_sms($order_id);


        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            return response()->json([
                'message' => $processedString
            ]);
        } else {

            return response()->json([
                'message' => $sms
            ]);
        }
    }



    public function webhook(request $request)
    {

        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->code;
        $country = $request->country;
        $receivedAt = $request->receivedAt;
        $orders = Verification::where('order_id', $activationId)->update([
            'sms' => $code,
            'status' => 2,
        ]);


        $message = json_encode($request->all());
        send_notification($message);


    }



public function tellaWebhook(Request $request)
{
    try {
        // Validate the required fields
        // $request->validate([
        //     'event' => 'required|string',
        //     'id' => 'required|string',
        //     'mdn' => 'required|string',
        // ]);

        // Handle only 'premium_request' events
        if ($request->event === 'premium_request' && $request->status === 'ok') {
            // Update the corresponding verification record
            $affectedRows = Verification::where('order_id', $request->id)->update(['phone' => $request->mdn]);

            if ($affectedRows > 0) {
                // Log successful update
                Log::info('Verification updated for order_id: ' . $request->id);

                // Send a notification with all incoming data
                $message = json_encode($request->all());
                send_notification($message);

                // Return a success response
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook processed successfully.',
                ], 200);
            } else {
                // If no rows were updated, log the information
                Log::warning('No verification record found for order_id: ' . $request->id);

                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found for the provided order_id.',
                ], 404);
            }
        } else if($request->event === 'incoming_message') {
        Verification::where('order_id', $request->id)->update([
            'sms' => $request->pin,
            'status' => 2,
            ]);
        $message = json_encode($request->all());
                send_notification($message);
    }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unsupported event type.',
            ], 400);
        }
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error processing webhook: ' . $e->getMessage());

        // Return a server error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while processing the webhook.',
        ], 500);
    }
}


public function simhook(Request $request) {
    try {
        if ($request->webhook_type == 'receiving_sms'){
            $id = $request->operation_id;
            // print_r($id);
            // die();
            $sms = $request->code;
            $verification = Verification::whereOrderId($id)->first()->order_id;
            if ($verification) {
                Verification::whereOrderId($id)->update([
                    'sms' => $sms,
                    'status' => 2,
                ]);
            }
        }
    }catch (\Exception $e) {
        return response([
            'message' => $e->getMessage()
        ], 500);
    }
}



    public function orders(request $request)
    {
        $orders = Verification::where('user_id', Auth::id())->get() ?? null;
        return view('orders', compact('orders'));
    }


    public function about_us(request $request)
    {

        return view('about-us');
    }


    public function policy(request $request)
    {

        return view('policy');
    }


    public function delete_order(request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            Verification::where('id', $request->id)->delete();
            return back()->with('message', "Order has been successfully deleted");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($can_order == 0) {
                return back()->with('error', "Please wait and try again later");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }





    public function e_check(request $request)
    {

        $get_user =  User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }


        return response()->json([
            'status' => true,
            'user' => $get_user->username,
        ]);
    }


    public function e_fund(request $request)
    {

        $get_user =  User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }

        User::where('email', $request->email)->increment('wallet', $request->amount) ?? null;

        $amount = number_format($request->amount, 2);

        return response()->json([
            'status' => true,
            'message' => "NGN $amount has been successfully added to your wallet",
        ]);
    }


    public function ban_users(request $request)
    {
        User::where('id', $request->id)->update(['status' => 9]);
        return back()->with('message', 'User Banned');
    }


    public function user_ban(request $request)
    {
        return view('ban');
    }

    public function unban_user(request $request)
    {

        Verification::where('user_id', $request->id)->delete();
        Transaction::where('user_id', $request->id)->where('status', 2)->delete();

        $get_wallet = User::where('id', $request->id)->first()->wallet;

        $trx = new Transaction();
        $trx->user_id = $request->id;
        $trx->ref_id = random_int(000000, 999999);
        $trx->amount = $get_wallet;
        $trx->type = 2;
        $trx->status = 2;
        $trx->save();

        User::where('id', $request->id)->update(['status' => 2]);
        return back()->with('message', 'Account unban successfully');


    }

}
