<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Tenants;
use App\TenantSettings;
use App\TransactionDetails;
use App\User;
use Auth;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use Validator;
use View;

class InternalUserController extends Controller
{
    private $paypalObject = '';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->paypalObject = new ExpressCheckout;
    }

    /*=============================================================
    =            Section Internal User Login Functions            =
    =============================================================*/
    /**
     *
     * Login Screen Display Function
     *
     */

    public function login_screen(Request $req)
    {
        return view('company_portal.layouts.login');
    }

    /**
     *
     * Login User Function
     *
     */

    public function login_user(Request $req)
    {
        // 1) Validations
        $validationArray = [
            'user_name' => 'required',
            'password'  => 'required',
        ];
        $customMessages = [
            'user_name.required' => 'User Name is required.',
            'password.required'  => 'User Password is required.',
        ];
        $validator = Validator::make($req->all(), $validationArray, $customMessages);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // 2) Check Authentication
        if (Auth::attempt(['Username' => $req->input('user_name'), 'password' => $req->input('password')])) {
            if (Auth::user()->Roles == 2 && Auth::user()->AccountStatus == 0) {
                if (Auth::user()->account_type == 0) {
                    return back()->withErrors(['Your Account is still in approval proccess. We will let you know when account approval will be completed']);
                    Auth::logout();
                }
                return back()->withErrors(['Your Account is terminated by the Administration. Please Contact Admin to proceed.']);
                Auth::logout();
            }
            $sessionData = [
                'email'     => Auth::user()->EmailAddress,
                'user_name' => Auth::user()->Username,
            ];
            session($sessionData);

            // 3) Check if User is Super Admin or Company
            if (Auth::user()->Roles == 1) {
                return redirect('/admin/dashboard');
            } elseif (Auth::user()->Roles == 2) {
                return redirect('/admin/company/dashboard');
            } elseif (Auth::user()->Roles == 3) {
                return redirect()->route('partner.home');
            }
        } else {
            return back()->withErrors(['Invalid Credentials. Please try again.']);
        }
    }

    /*=====  End of Section Internal User Login Functions  ======*/

    /*================================================================
    =            Section Internal User Register Functions            =
    ================================================================*/
    /**
     *
     * Register Screen Display Function
     *
     */

    public function register_screen(Request $req)
    {
        return view('company_portal.layouts.register');
    }

    /**
     *
     * Register User Function
     *
     */

    public function register_user(Request $req)
    {
        // 1) Validations
        $validationArray = [
            'user_name'    => 'required|max:100|min:7',
            'password'     => 'required|confirmed|min:6',
            'user_email'   => 'required|email',
            'company_name' => 'required',
            'time_zone'    => 'required',
        ];
        $customMessages = [
            'user_name.required'    => 'User Name is required.',
            'user_name.max'         => 'User Name should not be more than 100 characters.',
            'password.required'     => 'User Password is required.',
            'password.confirmed'    => 'Password does not match.',
            'user_email.required'   => 'User Email is required.',
            'user_email.email'      => 'User Email is not properly formated.',
            'company_name.required' => 'Company Name is required',
            'time_zone.required'    => 'Please Select the Time Zone for the System',
            'password.min'          => 'Password should be minimum 6 character long',
            'user_name.min'         => 'Username should be minimum 7 character long',
        ];
        $validator = Validator::make($req->all(), $validationArray, $customMessages);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        /* Check if User Email Already Exist */
        $checkEmailExist = User::where('EmailAddress', $req->user_email)->get()->toArray();
        if (count($checkEmailExist) > 0) {
            return back()->withErrors(['Sorry, the following Email <b><u>' . $req->input('user_email') . '</u></b> is already existed in the Application. Please Contact Administration for further details']);
        }
        /* Check if Company Already Exist */
        $checkExistence = GeneralFunctions::checkDataExist('tenants', ['TenantName'], [$req->input('company_name')]);
        if (count($checkExistence) > 0) {
            return back()->withErrors(['Sorry, the following company <b><u>' . $req->input('company_name') . '</u></b> is already existed in the Application. Please Contact Administration for further details']);
        }

        // 2) Add Tenant (Company Details)
        $tenantData = [
            'TenantName' => $req->input('company_name'),
        ];
        $tenantRecord = Tenants::create($tenantData);
        $tenantId     = $tenantRecord->Id;
        // 3) Add Account Implementaion Data in Array

        $internalUSerData = [
            'Username'         => $req->input('user_name'),
            'password'         => Hash::make($req->input('password')),
            'IsAdmin'          => 1,
            'CreatedBy'        => 'SYSTEM',
            'CreatedIPAddress' => GeneralFunctions::getRealIpAddr(),
            'EmailAddress'     => $req->input('user_email'),
            'Roles'            => 2,
            'AccountStatus'    => 0,
            'TenantId'         => $tenantId,
        ];
        $internalUserRecord = User::create($internalUSerData);
        // 4) Add Tenant System Settings Detail

        $dataTenantSettings = [
            'SettingName' => 'time_zone',
            'ValueData'   => $req->input('time_zone'),
            'TenantId'    => $tenantId,
            'UserId'      => $internalUserRecord->id,
        ];
        TenantSettings::create($dataTenantSettings);

        // 5) Send Account Registration Confirmation to Super Admin
        $data = [
            'subject'         => 'New Registration',
            'heading_details' => 'Welcome <u><b>' . $req->input('company_name') . '</b></u>.',
            'sub_heading'     => 'Congratulations for becoming part of Betting Form Platform',
            'heading'         => 'New Registration',
            'title'           => 'Company <u><b>' . $req->input('company_name') . '</b></u> Registration',
            'content'         => 'Thanks, for Regisetring to our Systme. You will be notify by us as you account is approved by Administration.',
            'email'           => $req->input('user_email'),
        ];
        GeneralFunctions::sendEmail($data);

        return back()->with('success', 'Your Account Details are sent to Administration for approval. We will let you know after confirmation through email you provided about your account status. Once your account is approved we will let you know that you will get free or paid account. Thanks');

        // 3) Payment Gateway Process
        // $generateInvoiceId = 'ACC_Activation_' . uniqid();

        // $data          = [];
        // $data['items'] = [
        //     [
        //         'name'  => 'New Account Registration',
        //         'price' => 9.99,
        //         'qty'   => 1,
        //     ],
        // ];

        // $data['invoice_id']          = $generateInvoiceId;
        // $data['invoice_description'] = "Order # " . $data['invoice_id'] . " Invoice";
        // $data['return_url']          = url('/admin/payment/success');
        // $data['cancel_url']          = url('/admin/payment/refused');

        // $total = 0;
        // foreach ($data['items'] as $item) {
        //     $total += $item['price'] * $item['qty'];
        // }

        // $data['total'] = $total;

        // $options = [
        //     'BRANDNAME'   => 'Betting Form Game Managment',
        //     // 'LOGOIMG' => 'https://example.com/mylogo.png',
        //     'CHANNELTYPE' => 'Merchant',
        // ];

        // $response = $this->paypalObject->addOptions($options)->setExpressCheckout($data);
        // if ($response['ACK'] == 'Success') {
        //     $sessionData = [
        //         'token'         => $response['TOKEN'],
        //         'title'         => 'New Account Registration',
        //         'invoice_id'    => $generateInvoiceId,
        //         'data'          => $data,
        //         'data_to_store' => $internalUSerData,
        //         'company_name'  => $req->input('company_name'),
        //         'password'      => $req->input('password'),
        //     ];

        //     Session::put('payment_detail', $sessionData);
        // }
        // // This will redirect user to PayPal
        // return redirect($response['paypal_link']);
    }

    public function account_activation(Request $req)
    {
        //3) Payment Gateway Process
        $internalUSerData = User::where('id', $req->user_id)->first()->toArray();

        $generateInvoiceId = 'ACC_Activation_' . uniqid();

        $data          = [];
        $data['items'] = [
            [
                'name'  => 'New Account Registration',
                'price' => 9.99,
                'qty'   => 1,
            ],
        ];

        $data['invoice_id']          = $generateInvoiceId;
        $data['invoice_description'] = "Order # " . $data['invoice_id'] . " Invoice";
        $data['return_url']          = url('/admin/payment/success');
        $data['cancel_url']          = url('/admin/payment/refused');

        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $data['total'] = $total;

        $options = [
            'BRANDNAME'   => 'Betting Form Game Managment',
            // 'LOGOIMG' => 'https://example.com/mylogo.png',
            'CHANNELTYPE' => 'Merchant',
        ];

        $response = $this->paypalObject->addOptions($options)->setExpressCheckout($data);
        if ($response['ACK'] == 'Success') {
            $sessionData = [
                'token'         => $response['TOKEN'],
                'title'         => 'New Account Registration',
                'invoice_id'    => $generateInvoiceId,
                'data'          => $data,
                'data_to_store' => $internalUSerData,
            ];

            Session::put('payment_detail', $sessionData);
        }
        // This will redirect user to PayPal
        return redirect($response['paypal_link']);
    }

    public function payment_success(Request $req)
    {
        $getPaymentDetails  = Session::get('payment_detail');
        $internalUserRecord = $getPaymentDetails['data_to_store'];
        $token              = $req->get('token');
        $PayerID            = $req->get('PayerID');
        // Verify Express Checkout Token
        $response = $this->paypalObject->getExpressCheckoutDetails($token);
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            // Perform transaction on PayPal
            $payment_status = $this->paypalObject->doExpressCheckoutPayment($getPaymentDetails['data'], $token, $PayerID);
            if ($payment_status['ACK'] == 'Failure') {
                Session::flash('error', $payment_status['L_SHORTMESSAGE0'] . ' Insufficient amount');
                $redirect_url = '/admin/register/';
                return redirect($redirect_url)->with('error_msg', 'Insufficient amount');
            } else {
                // Update User details for activation.
                User::where('id', $internalUserRecord['id'])->update(['AccountStatus' => 1]);

                // Get Company Name from tenants
                $getCompanyName = Tenants::where('Id', $internalUserRecord['TenantId'])->first();
                // Add Payment Transaction in Table
                $data = [
                    'invoice_title'  => $getPaymentDetails['title'],
                    'user_id'        => $internalUserRecord['id'],
                    'invoice_number' => $getPaymentDetails['invoice_id'],
                    'method'         => 'PAYPAL',
                    'status'         => 1,
                    'amount'         => $payment_status['PAYMENTINFO_0_AMT'],
                    'response_data'  => json_encode($payment_status),
                ];
                TransactionDetails::create($data);
                // Now Forget Session Values
                Session::forget('payment_detail');

                // 1) Send Account Registration Confirmation from System
                $data = [
                    'subject'         => 'New Registration',
                    'heading_details' => 'Welcome <u><b>' . $getCompanyName->TenantName . '</b></u>.',
                    'sub_heading'     => 'Congratulations for becoming part of Betting Form Platform',
                    'heading'         => 'New Registration',
                    'title'           => 'Company <u><b>' . $getCompanyName->TenantName . '</b></u> Registration',
                    'content'         => 'Thanks, for Registring to our Systme. You can now enjoy the Sevices of our Application.',
                    'email'           => $getPaymentDetails['data_to_store']['EmailAddress'],
                ];
                GeneralFunctions::sendEmail($data);

                // 2) Check Authentication
                return redirect('/admin/login')->with('success', 'Congratulations, Your Account is Activated. Please Login to you account');
            }
        }
    }

    public function payment_refused(Request $req)
    {

        $getToken = $req->input('token');
        return redirect('admin/register/')->with('error_msg', 'Your Payment Has Been Refused.');
    }

    /*=====  End of Section Internal User Register Functions  ======*/

    /*==============================================
    =            Section Staff Function            =
    ==============================================*/
    /**
     *
     * Block Show Staff List Function
     *
     */
    public function show_staff_list(Request $req)
    {
        // 1) Get Staff List from the table
        $getStaff       = User::where('TenantId', Auth::user()->TenantId)->where('IsAdmin', 0)->get();
        $getStaff       = $getStaff->toArray();
        $data['title']  = 'Staff List';
        $data['record'] = $getStaff;
        return view('layouts.staff.staff_list', $data);
    }

    /**
     *
     * Block Show Staff Form Function
     *
     */
    public function show_staff_form(Request $req)
    {
        $data['title'] = 'Add Staff';
        return view('layouts.staff.staff_form', $data);
    }

    /*=====  End of Section Staff List  ======*/

    /*===========================================================
    =            Section Logout from Portal Function            =
    ===========================================================*/
    /**
     *
     * Block Logout from Account
     *
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }

    /*=====  End of Section Logout from Portal Function  ======*/

    /*=========================================================
    =            Section Layout for Reset Password           =
    =========================================================*/
    /**
     *
     * Block Reset Password Function
     *
     */
    public function reset_password(Request $req)
    {
        return view('auth.passwords.reset');
    }

    /*=====  End of Section Layout for Reset Password ======*/

    /*====================================
    =            Section for Online Payment for Paid Accounts            =
    ====================================*/
    public function show_payment_screen($id)
    {
        $getInfoFromUser      = User::with('transaction_details')->where('id', $id)->first()->toArray();
        $data['user_details'] = $getInfoFromUser;
        $data['status']       = 1;
        if ($getInfoFromUser['transaction_details'] != null) {
            /**

            TODO:
            - First Create View to Show Already Payment Has cleared for account activation
            - Second also create login button on that blade.

             */
            $data['status'] = 2;

        }

        return view('layouts.payment_page.account_activation_screen', $data);
        /*=====  End of Section for Online Payment for Paid Accounts ======*/

    }

}
