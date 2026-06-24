<?php
// use App\Models\UserAccess;

use App\Models\UserAccess;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

function send_error($message, $errors = null, $code = 404)
{
    $response = [
        'status' => false,
        'message' => $message,
    ];
    !empty($errors) ? $response['errors'] = $errors : null;

    return response()->json($response, $code);
}

// upload image
function imageUpload($request, $image, $directory, $code)
{
    $doUpload = function ($image) use ($directory, $code) {
        $extention = $image->getClientOriginalExtension();
        $imageName = $code . '_' . uniqId() . '.' . $extention;
        $image->move($directory, $imageName);
        return $directory . '/' . $imageName;
    };
    if (!empty($image) && $request->hasFile($image)) {
        $file = $request->file($image);
        if (is_array($file) && count($file)) {
            $imagesPath = [];
            foreach ($file as $key => $image) {
                $imagesPath[] = $doUpload($image);
            }
            return $imagesPath;
        } else {
            return $doUpload($file);
        }
    }

    return false;
}

// code generate
function invoiceGenerate($model, $prefix = '', $branch_id = null)
{
    $year = date('y');
    $invoice = $year . "00001";
    $modelName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $model)) . 's';
    $clause = "";
    if ($branch_id != null) {
        $clause .= "and branch_id = '$branch_id'";
    }
    $model = DB::select("select * from `$modelName` where invoice like '$prefix$year%' $clause");

    $num_rows = count($model);
    if ($num_rows != 0) {
        $newCode = $num_rows + 1;
        $zeros = ['0', '00', '000', '0000'];
        $invoice = $year . (strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode);
    }
    return $prefix . $invoice;
}
// code generate
function transactionInvoice($model, $prefix = '', $branch_id = null, $type = 'expense')
{
    $year = date('y');
    $invoice = $year . "0001";

    $modelName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $model)) . 's';
    $clause = "";
    if ($branch_id != null) {
        $clause .= "and branch_id = '$branch_id'";
    }
    $model = DB::select("select * from `$modelName` where type = '$type' and invoice like '$prefix$year%' $clause");

    $num_rows = count($model);
    if ($num_rows != 0) {
        $newCode = $num_rows + 1;
        $zeros = ['0', '00', '000'];
        $invoice = $year . (strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode);
    }
    return $prefix . $invoice;
}

// code generate
function generateCode($model, $prefix = '', $branch_id = null)
{
    $code = "00001";
    $modelName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $model)) . 's';
    $clause = "";
    if ($branch_id != null) {
        $clause .= "and branch_id = '$branch_id'";
    }
    $model = DB::select("select * from `$modelName` where 1 = 1 $clause");

    $num_rows = count($model);
    if ($num_rows != 0) {
        $newCode = $num_rows + 1;
        $zeros = ['0', '00', '000', '0000'];
        $code = strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode;
    }
    return $prefix . $code;
}
// code generate
function generateEmpCode($model, $prefix = '', $branch_id = null)
{
    $code = "00001";
    $modelName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $model)) . 's';
    $clause = "";
    if ($branch_id != null) {
        $clause .= "and branch_id = '$branch_id'";
    }
    $model = DB::select("select * from `$modelName` where role='employee' $clause");

    $num_rows = count($model);
    if ($num_rows != 0) {
        $newCode = $num_rows + 1;
        $zeros = ['0', '00', '000', '0000'];
        $code = strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode;
    }
    return $prefix . $code;
}

// make slug
function make_slug($string)
{
    return strtolower(preg_replace('/\s+/u', '-', trim($string)));
}

//credentials check
function credentials($username, $password)
{
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        return ['email' => $username, 'password' => $password];
    } else {
        return ['username' => $username, 'password' => $password];
    }
}

// user access
function checkAccess($accessName)
{
    $status = false;
    $user = Auth::user();
    $userAccess = UserAccess::where('user_id', $user->id)->first();

    if ($user->role == 'Superadmin' || $user->role == 'admin') {
        $status = true;
    } else {
        if (!empty($userAccess) && !empty($userAccess->access)) {
            $access = json_decode(json_decode($userAccess->access, true), true);
            if (is_array($access) && in_array($accessName, $access)) {
                $status = true;
            }
        }
    }

    return $status;
}

// user action
function buttonAction($action)
{
    $status = false;
    if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin') {
        $status = true;
    } else {
        $actionbtn = explode(",", Auth::user()->action);
        if (in_array($action, $actionbtn)) {
            $status = true;
        }
    }

    return $status;
}

// banglamonth
function bangla_number($int)
{
    $engNumber = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0);
    $bangNumber = array('১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০');

    $converted = str_replace($engNumber, $bangNumber, $int);
    return $converted;
}

function dateBangla($timestamp = null)
{
    if ($timestamp === null) {
        $timestamp = time();
    }

    // Bengali months
    $banglaMonths = [
        'বৈশাখ',
        'জ্যৈষ্ঠ',
        'আষাঢ়',
        'শ্রাবণ',
        'ভাদ্র',
        'আশ্বিন',
        'কার্তিক',
        'অগ্রহায়ণ',
        'পৌষ',
        'মাঘ',
        'ফাল্গুন',
        'চৈত্র'
    ];

    // Bengali New Year start dates
    $bengaliNewYearStart = [
        1427 => mktime(0, 0, 0, 4, 14, 2020),
        1428 => mktime(0, 0, 0, 4, 14, 2021),
        1429 => mktime(0, 0, 0, 4, 14, 2022),
        1430 => mktime(0, 0, 0, 4, 14, 2023),
        1431 => mktime(0, 0, 0, 4, 14, 2024),
    ];

    $banglaYear = 1427;
    foreach ($bengaliNewYearStart as $year => $startTimestamp) {
        if ($timestamp >= $startTimestamp) {
            $banglaYear = $year;
        } else {
            break;
        }
    }

    $bengaliYearStart = $bengaliNewYearStart[$banglaYear];
    $dayOfYear = floor(($timestamp - $bengaliYearStart) / (60 * 60 * 24));
    $daysInBanglaMonths = [31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30, 30];
    $banglaMonth = 0;
    $banglaDay = $dayOfYear;

    foreach ($daysInBanglaMonths as $daysInMonth) {
        if ($banglaDay < $daysInMonth) {
            break;
        }
        $banglaDay -= $daysInMonth;
        $banglaMonth++;
    }

    if ($banglaMonth >= count($banglaMonths)) {
        $banglaMonth = count($banglaMonths) - 1;
    }

    $banglaMonthName = $banglaMonths[$banglaMonth];
    $banglaDay += 1;

    return getBanglaDay(date("l", $timestamp)) . ', ' . bangla_number($banglaDay) . ' ' . $banglaMonthName . ' ' . bangla_number($banglaYear);
}

function getBanglaDay($englishDay)
{
    $dayMapping = [
        'Sunday'    => 'রবিবার',
        'Monday'    => 'সোমবার',
        'Tuesday'   => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার',
        'Thursday'  => 'বৃহস্পতিবার',
        'Friday'    => 'শুক্রবার',
        'Saturday'  => 'শনিবার'
    ];
    if (array_key_exists($englishDay, $dayMapping)) {
        return $dayMapping[$englishDay];
    } else {
        return 'Invalid day';
    }
}

function company()
{
    return CompanyProfile::first();
}
