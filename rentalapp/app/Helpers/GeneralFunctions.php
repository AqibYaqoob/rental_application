<?php
namespace App\Helpers;

use App\Currency;
use App\PartnerSetting;
use App\Properties;
use App\TenantSettings;
use App\User;
use App\UserPackages;
use Auth;
use Carbon\Carbon;
use DateTime;
use DB;
use FCM;
use Illuminate\Support\Facades\Crypt;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use Mail;

class GeneralFunctions
{
    /**
     *
     * Block : Ajax Debug data
     *
     */
    public static function ajax_debug($data)
    {
        echo '<pre>';
        print_r($data);
        die();
    }

    /**
     *
     * Block : Get IP ADDRESS of Current User
     *
     */
    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     *
     * Block : Check if Data in Table Already Exist
     *
     */
    public static function checkDataExist($table_name, $columns_array, $data_array, $update_column_status = null, $record_id = null)
    {
        $checkExistingRecord = DB::table($table_name);
        foreach ($columns_array as $key => $value) {
            $checkExistingRecord->where($value, $data_array[$key]);
        }
        if ($update_column_status != null) {
            $checkExistingRecord->where('id', '!=', $record_id);
        }
        $result = $checkExistingRecord->get();
        return $result->toArray();
    }

    /**
     *
     * Block : Custom Email Function to Send Emails
     *
     */
    public static function sendEmail($data)
    {
        Mail::send('email.custome_email_template', $data, function ($message) use ($data) {
            $message->from(env('COMPANY_EMAIL_ADDRESS'), 'Rental Application RVC');
            $message->to($data['email']);
            $message->subject($data['subject']);
        });
    }

    /**
     *
     * Block : Get Company Identity
     *
     */
    public static function getCompanyAuth()
    {
        $userData = User::where('TenantId', Auth::user()->TenantId)->where('IsAdmin', 1)->first();
        return $userData->id;
    }

    /**
     *
     * Block : Serializing error Message
     *
     */
    public static function error_msg_serialize($errorList)
    {
        $errorData = $errorList;
        $errorData = $errorData->toArray();
        $errors    = [];
        $i         = 0;
        foreach ($errorData as $key => $value) {
            $errors[$i] = $value[0];
            $i++;
        }

        return $errors;
    }

    /* Permissions Working */
    public static function check_view_permission($code)
    {
        return GeneralFunctions::permissions($code, 4);
    }

    public static function check_edit_permission($code)
    {
        return GeneralFunctions::permissions($code, 2);
    }

    public static function check_add_permission($code)
    {
        return GeneralFunctions::permissions($code, 1);
    }

    public static function check_delete_permission($code)
    {
        return GeneralFunctions::permissions($code, 3);
    }

