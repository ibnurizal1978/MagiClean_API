<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailOtp;
use App\Mail\EmailVoucher;
use URL;
use Illuminate\Support\Facades\Crypt;

class MCController extends Controller
{
    public function signUp(request $r) 
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'full_name'  => ['string', 'required', 'max:100'],
            'email' => ['email', 'required','max:100'],
            'sms_checklist' => ['required'],
            'email_checklist' => ['required'],
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'SGP';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if email is already in db */
        $duplicate   = DB::table('tbl_user')
            ->where('email', '=', $r->email)
            ->count();

        if ($duplicate > 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'duplicate email address';
            return response()->json($response, 400);
        }
        /* end check if email is already in db */

        DB::table('tbl_user')->insert([
            'email'              => $r->email,
            'full_name'          => $r->full_name,
            'sms_checklist'      => $r->sms_checklist,
            'email_checklist'    => $r->email_checklist,
            'active_status'      => 0,
            'created_at'         => now()
        ]);

        /* email otp */
        $otp_code     = substr(str_shuffle('0123456789'), 0, 6);      
        $data = [
            'email'     => $r->email,
            'full_name' => $r->full_name,
            'otp_code'  => $otp_code,
        ];
        if(Mail::to($r->email)->send(new EmailOtp($data)))
        {
            $email_sent_status = 1;
        }else{
            $email_sent_status = 0;
        }
        DB::table('tbl_otp')->insert([
            'email'              => $r->email,
            'otp_code'           => $otp_code,
            'email_sent_status'  => $email_sent_status,
            'created_at'         => now(),
            'updated_at'         => now()
        ]);
        /* end email otp */

