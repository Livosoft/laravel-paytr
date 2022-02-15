<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Balances;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class UserController extends Controller
{

    public function odeme_ekraniq(Request $request)
    {

        //Kullanıcı Bilgilerimi Çekiyorum
        $user = User::where('id',Auth::user()->id)->first();

        //Random Bir Key Oluşturuyorum - Hem Paytr Hemde Sistemim İçin
        $no = rand(23123,5345345);

        $merchant_id 	= 'zxxxx';
        $merchant_key 	= 'zxxxx';
        $merchant_salt	= 'zxxxx';

        $email = $user->email;
        $payment_amount	= $request->miktar*100;
        $merchant_oid = $no;
        $user_name = $user->name;
        $user_address = $user->user_address;
        $user_phone = $user->user_phone;
        $merchant_ok_url = "https://www.majesgame.com/basarili-odeme"; //paytr callback tarafında aynı url olmayacak hataya sebep olur
        $merchant_fail_url = "https://www.majesgame.com/odeme-basarisiz";
        $user_basket = base64_encode(json_encode(array("Bakiye", $request->miktar, 1)));

        ## Kullanıcının IP adresi
        if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }


        $user_ip=$ip;

        $timeout_limit = "30";
        $debug_on = 1;
        $test_mode = 0;
        $no_installment	= 0;
        $max_installment = 0;

        $currency = "TL";

        ####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
        $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;
        $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
        $post_vals=array(
                'merchant_id'=>$merchant_id,
                'user_ip'=>$user_ip,
                'merchant_oid'=>$merchant_oid,
                'email'=>$email,
                'payment_amount'=>$payment_amount,
                'paytr_token'=>$paytr_token,
                'user_basket'=>$user_basket,
                'debug_on'=>$debug_on,
                'no_installment'=>$no_installment,
                'max_installment'=>$max_installment,
                'user_name'=>$user_name,
                'user_address'=>$user_address,
                'user_phone'=>$user_phone,
                'merchant_ok_url'=>$merchant_ok_url,
                'merchant_fail_url'=>$merchant_fail_url,
                'timeout_limit'=>$timeout_limit,
                'currency'=>$currency,
                'test_mode'=>$test_mode
            );

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);



        $result = @curl_exec($ch);

        if(curl_errno($ch))
            die("PAYTR IFRAME connection error. err:".curl_error($ch));

        curl_close($ch);

        $result=json_decode($result,1);

        if($result['status']=='success')
            $token=$result['token'];
        else
            die("PAYTR IFRAME failed. reason:".$result['reason']);

            $ekle = Balances::insert([
                "user_id" =>$user->id,
                "balance_token" => $no,
                "yuklenen" => $request->miktar,
                "status" => 0,
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            return view('frontend.user.success',compact('token'));
    }

    public function odeme_basarili(Request $request)
    {

        if(!$request->has('merchant_oid') && !$request->has('status') && !$request->has('total_amount')) { exit("OK"); }


        $post = $_REQUEST;


        ####################### DÜZENLEMESİ ZORUNLU ALANLAR #######################
        #
        ## API Entegrasyon Bilgileri - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
        $merchant_key 	= 'zxxxx';
        $merchant_salt	= 'zxxxx';
        ###########################################################################

        ####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
        #
        ## POST değerleri ile hash oluştur.
        $hash = base64_encode( hash_hmac('sha256', $post['merchant_oid'].$merchant_salt.$post['status'].$post['total_amount'], $merchant_key, true) );
        #

        //burada paytrden dönen tokeni alıyorum bakiye tabloma eşleştiriyorum
        $find = \App\Models\Balances::where('balance_token',$post['merchant_oid'])->first();
        $finduser = User::where('id',$find->user_id)->first();

        if( $post['status'] == 'success' ) { ## Ödeme Onaylandı

            //işlem başarılı ile işlemimi yapıyorum
         $update = User::where('id',$finduser->id)->update([
                "balance" => $finduser->balance+$find->yuklenen,

            ]);

            $upbal = Balances::where('balance_token',$post['merchant_oid'])->update([
                "status" => 1,
            ]);



        } else { ## Ödemeye Onay Verilmedi



        }

        ## Bildirimin alındığını PayTR sistemine bildir.
        echo "OK";
        exit;

    }

        // burada gelen callback'i buraya yönlendiriyorum ki tekrar tekrar bakiye vermesin
    public function basariliodeme()
    {
        return view('frontend.user.nots');
    }




##########################################################################


}