    public static function permissions($code, $permission)
    {
        if (Auth::user()->IsAdmin != 1) {
            $getRoleData         = DB::table('staff_contact_details')->where('user_id', Auth::user()->id)->first();
            $getScreenPermission = DB::table('screens_details')->where('code', $code)->first();
            if (!$getScreenPermission) {
                return false;
            }
            $screenId = $getScreenPermission->id;
            $roleId   = $getRoleData->role_id;

            $data = DB::table('permission_role')->where('screen_id', $screenId)->where('role_id', $roleId)->where('permission_id', $permission)->get();
            if (count($data->toArray()) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     *
     * Block : Encrypt Strings for protections
     *
     */
    public static function encryptString($data)
    {
        $encrypted = Crypt::encryptString($data);
        return $encrypted;
    }

    /**
     *
     * Block : Converting Decimal Value into Percentage
     *
     */
    public static function toPercentage($value)
    {
        $value = $value * 100;
        return $value;
    }

    /**
     *
     * Block : Convert String Date into Database DateTime
     *
     */
    public static function convertToDateTime($stringDate)
    {
        $timestamp = strtotime($stringDate);
        return date("Y-m-d H:i:s", $timestamp);
    }

    /**
     *
     * Block : Convert Date to String for Date Picker
     *
     */
    public static function convertToDateTimeToString($date)
    {
        if (!($date instanceof Carbon)) {
            if (is_numeric($date)) {
                // Assume Timestamp
                $date = Carbon::createFromTimestamp($date);
            } else {
                $date = Carbon::parse($date);
            }
        }
        $getTimeZone = TenantSettings::where('TenantId', Auth::user()->TenantId)->first();
        if ($getTimeZone) {
            return $date->setTimezone($getTimeZone->ValueData)->format('jS F Y g:ia');
        } else {
            return $date->setTimezone('UTC')->format('jS F Y g:ia');
        }
    }

    /**
     *
     * Block : Get Root User Of Company
     *
     */
    public static function adminUserId()
    {
        $getUserId = User::where('TenantId', Auth::user()->TenantId)->where('IsAdmin', 1)->first();
        return $getUserId->id;
    }

    /**
     *
     * Block : Get Currency Conversion Between Two Accounts
     *
     */
    public static function convertAmount($sourceId, $targetId, $amount, $transferType)
    {
        $funds = 0.0;
        if ($transferType == 1) {
            // Tenant Account Process
            $getTargetBaseCurrency = PartnerSetting::with('currency.currencyList')->where('partner_id', $targetId)->first();
            if ($getTargetBaseCurrency) {
                // Calculation Process => Convert Transaction Amount of Tenant to Partner Currency
                $funds = $amount * $getTargetBaseCurrency->currency->CurrentRate;
            }
            return ['converted_funds' => $funds, 'partners_currency' => $getTargetBaseCurrency->currency->currencyList->currency, 'currency_rate' => $getTargetBaseCurrency->currency->CurrentRate, 'partners_currency_id' => $getTargetBaseCurrency->currency->Id];
        } elseif ($transferType == 2) {
            // Partner Account Process
            $getTargetBaseCurrency = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->with('currencyList')->first();
            // GeneralFunctions::ajax_debug($getTargetBaseCurrency);
            return ['tenant_currency' => $getTargetBaseCurrency->currencyList->currency, 'currency_rate' => $getTargetBaseCurrency->CurrentRate, 'tenant_currency_id' => $getTargetBaseCurrency->Id];
        } else {
            // Partner to Partner Account Process
            $getTargetBaseCurrency = PartnerSetting::with('currency.currencyList')->where('partner_id', $targetId)->first();
            if ($getTargetBaseCurrency) {
                // Calculation Process => Convert Transaction Amount of Partner to Base Partner Currency
                $getTenantBaseCurrency = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->with('currencyList')->first();
                $funds                 = ($amount / $getTenantBaseCurrency->CurrentRate) * $getTargetBaseCurrency->currency->CurrentRate;
            }
            return ['converted_funds' => $funds, 'partners_currency' => $getTargetBaseCurrency->currency->currencyList->currency, 'currency_rate' => $getTargetBaseCurrency->currency->CurrentRate, 'partners_currency_id' => $getTargetBaseCurrency->currency->Id];
        }
    }

    /**
     *
     * Block : Get Account Total Balance Info
     *
     */
    public static function getBalance(array $record, $accountHolderCurrency)
    {
        $totalBalance = 0.0;
        if (count($record) > 0) {
            foreach ($record as $key => $value) {
                /*----------  Calculation of Balance from different Currency Saved in Accounts Table  ----------*/
                if ($value['currency']['Id'] != $accountHolderCurrency->Id) {
                    // Step 1) Convert the Amount into Base Currency of Tenant Account
                    $convertedAmountToBaseCurrency = $value['Amount'] / $value['Current_Rate'];
                    $totalBalance                  = $totalBalance + ($convertedAmountToBaseCurrency * $accountHolderCurrency->CurrentRate);
                } else {
                    $totalBalance = $totalBalance + $value['Amount'];
                }
            }
        }
        return $totalBalance;
    }

    /**
     *
     * Block : Get Database Date to show in Date Picker
     *
     */
    public static function convertDBdateIntoDatepicker($date)
    {
        if (!($date instanceof Carbon)) {
            if (is_numeric($date)) {
                // Assume Timestamp
                $date = Carbon::createFromTimestamp($date);
            } else {
                $date = Carbon::parse($date);
            }
        }
        $getTimeZone = TenantSettings::where('TenantId', Auth::user()->TenantId)->first();
        if ($getTimeZone) {
            return $date->setTimezone($getTimeZone->ValueData)->format('d/m/Y');
        } else {
            return $date->setTimezone('UTC')->format('d/m/Y');
        }
    }

    /**
     *
     * Block : Upload Files
     *
     */
    public static function uploadFile($file)
    {
        $destinationPath = 'uploads';
        // Expload and Add Time Stamp
        $fileName              = explode('.', $file->getClientOriginalName());
        $fileNameWithTimeStamp = $fileName[0] . '_' . time() . '.' . $fileName[1];
        $file->move($destinationPath, $fileNameWithTimeStamp);

        $record = ['file_name' => $fileName[0], 'file_extention' => $file->getClientOriginalExtension(), 'url' => '/uploads/' . $fileNameWithTimeStamp];
        return $record;
    }
    /**
     *
     * Block : Upload Files using Base 64
     *
     */
    public static function uploadFileUsingBase64($fileData)
    {
        $image      = base64_decode($fileData);
        $image_name = 'file' . '_' . time() . '.png';
        $path       = public_path() . "/uploads/" . $image_name;
        file_put_contents($path, $image);
        $record = ['file_name' => $image_name, 'file_extention' => 'png', 'url' => '/uploads/' . $image_name];
        return $record;
    }

    /**
     *
     * Block: Convert unix timestamp into human readable format
     *
     */
    public static function convertUnixToReadableFormat($val)
    {

// Grab the milliseconds as the initial value mod 1000
        $milliseconds = $val % 1000;

// Divide by 1000 to obtain the actual timestamp
        $ts = intval($val / 1000);

// Parse into a DateTime object
        $date = DateTime::createFromFormat('U', $ts);

// Formatted output
        return $date->format('D, d M Y H:i:s');
    }
    /**
     *
     * Block for Push Notification
     *
     */
    public static function pushNotification($deviceId, $title, $data, $bodyHead)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        // $notificationBuilder = new PayloadNotificationBuilder($title);
        // $notificationBuilder->setBody($bodyHead)
        //     ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['text' => $bodyHead, 'title' => $title, 'line1' => 'Testing', 'line2' => 'Testing']);

        $option = $optionBuilder->build();
        // $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        $tokens = $deviceId;

        $downstreamResponse = FCM::sendTo($tokens, $option, null, $data);

        return $downstreamResponse->numberSuccess();
    }

    /**
     *
     * Check if Properties are not more than the given Package
     *
     */
    public static function checkPackagePropertyRange($user_id)
    {
        // 1) Check the count of Total Properties that are added.
        $totalPropertiesAdded = Properties::select('id')->where('user_id', $user_id)->get()->toArray();
        $totalPropertiesAdded = count($totalPropertiesAdded);
        // 2) Get Package Details
        $packageDetails = UserPackages::where('user_id', $user_id)->with('package_detail')->first();
        if ($packageDetails) {
            $packageDetails = $packageDetails->toArray();
        }
        $packagePropertyRange = $packageDetails['package_detail']['properties_range'];
        if ($totalPropertiesAdded >= $packagePropertyRange) {
            return false;
        }

        return true;
    }

}