        $response['status'] = 'success';
        $response['message'] = 'success, waiting for token to activate user';
        return response()->json($response, 200);

    }

    public function inputOtp(request $r)
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email'       => ['email', 'required','max:100'],
            'otp_code'    => ['string', 'required','max:6']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'IOP';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */
        
        /* check if OTP code is not used */
        $tgl = date('Y-m-d H:i:s');
        DB::enableQueryLog();
        $check   = DB::table('tbl_otp')
        ->where('email', '=', $r->email)
        ->where('otp_code', '=', $r->otp_code)
        ->where('otp_used_status', '=', 0)   
        ->where('created_at', '>',  Carbon::now()->subMinutes(5)->toDateTimeString())
        ->count();
        DB::getQueryLog();
         

        if ($check == 0)
        {
            $response['status'] = 'error';
            $response['message'] = 'wrong OTP code or exceed time limit';
            return response()->json($response, 400);
        }
        /* end check if OTP code is not used */

        DB::table('tbl_otp') //update tbl_otp so the code will not used anymore and also check if it more than 5 minutes
        ->where('email', $r->email)
        ->where('otp_code', $r->otp_code)
        ->update([
            'otp_used_status'    => 1,
            'updated_at'         => now()
        ]);
        

        DB::table('tbl_user') //update tbl_user set active = 1
        ->where('email', $r->email)
        ->update([
            'active_status'      => 1,
            'updated_at'         => now()
        ]);

        $response['status'] = 'success';
        $response['message'] = '';
        return response()->json($response, 200); 

    }

    public function resendOtp(request $r) 
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email'       => ['email', 'required','max:100']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'RSO';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token:';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if email is there */
        $check   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->count();

        if ($check == 0)
        {
            $response['status'] = 'error';
            $response['message'] = 'Account not found , Please sign up with us';
            return response()->json($response, 400);
        }
        /* end check if email is there */

        /* email otp */
        $db = DB::table('tbl_user')
        ->select('full_name')
        ->where('email', '=', $r->email)
        ->limit(1)
        ->get();

        $otp_code     = substr(str_shuffle('0123456789'), 0, 6);      
        $data = [
            'email'     => $r->email,
            'full_name' => $db[0]->full_name, 
            'otp_code'  => $otp_code,
        ];
        if(Mail::to($r->email)->send(new EmailOtp($data)))
        {
            $email_sent_status = 1;
            //return "ok";
        }else{
            $email_sent_status = 0;
           // return "no";
        }
        DB::table('tbl_otp')->insert([
            'email'              => $r->email,
            'otp_code'           => $otp_code,
            'email_sent_status'  => $email_sent_status,
            'created_at'         => now(),
            'updated_at'         => now()
        ]);
        /* end email otp */

        $response['status'] = 'success';
        $response['message'] = 'resend success, waiting for token to activate user';
        return response()->json($response, 200);

    }

    public function login(request $r)
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email' => ['email', 'required','max:100']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'LGN';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if email is on db */
        $duplicate   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->count();

        if ($duplicate == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'Account not found , Please sign up with us';
            return response()->json($response, 400);
        }
        /* end check if email is on db */

        /* check if email is active */
        $duplicate   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->where('active_status', '=', 1)
        ->count();

        if ($duplicate == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'user is inactive';
            return response()->json($response, 400);
        }
        /* end check if email is active */

        /* check if email is already login */
        $duplicate   = DB::table('tbl_user_session')
        ->where('email', '=', $r->email)
        ->count();

        if ($duplicate > 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'duplicate login session';
            return response()->json($response, 400);
        }
        /* end check if email is already login */

        $session_id = hash("sha256",Carbon::now()->isoFormat('YYYYMMDD').substr(str_shuffle('0123456789'), 0, 10));
        DB::table('tbl_user_session')->insert([
            'email'         => $r->email,
            'session_id'    => $session_id,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);

        DB::table('tbl_user')
                ->where('email', $r->email)
                ->update([
                    'last_login' => Carbon::now()
                ]);

        $response['status'] = 'success';
        $response['message'] = $session_id;
        return response()->json($response, 200);
    }

    public function getSession(request $r)
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email'         => ['email', 'required','max:100'],
            'session_id'    => ['string', 'required','max:255']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'SSN';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */
        
        /* check if email in user session */
        $check   = DB::table('tbl_user_session')
        ->where('email', '=', $r->email)
        ->where('session_id', '=', $r->session_id)
        ->count();

        if ($check == 0)
        {
            $response['status'] = 'error';
            $response['message'] = 'data not found';
            return response()->json($response, 400);
        }
        /* end check if email in user session */

        $response['status'] = 'success';
        $response['message'] = '';
        return response()->json($response, 200); 

    }

    public function deleteSession()
    {
        /* check if session more than 2 hours */
        DB::select("DELETE FROM tbl_user_session WHERE created_at < now() - interval 2 hour");
    }

    public function sendScore(request $r) 
    {
        //$getScore = Crypt::decrypt($r->score);
        $getScore = $r->score;

        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email'         => ['email', 'required','max:100'],
            'score'         => ['integer', 'required','min:0'],
            'time'          => ['required','date_format:H:i:s']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'SSC';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date.$getScore);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if email is on db */
        $check   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'Account not found , Please sign up with us';
            return response()->json($response, 400);
        }
        /* end check if email is on db */

        /* check if user active status is 1 */
        $check   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->where('active_status', '=', 1)
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'user is inactive';
            return response()->json($response, 400);
        }
        /* end check if user active status is 1 */

        /* check if user first time play */
        $checkFirstTime   = DB::table('tbl_leaderboard')
        ->where('email', '=', $r->email)
        ->count();

        if ($checkFirstTime == 0) 
        {
            $highScore  = true;
            $score      = $getScore;
            $firstTime  = true;
        }else{
            $firstTime  = false;
            /* check highscore */
            $data = DB::table('tbl_leaderboard')
            ->where('email', $r->email)
            ->orderby('score','DESC')
            ->limit(1)
            ->get();

            if($r->score > $data[0]->score) 
            {
                $highScore  = true;
                $score      = $getScore;
            }else{
                $highScore  = false;
                $score      = $data[0]->score;
            }
            /* end check highscore */

        }
        /* end check if user first time play */

        DB::table('tbl_leaderboard')->insert([
            'email'         => $r->email,
            'score'         => $getScore,
            'time'          => $r->time,
            'created_at'    => now(),
            'updated_at'    => now()
        ]);

        $firstTime50 = false;
        if($r->score > 49) //check if score min 50, is this user sent email voucher code? if no then send
        {
            $check   = DB::table('tbl_email_voucher')
            ->where('email', '=', $r->email)
            ->count();
    
            if ($check == 0) //send email, because this user not yet send email
            {   
                $data = '';
                Mail::to($r->email)->send(new EmailVoucher($data));
                DB::table('tbl_email_voucher')->insert([
                    'email'         => $r->email,
                    'created_at'    => now()
                ]);

                DB::table('tbl_user')
                ->where('email', $r->email)
                ->update([
                    'email_sent_status' => 1
                ]);

                $firstTime50 = true;
            }

        }else{
        $firstTime50 = false;
        }

        $response['status']     = 'success';
        $response['message']    = $highScore;
        $response['highScore']  = $score;
        $response['score']      = $getScore;
        $response['firstTime']  = $firstTime;
        $response['firstTime50']  = $firstTime50;
        return response()->json($response, 200);
    }

    public function getPage(request $r) 
    {
        /* check input validation */
        $validate   = validator::make($r->all(), [
            'email'         => ['email', 'required','max:100'],
            'page_name'     => ['string', 'required','max:50']
        ]);

        if($validate->fails())
        {
            $response['status'] = 'error';
            $response['message'] = 'validation failed';
            return response()->json($response, 400);
        }
        /* end check input validation */

        /* check token */
        $app_id     = 'GTP';
        $email      = $r->input('email');
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$email.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if email is on db */
        $check   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'Account not found , Please sign up with us';
            return response()->json($response, 400);
        }
        /* end check if email is on db */

        /* check if user active status is 1 */
        $check   = DB::table('tbl_user')
        ->where('email', '=', $r->email)
        ->where('active_status', '=', 1)
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'user is inactive';
            return response()->json($response, 400);
        }
        /* end check if user active status is 1 */

        DB::table('tbl_log_page')->insert([
            'email'         => $r->email,
            'page_name'     => $r->page_name,
            'sub_page'      => $r->sub_page,
            'params'        => $r->params,
            'ip_address'    => $r->ip(),
            'url'           => URL::full(),
            'created_at'    => now()
        ]);
        
        $response['status'] = 'success';
        $response['message'] = 'success';
        return response()->json($response, 200);
    }

    public function viewScore1(request $r) 
    {

        /* check token */
        $app_id     = 'VSC';
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token'.$token;
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if data empty */
        $check   = DB::table('tbl_leaderboard')
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'empty data';
            return response()->json($response, 400);
        }
        /* end check if data empty */

        /* display data */
        $data  = DB::table('tbl_leaderboard')
        ->select()
        ->orderby('score', 'desc')
        ->limit(10)
        ->get();
        /*$data = DB::select(
            DB::raw('SELECT DISTINCT email, time, max(score) as score FROM tbl_leaderboard group by email ORDER BY score DESC')
        );*/
        /* display data */
        $arr = array($data);

        $arrNew = array();
        $incI = 0;
        foreach($arr AS $arrKey => $arrData){
            $arrNew[$incI]['email'] = $arrKey;
           // $arrNew[$incI]['score1'] = $arrData['score'];
            $incI++;
        }
        $encoded = json_encode($arrNew);
        
        $response['status'] = 'success';
        $response['message'] = $arr;
        return response()->json($response, 200);
    }

    public function viewScore(request $r) 
    {

        /* check token */
        $app_id     = 'VSC';
        $date       = Carbon::now()->isoFormat('YYYYMMDD');
        $token      = hash("sha256",$app_id.$date);
        
        if($token <> $r->input('token'))
        {
            $response['status'] = 'error';
            $response['message'] = 'invalid token';
            return response()->json($response, 400);
        }
        /* end check token */

        /* check if data empty */
        $check   = DB::table('tbl_leaderboard')
        ->count();

        if ($check == 0) 
        {
            $response['status'] = 'error';
            $response['message'] = 'empty data';
            return response()->json($response, 400);
        }
        /* end check if data empty */

        /* display data */
        /*$data  = DB::table('tbl_leaderboard')
        ->select()
        ->orderby('score', 'desc')
        ->limit(10)
        ->get();*/
        $data = DB::select(
            DB::raw('SELECT DISTINCT email, max(score) as score, max(time) as time FROM tbl_leaderboard group by email ORDER BY score DESC limit 20')
        );
        /* display data */
        $arr = array($data);

        $arrNew = array();
        $incI = 0;
        foreach($arr AS $arrKey => $arrData){
            $arrNew[$incI]['email'] = $arrKey;
           // $arrNew[$incI]['score1'] = $arrData['score'];
            $incI++;
        }
        $encoded = json_encode($arrNew);
        
        $response['status'] = 'success';
        $response['message'] = $arr;
        return response()->json($response, 200);
    }

}