<?php

namespace App\Http\Controllers\API;

use App\Helpers\ExpenseHelper;
use App\Helpers\SubscriptionHelper;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\PaymentMethod;
use App\Model\RepeatTransaction;
use App\Model\Transactions;
use App\Model\User;
use App\Model\UserBudget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function addTransaction(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $customMessages = [
                'repeat_every_after.required_if' => 'The :attribute field is required when repeat is on.',
                'repeat_type.required_if' => 'The :attribute field is required when repeat is on.',

                'payment_method.required_if' => 'The :attribute field is required while adding expense.',

                'budget_amount.required_if' => 'The :attribute field is required when budget is on.',
                'budget_start_date.required_if' => 'The :attribute field is required when budget is on.'
            ];
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                "transaction_type" =>"required|in:1,2",
                "category_id" =>"required",
//                "payment_method" =>"integer",
//                "payment_method" =>"integer|required_if:transaction_type,1",
                "amount" =>"required",
                "date" =>"required|date",
                'picture' => 'image|mimes:jpeg,gif,bmp,png,svg+xml',
                'repeat' => 'required|boolean',
                'repeat_every_after' => 'required_if:repeat,1',
                'repeat_type' => 'required_if:repeat,1|in:1,2,3',
                'repeat_end_date' =>'date',

                'budget' => 'boolean|required_if:transaction_type,1',
                'budget_amount' => 'required_if:budget,1',
                'budget_start_date' => 'required_if:budget,1|date',
                'budget_end_date' =>'date'
            ],$customMessages);

            $validator->after(function ($validator) use ($request) {
                $category_id = $request->input('category_id');
                $user_id = $request->input('user_id');
                $id = $request->input('id');
                if ($request->has('id') && $id!='' && $id != null && $id != 0)
                {
                    $transaction = Transactions::query()->find($id);
                    if (!$transaction) {
                        $validator->errors()->add('id', 'Please pass a valid transaction id to proceed editing.');
                    }
                    else
                    {
                        if ($transaction->user_id !=  $user_id) {
                            $validator->errors()->add('id', 'User can\'t edit another user\'s transaction.');
                        }
                    }
                }
                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
                $transaction_type = $request->input('transaction_type');
                if ($request->has('transaction_type') && $transaction_type != '' && $transaction_type != 0) {
                    $payment_method = $request->input('payment_method');
                    if ($transaction_type==1){
                        $payment_method_details = PaymentMethod::query()->find($payment_method);
                        if (($payment_method_details && ($payment_method_details->user_id!=0 && $payment_method_details->user_id!=$user_id)) )
                        {
                            $validator->errors()->add('user_id', 'Please pass a valid payment method id.');
                        }
                    }
                }
                if ($request->has('category_id') && $category_id != '' && $category_id != 0) {
                    $category = Category::query()->find($category_id);

                    if (!$category) {
                        $validator->errors()->add('category_id', 'Please pass a valid category id.');
                    }
                    else
                    {
                        if ($category->user_id!=0 && $category->user_id!=$user_id)
                        {
                            $validator->errors()->add('category_id', 'This custom category belongs to another user. Please pass a valid category id.');
                        }
                        if ($category->category_type != $request->input('transaction_type'))
                        {
                            $validator->errors()->add('category_id', 'This category belongs to another type of transaction. Please pass a valid category id.');
                        }
                    }
                }

            });

            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $user_id = $request->input('user_id');
                $transaction_type = $request->input('transaction_type');
                $category_id = $request->input('category_id');
                $amount = $request->input('amount');
                $date = $request->input('date');
                $note = $request->input('note');
                $payment_method = $request->input('payment_method');
                if ($transaction_type==2||$payment_method==null ||$payment_method==0 ||  !($request->has('payment_method'))){
                    $payment_method=0;
                }

                $repeat=false;
                if ($request->input('repeat')== 1) {
                    $repeat = true;
                }
                $budget=false;
                if ($request->input('budget')== 1) {
                    $budget = true;
                }

                $id = $request->input('id');

//                if (!($request->has('id')))
//                {
//                    $subscription_status = SubscriptionHelper::getSubscriptionStatus($user_id);
//                    if ($subscription_status==1)
//                    {
//
//                        $response['success'] = true;
//                        $response['status_code'] = '200';
//                        $response['message'] ='Your 30 day free trial has expired, please upgrade!';
//                        $response['data'] = $subscription_status;
//                        return response()->json($response);
//
//                        /****** Check for monthly 10 transactions ******/
//                        /*$now = Carbon::now();
//                        $expired_at = $subscription_details['Expiring_at'];
//                        $query =Transactions::query()->where('user_id','=', $user_id);
//                        $e_m = date('m', strtotime($expired_at));
//                        $e_y = date('Y', strtotime($expired_at));
//
//                        $m = date('m', strtotime($now));
//                        $y = date('Y', strtotime($now));
//
//                        if ($e_m== $m && $e_y== $y)
//                        {
//                            $first_date =$expired_at;
//                            $last_date = date('Y-m-t', strtotime($expired_at));
//                        }
//                        else
//                        {
//                            $first_date =  date('Y-m-01', strtotime($now));
//                            $last_date = date('Y-m-t', strtotime($now));
//
//                        }
//                        $query->where('created_at','>=', $first_date)
//                            ->where('created_at','<=', $last_date);
//
//                        $new_transaction_count = $query->count();
//                        if ($new_transaction_count==10)
//                        {
//                            $response['success'] = true;
//                            $response['status_code'] = '200';
//                            $response['message'] ='You have entered 10 monthly transactions after your 30 day free trial has expired, To enter more please upgrade!';
//                            $response['data'] = SubscriptionHelper::getSubscriptionStatus($user_id);
//                            return response()->json($response);
//                        }*/
//                        /****** Check for monthly 10 transactions ******/
//
//                    }
//                }
                $transaction = Transactions::query()->find($id);
                if (!$transaction){
                    $transaction = new Transactions();
                    $transaction->user_id = $user_id;
                }
                $transaction->transaction_type = $transaction_type;
                $transaction->category_id = $category_id;
                $transaction->amount = $amount;
                $transaction->repeat = $repeat;
                $transaction->date = $date;
                $transaction->note = $note;
                $transaction->payment_method = $payment_method;

                // <======================== Handle Picture ==========================>
                $filename = '';
                $picture = '';
                if ($request->hasFile('picture')) {
                    $picture = $request->file('picture');
                    $file_extension = $picture->getClientOriginalExtension();
                    $filename = 'transaction-picture-' . time() . '.' . $file_extension;

                    $picture->move(base_path('public/uploads/transactions/'), $filename);
                    $image_path = base_path('public/uploads/transactions/' . $filename);
                    if (file_exists($image_path) && is_file($image_path)) {
                        $picture = env('APP_URL') . '/uploads/transactions/' . $filename;
                    }
                }
                // <======================== Handle Picture ==========================>

                if (!($request->has('id'))) {
                    $transaction->picture = $filename;
                }
                if ($repeat ==  true) {
                    if ($request->has('repeat_end_date')
                        && $request->input('repeat_end_date') != ''
                        &&  $request->input('repeat_end_date') != null )
                    {
                        $repeat_end_date = $request->input('repeat_end_date');
                        if ($date > $repeat_end_date)
                        {
                            throw new \Exception('Repeat end date should be greater than start date of transaction.');
                        }
                    }
                }

                $transaction->save();


                if ($repeat ==  true)
                {
                    $repeat_every_after = $request->input('repeat_every_after');
                    $repeat_type = $request->input('repeat_type');

                    if ($request->has('id') && $id!=0 && $id!= '')
                    {
                        $repeat_transaction = RepeatTransaction::query()
                            ->where('transaction_id','=', $id)->where('user_id','=', $user_id)->first();
                        if (!$repeat_transaction) {
                            $repeat_transaction = new RepeatTransaction();
                            $repeat_transaction->user_id = $user_id;
                            $repeat_transaction->transaction_id = $transaction->id;
                        }
                        $repeat_transaction->repeat_every_after = $repeat_every_after;
                        $repeat_transaction->repeat_type = $repeat_type;
                    }
                    else
                    {
                        $repeat_transaction = new RepeatTransaction();
                        $repeat_transaction->user_id = $user_id;
                        $repeat_transaction->transaction_id = $transaction->id;
                        $repeat_transaction->repeat_every_after = $repeat_every_after;
                        $repeat_transaction->repeat_type = $repeat_type;
                    }

                    if ($request->has('repeat_end_date')
                        && $request->input('repeat_end_date') != ''
                        &&  $request->input('repeat_end_date') != null )
                    {
                        $repeat_end_date = $request->input('repeat_end_date');
                        if ($date > $repeat_end_date)
                        {
                            throw new \Exception('Repeat end date should be greater than start date of transaction.');
                        }
                        $repeat_transaction->repeat_end_date = $repeat_end_date;
                    }

                    $repeat_transaction->save();
                }
                else
                {
                    if ($request->has('id') && $id!= 0 && $id!='') {
                        $repeat_transaction = RepeatTransaction::query()
                            ->where('transaction_id', '=', $id)->where('user_id', '=', $user_id)->first();
                        if ($repeat_transaction) {
//                            $repeat_transaction->delete();
                        }
                    }
                }

                //save budget details
                if ($budget ==  true)
                {
                    $budget_amount = $request->input('budget_amount');          // budget amount
                    $budget_start_date = $request->input('budget_start_date');          // budget start date

                    if ($request->has('budget_end_date')
                        && $request->input('budget_end_date') != ''
                        &&  $request->input('budget_end_date') != null )
                    {
                        $budget_end_date = $request->input('budget_end_date');           // budget end date
                        if ($budget_start_date > $budget_end_date)
                        {
                            throw new \Exception('Budget end date should be greater than budget start date of.');
                        }

                        $user_budget = UserBudget::query()->where('user_id','=', $user_id)
                            ->where('category_id','=', $category_id)
                            ->where('start_date','=', $budget_start_date)
                            ->where('end_date','=', $budget_end_date)
                            ->first();
                        if (!$user_budget) {
                            $user_budget = new UserBudget();
                            $user_budget->user_id = $user_id;
                            $user_budget->category_id = $category_id;
                            $user_budget->amount = $budget_amount;
                            $user_budget->start_date = $budget_start_date;
                            $user_budget->end_date = $budget_end_date;
                        }
                        else
                        {
                            $user_budget->amount += $budget_amount;
                        }
                        $user_budget->save();

                    }
                    else
                    {
                        $user_budget = UserBudget::query()->where('user_id','=', $user_id)
                            ->where('category_id','=', $category_id)
                            ->where('start_date','=', $budget_start_date)
                            ->whereNull('end_date')
                            ->first();
                        if (!$user_budget) {
                            $user_budget = new UserBudget();
                            $user_budget->user_id = $user_id;
                            $user_budget->category_id = $category_id;
                            $user_budget->amount = $budget_amount;
                            $user_budget->start_date = $budget_start_date;
                        }
                        else
                        {
                            $user_budget->amount += $budget_amount;
                        }
                        $user_budget->save();
                    }
                }

                $message = 'Transaction has been added successfully.';
                if ($request->has('id') && $id!= 0 && $id!='') {
                    $message = 'Transaction has been updated successfully.';
                }


                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] =$message;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function getDailyExpense(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'filter_date' => 'required|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');

                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
            });


            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {
                $user_id = $request->input('user_id');
                $filter_date = $request->input('filter_date');
                $expenseTransactions = Transactions::query()
                    ->where('user_id' ,'=', $user_id)
                    ->where('transaction_type' ,'=', 1)
                    ->where('date' ,'<=', $filter_date)
                    ->orderBy('category_id','ASC')
                    ->get();

                $totalExpense=0;

                $expenses =[];
                if ($expenseTransactions->count()>0) {
                    foreach ($expenseTransactions as $transaction) {
                        $start_date = $transaction->date;
                        if ($transaction->repeat == 0) {
                            if ($start_date == $filter_date) {

                                $category = Category::query()->find($transaction->category_id);

                                if ($category) {
                                    $totalExpense += round($transaction->amount, 2);

                                    $expense = [
                                        'id' => $category->id,
                                        'category' => $category->name,
                                        'color' => $category->color,
                                        'icon' => $category->icon,
                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                    ];
                                    array_push($expenses, $expense);
                                }
                            }
                        }

                        else {
                            if ($start_date <= $filter_date) {
                                $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                if ($repeat_detail) {
                                    $repeat_every_after = $repeat_detail->repeat_every_after;
                                    if ($repeat_detail->repeat_type==1)// FOR DAYS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($start_date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            if (($interval % $repeat_every_after) == 0) {
                                                $category = Category::query()->find($transaction->category_id);

                                                if ($category) {
                                                    $totalExpense += round($transaction->amount, 2);
                                                    $expense = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                                    ];

                                                    array_push($expenses, $expense);
                                                }
                                            }
                                        }
                                    }
                                    if($repeat_detail->repeat_type==2) // FOR WEEKS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($start_date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            $week_interval = $interval/7;


                                            if (!(is_float($week_interval)) &&  ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                $category = Category::query()->find($transaction->category_id);

                                                if ($category) {
                                                    $totalExpense += round($transaction->amount, 2);
                                                    $expense = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                                    ];

                                                    array_push($expenses, $expense);
                                                }
                                            }
                                        }

                                    }
                                    if($repeat_detail->repeat_type==3) // FOR MONTHS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($start_date);
                                            $datetime2 = date_create($filter_date);
                                            $start_day =$datetime1->format('d');
                                            $filter_day =$datetime2->format('d');
                                            if ($start_day == $filter_day) {

                                                $ts1 = strtotime($datetime1);
                                                $ts2 = strtotime($datetime2);

                                                $ts1 = \DateTime::createFromFormat("Y-m-d", $start_date);
                                                $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                                $year1 =(int)($ts1->format("Y"));
                                                $year2 = (int)($ts2->format("Y"));

                                                $month1 = (int)($ts1->format("m"));
                                                $month2 = (int)($ts2->format("m"));

                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                if (($diff%$repeat_every_after)==0) {
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if ($category) {
                                                        $totalExpense += $transaction->amount;
                                                        $expense = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => number_format($transaction->amount, 2, '.', '')
                                                        ];

                                                        array_push($expenses, $expense);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }


                            }

                        }
                    }
                }

                $incomeTransactions = Transactions::query()
                    ->where('user_id' ,'=', $user_id)
                    ->where('transaction_type' ,'=', 2)
                    ->where('date' ,'<=', $filter_date)
                    ->get();

                $totalIncome=0;
                $incomes =[];
                if ($incomeTransactions->count()>0) {
                    foreach ($incomeTransactions as $transaction) {
                        $start_date = $transaction->date;
                        if ($transaction->repeat == 0) {
                            if ($start_date == $filter_date) {
                                $category = Category::query()->find($transaction->category_id);

                                if($category){
                                    $totalIncome += $transaction->amount;
                                    $income= [
                                        'id' => $category->id,
                                        'category' => $category->name,
                                        'color' => $category->color,
                                        'icon' => $category->icon,
                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                    ];
                                    array_push($incomes, $income);
                                }
                            }
                        } else {
                            if ($start_date <= $filter_date) {
                                $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();
                                if ($repeat_detail) {
                                    $repeat_every_after = $repeat_detail->repeat_every_after;
                                    if ($repeat_detail->repeat_type==1)// FOR DAYS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($start_date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            if (($interval % $repeat_every_after) == 0) {
                                                $category = Category::query()->find($transaction->category_id);

                                                if ($category) {
                                                    $totalIncome += $transaction->amount;
                                                    $income = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                                    ];

                                                    array_push($incomes, $income);
                                                }
                                            }
                                        }
                                    }
                                    if($repeat_detail->repeat_type==2) // FOR WEEKS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($start_date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            $week_interval = $interval/7;

                                            if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                $category = Category::query()->find($transaction->category_id);

                                                if ($category) {
                                                    $totalIncome += $transaction->amount;
                                                    $income = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                                    ];

                                                    array_push($incomes, $income);
                                                }
                                            }
                                        }
                                    }
                                    if($repeat_detail->repeat_type==3) // FOR MONTHS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS

                                        $datetime1 = date_create($start_date);
                                        $datetime2 = date_create($filter_date);
                                        $start_day =$datetime1->format('d');
                                        $filter_day =$datetime2->format('d');
                                        if ($start_day == $filter_day) {

                                            $ts1 = strtotime($datetime1);
                                            $ts2 = strtotime($datetime2);

                                            $ts1 = \DateTime::createFromFormat("Y-m-d", $start_date);
                                            $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                            $year1 =(int)($ts1->format("Y"));
                                            $year2 = (int)($ts2->format("Y"));

                                            $month1 = (int)($ts1->format("m"));
                                            $month2 = (int)($ts2->format("m"));


                                            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                            if (($diff%$repeat_every_after)==0) {

                                                $category = Category::query()->find($transaction->category_id);

                                                if ($category) {
                                                    $totalIncome += $transaction->amount;
                                                    $income = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => number_format($transaction->amount, 2, '.', '')
                                                    ];

                                                    array_push($incomes, $income);
                                                }
                                            }
                                        }
                                    }

                                }
                            }

                        }
                    }
                }
                $carry_over=0;
                $carry_over = ExpenseHelper::carryOverTillDate($user_id, $filter_date);
//
                $arr_rersponse = ExpenseHelper::calculateCarryOver($user_id, $filter_date);

//<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>
                $arr_expenses = [];
                $arr_incomes =[];

                if (count($expenses)>0) {
                    // find all subarray keys (2,3,4,5)
                    foreach ($expenses as $expense) {

                        $ids[] = $expense['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $ids = array_unique($ids);
                    // sum values with same key from $arr and save to $sums
                    foreach ($ids as $id) {
                        foreach ($expenses as $expens) {
                            if ($expens['id'] == $id) {
                                $t_amount += $expens['amount'];
                                $new = array('id' => $id, 'category' => $expens['category'],'color' => $expens['color'],'icon' => $expens['icon'], 'amount' => number_format($t_amount, 2));
                            }
                        }
                        array_push($arr_expenses, $new);
                        $t_amount = 0;
                    }
                }
                if (count($incomes)>0) {
                    // find all subarray keys (2,3,4,5)
                    foreach ($incomes as $income) {

                        $income_ids[] = $income['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $income_ids = array_unique($income_ids);
                    // sum values with same key from $arr and save to $sums
                    foreach ($income_ids as $income_id) {
                        foreach ($incomes as $ele_income) {
                            if ($ele_income['id'] == $income_id) {
                                $t_amount += $ele_income['amount'];
                                $new = array('id' => $income_id, 'category' => $ele_income['category'], 'color' => $ele_income['color'], 'icon' => $ele_income['icon'], 'amount' => number_format($t_amount, 2));
                            }
                        }
                        array_push($arr_incomes, $new);
                        $t_amount = 0;
                    }
                }

//<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>


                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Transactions For '.$filter_date;
                $response['data'] =[
                    'expenses' => $arr_expenses,
                    'incomes' => $arr_incomes,
                    'TotalExpense' =>number_format($totalExpense, 2),
                    'TotalIncome' => number_format($totalIncome, 2),
                    'Balance' => number_format(($totalIncome+$carry_over)-$totalExpense, 2),
                    'BalanceWithOutCarryOver' => number_format(($totalIncome)-$totalExpense, 2),
                    'carry_over' => number_format($carry_over, 2),
                    'total_expense_till_now' => number_format($arr_rersponse['TotalExpense'], 2),
                ];
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function getWeeklyExpense(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'filter_date' => 'required|date',
                "type" =>"required|in:1,2",
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');

                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
            });


            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $type = $request->input('type');

                $value =7;
                $msg ="Weekly";
                if ($type==2)
                {
                    $value =14;
                    $msg ="Bi-Weekly";
                }
                $user_id = $request->input('user_id');
                $filter_date = $request->input('filter_date');
                $start_date = $filter_date;
                $totalExpense=0;
                $budget=0;
                $expenses =[];
                $totalIncome=0;
                $incomes =[];

                $x=1;

                do{

                    if($x>1)
                    {
                        $date = $filter_date;
                        $date1 = str_replace('-', '/', $date);
                        $filter_date = date('Y-m-d',strtotime($date1 . "+1 days"));

                    }
                    $expenseTransactions = Transactions::query()
                        ->where('user_id' ,'=', $user_id)
                        ->where('transaction_type' ,'=', 1)
                        ->where('date' ,'<=', $filter_date)
                        ->get();

                    if ($expenseTransactions->count()>0) {
                        foreach ($expenseTransactions as $transaction) {
                            $start_date = $transaction->date;
                            if ($transaction->repeat == 0) {
                                if ($start_date == $filter_date) {
                                    $category = Category::query()->find($transaction->category_id);

                                    if ($category) {
                                        $totalExpense += round($transaction->amount, 2);

                                        $expense = [
                                            'id' => $category->id,
                                            'category' => $category->name,
                                            'color' => $category->color,
                                            'icon' => $category->icon,
                                            'amount' => round($transaction->amount, 2)
                                        ];

                                        array_push($expenses, $expense);
                                    }
                                }
                            }
//
                            else {
                                if ($start_date <= $filter_date) {
                                    $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                    if ($repeat_detail) {
                                        $repeat_every_after = $repeat_detail->repeat_every_after;
                                        if ($repeat_detail->repeat_type==1)// FOR DAYS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($start_date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                if (($interval % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);
                                                    if ($category) {
                                                        $totalExpense += round($transaction->amount, 2);
                                                        $expense = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount, 2)
                                                        ];

                                                        array_push($expenses, $expense);
                                                    }
                                                }
                                            }
                                        }
                                        if($repeat_detail->repeat_type==2) // FOR WEEKS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($start_date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                $week_interval = $interval/7;


                                                if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                    $category = Category::query()->find($transaction->category_id);
                                                    if($category){
                                                        $totalExpense += round($transaction->amount,2);
                                                        $expense= [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount,2)
                                                        ];

                                                        array_push($expenses, $expense);
                                                    }
                                                }
                                            }

                                        }
                                        if($repeat_detail->repeat_type==3) // FOR MONTHS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {

                                                $datetime1 = date_create($start_date);
                                                $datetime2 = date_create($filter_date);
                                                $start_day =$datetime1->format('d');
                                                $filter_day =$datetime2->format('d');
                                                if ($start_day == $filter_day) {

                                                    $ts1 = strtotime($datetime1);
                                                    $ts2 = strtotime($datetime2);

                                                    $ts1 = \DateTime::createFromFormat("Y-m-d", $start_date);
                                                    $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                                    $year1 =(int)($ts1->format("Y"));
                                                    $year2 = (int)($ts2->format("Y"));

                                                    $month1 = (int)($ts1->format("m"));
                                                    $month2 = (int)($ts2->format("m"));


                                                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                    if (($diff % $repeat_every_after) == 0) {
                                                        $category = Category::query()->find($transaction->category_id);

                                                        if($category){
                                                            $totalExpense += $transaction->amount;
                                                            $expense = [
                                                                'id' => $category->id,
                                                                'category' => $category->name,
                                                                'color' => $category->color,
                                                                'icon' => $category->icon,
                                                                'amount' => round($transaction->amount, 2)
                                                            ];

                                                            array_push($expenses, $expense);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $incomeTransactions = Transactions::query()
                        ->where('user_id' ,'=', $user_id)
                        ->where('transaction_type' ,'=', 2)
                        ->where('date' ,'<=', $filter_date)
                        ->get();

                    if ($incomeTransactions->count()>0) {
                        foreach ($incomeTransactions as $transaction) {
                            $start_date = $transaction->date;
                            if ($transaction->repeat == 0) {
                                if ($start_date == $filter_date) {
                                    $category = Category::query()->find($transaction->category_id);
                                    if ($category) {
                                        $totalIncome += $transaction->amount;
                                        $income = [
                                            'id' => $category->id,
                                            'category' => $category->name,
                                            'color' => $category->color,
                                            'icon' => $category->icon,
                                            'amount' => round($transaction->amount, 2)
                                        ];

                                        array_push($incomes, $income);
                                    }
                                }
                            } else {

                                if ($start_date <= $filter_date) {
                                    $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();
                                    if ($repeat_detail) {
                                        $repeat_every_after = $repeat_detail->repeat_every_after;
                                        if ($repeat_detail->repeat_type==1)// FOR DAYS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($start_date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                if (($interval % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if ($category) {
                                                        $totalIncome += $transaction->amount;
                                                        $income = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount, 2)
                                                        ];

                                                        array_push($incomes, $income);
                                                    }
                                                }
                                            }
                                        }
                                        if($repeat_detail->repeat_type==2) // FOR WEEKS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($start_date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                $week_interval = $interval/7;

                                                if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if($category) {
                                                        $totalIncome += $transaction->amount;
                                                        $income = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount, 2)
                                                        ];

                                                        array_push($incomes, $income);
                                                    }
                                                }
                                            }
                                        }
                                        if($repeat_detail->repeat_type==3) // FOR MONTHS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS

                                            $datetime1 = date_create($start_date);
                                            $datetime2 = $filter_date;
                                            $start_day =$datetime1->format('d');
                                            $filter_day =$datetime2->format('d');
                                            if ($start_day == $filter_day) {

                                                $ts1 = strtotime($datetime1);
                                                $ts2 = strtotime($datetime2);

                                                $ts1 = \DateTime::createFromFormat("Y-m-d", $start_date);
                                                $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                                $year1 =(int)($ts1->format("Y"));
                                                $year2 = (int)($ts2->format("Y"));

                                                $month1 = (int)($ts1->format("m"));
                                                $month2 = (int)($ts2->format("m"));


                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                if (($diff % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if ($category) {
                                                        $totalIncome += $transaction->amount;
                                                        $income = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount, 2)
                                                        ];

                                                        array_push($incomes, $income);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }

                    $x++;
                }

                while($x<=$value);

                $carry_over = ExpenseHelper::carryOverTillDate($user_id, $filter_date);

                $arr_rersponse = ExpenseHelper::calculateCarryOver($user_id, $filter_date);


                //<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>
                $final_expenses =[];
                $final_incomes =[];
                if (count($expenses)>0) {
                    // find all subarray keys (2,3,4,5)
                    foreach ($expenses as $expense) {

                        $ids[] = $expense['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $ids = array_unique($ids);

                    // sum values with same key from $arr and save to $sums
                    foreach ($ids as $id) {
                        foreach ($expenses as $expens) {
                            if ($expens['id'] == $id) {
                                $t_amount += $expens['amount'];
                                $new = array('id' => $id, 'category' => $expens['category'], 'color' => $expens['color'], 'icon' => $expens['icon'], 'amount' => number_format($t_amount,2));
                            }
                        }
                        array_push($final_expenses, $new);
                        $t_amount = 0;
                    }

                }
                if (count($incomes)>0) {
                    // find all subarray keys (2,3,4,5)
                    foreach ($incomes as $income) {

                        $income_ids[] = $income['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $income_ids = array_unique($income_ids);
                    // sum values with same key from $arr and save to $sums
                    foreach ($income_ids as $income_id) {
                        foreach ($incomes as $ele_income) {
                            if ($ele_income['id'] == $income_id) {
                                $t_amount += $ele_income['amount'];
                                $new = array('id' => $income_id, 'category' => $ele_income['category'], 'color' => $ele_income['color'], 'icon' => $ele_income['icon'], 'amount' => number_format($t_amount,2));
                            }
                        }
                        array_push($final_incomes, $new);
                        $t_amount = 0;
                    }
                }

//<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>

                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = $msg.' transactions from '.$start_date.' To '. $filter_date;
                $response['data'] =[
                    'expenses' => $final_expenses,
                    'incomes' => $final_incomes,
                    'TotalIncome' => number_format($totalIncome,2),
                    'TotalExpense' => number_format($totalExpense,2),
                    'Balance' => number_format(($totalIncome+$carry_over)-$totalExpense,2),
                    'BalanceWithOutCarryOver' => number_format(($totalIncome)-$totalExpense,2),
                    'carry_over' =>number_format($carry_over,2),
                    'total_expense_till_now' => number_format($arr_rersponse['TotalExpense'], 2),
                ];
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }


    /*****************
    Fetch transaction
     ******************/
    public function fetchTransaction(Request $request){

        $response = [];
        try{

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required',
            ]);

            $validator->after(function ($validator) use ($request) {
                $transaction_id = $request->input('transaction_id');

                if ($request->has('transaction_id') && $transaction_id != '' && $transaction_id != 0) {
                    $user = Transactions::query()->find($transaction_id);
                    if (!$user) {
                        $validator->errors()->add('transaction_id', 'Please pass a valid transaction id.');
                    }
                }
            });


            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $data = array();
                $transaction_id = $request->input('transaction_id');
                $dataTransactions = Transactions::query()
                    ->where('id' ,'=', $transaction_id)
                    ->first()->toArray();

                $total = round($dataTransactions['amount'],2);
                $payment_method1 = PaymentMethod::query()->find($dataTransactions['payment_method']);
                if(empty($payment_method1)){
                    $payment_method = "Not Found!";
                }else{
                    $payment_method = $payment_method1->icon;
                }

                $transactionType = $dataTransactions['transaction_type'];
                if($transactionType == 1){
                    $transaction_type = "Expense";
                }elseif($transactionType == 2){
                    $transaction_type = "Income";
                }else{
                    $transaction_type = "Not Found!";
                }

                $noteData = $dataTransactions['note'];


                $category = Category::query()->find($dataTransactions['category_id']);

                if(!empty($category)){
                    $cat_parent_id = $category->parent_id;
                    if($cat_parent_id != 0){
                        $parent_cat = Category::query()->find($cat_parent_id);
                        if(!empty($parent_cat)){

                            $data= [
                                'sub_category_id' => $category->id,
                                'sub_category_name' => $category->name,
                                'sub_category_color' => $category->color,
                                'sub_category_icon' => $category->icon,
                                'category_id' => $parent_cat->id,
                                'category_name' => $parent_cat->name,
                                'category_color' => $parent_cat->color,
                                'category_icon' => $parent_cat->icon,
                                'amount' => number_format($dataTransactions['amount'], 2, '.', ''),
                                'date'  => $dataTransactions['date'],
                                'payment_method'  => $payment_method,
                                'transaction_type' => $transaction_type,
                                'note' => $noteData
                            ];

                        }else{
                            $data= [
                                'sub_category_id' => null,
                                'sub_category_name' => null,
                                'sub_category_color' => null,
                                'sub_category_icon' => null,
                                'category_id' => $category->id,
                                'category_name' => $category->name,
                                'category_color' => $category->color,
                                'category_icon' => $category->icon,
                                'amount' => number_format($dataTransactions['amount'], 2, '.', ''),
                                'date'  => $dataTransactions['date'],
                                'payment_method'  => $payment_method,
                                'transaction_type' => $transaction_type,
                                'note' => $noteData
                            ];
                        }
                    }else{
                        $data= [
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                            'category_color' => $category->color,
                            'category_icon' => $category->icon,
                            'amount' => number_format($dataTransactions['amount'], 2, '.', ''),
                            'date'  => $dataTransactions['date'],
                            'payment_method'  => $payment_method,
                            'transaction_type' => $transaction_type,
                            'note' => $noteData
                        ];
                    }
                }else{
                    $data= [
                        'category' => 'Not Found any Category.',
                        'amount' => number_format($dataTransactions['amount'], 2, '.', ''),
                        'date'  => $dataTransactions['date'],
                        'payment_method'  => $payment_method,
                        'transaction_type' => $transaction_type,
                        'note' => $noteData
                    ];
                }

                if($dataTransactions['repeat'] == 1){

                    $repeatdata = array();
                    $repeatData = RepeatTransaction::query()
                        ->where('transaction_id' ,'=', $dataTransactions['id'])
                        ->first()->toArray();
                    if(!empty($repeatData)){

                        if($repeatData['repeat_type'] == 1){
                            $repeat_type = 'Days';
                        }elseif($repeatData['repeat_type'] == 2){
                            $repeat_type = 'Weeks';
                        }elseif($repeatData['repeat_type'] == 3){
                            $repeat_type = 'Months';
                        }else{
                            $repeat_type = 'Not Found!';
                        }
                        $repeatdata= [
                            'repeat_every_after' => $repeatData['repeat_every_after'],
                            'repeat_type' => $repeat_type,
                            'repeat_end_date'  => $repeatData['repeat_end_date']
                        ];


                        $response['success'] = true;
                        $response['status_code'] = '200';
                        $response['data'] =[
                            'transactions' => $data,
                            'repeat_data' => $repeatdata,
                        ];

                    }else{
                        $response['success'] = true;
                        $response['status_code'] = '200';
                        $response['data'] =[
                            'transactions' => $data,
                        ];
                    }

                }else{
                    $response['success'] = true;
                    $response['status_code'] = '200';
                    $response['data'] =[
                        'transactions' => $data,
                    ];
                }

            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }

    }
    /*****fetch transaction ends here*****/

    public function getDateBetweenExpenses(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');
                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }

                if ($request->has('start_date') && $request->has('end_date'))
                {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');

                    if ($start_date > $end_date)
                    {
                        $validator->errors()->add('end_date','End Date should be greater than Start Date.');
                    }
                }

            });

            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $user_id = $request->input('user_id');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                $totalExpense = ExpenseHelper::getTotalTransactions($user_id, $start_date,$end_date,1);
                $totalIncome = ExpenseHelper::getTotalTransactions($user_id, $start_date,$end_date,2);
                $carry_over = ExpenseHelper::carryOverTillDate($user_id, $end_date);

                DB::commit();

                $expenses=$totalExpense['Transactions'];
                $incomes = $totalIncome['Transactions'];


                //<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>
                $final_expenses =[];
                $final_incomes =[];

                if (count($expenses)>0) {

                    foreach ($expenses as $expense) {

                        $ids[] = $expense['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $ids = array_unique($ids);

                    // sum values with same key from $arr and save to $sums
                    foreach ($ids as $id) {
                        foreach ($expenses as $expens) {
                            if ($expens['id'] == $id) {
                                $t_amount += $expens['amount'];
                                $new = array(
                                    'id' => $id,
                                    'category' => $expens['category'],
                                    'color' => $expens['color'],
                                    'icon' => $expens['icon'],
                                    'amount' => number_format($t_amount,2)
                                );
                            }
                        }
                        array_push($final_expenses, $new);
                        $t_amount = 0;
                    }
                }

                if (count($incomes)>0) {
                    // find all subarray keys (2,3,4,5)
                    foreach ($incomes as $income) {

                        $income_ids[] = $income['id'];
                    }
                    $t_amount = 0;
                    // remove duplicate keys
                    $income_ids = array_unique($income_ids);
                    // sum values with same key from $arr and save to $sums
                    foreach ($income_ids as $income_id) {
                        foreach ($incomes as $ele_income) {
                            if ($ele_income['id'] == $income_id) {
                                $t_amount += $ele_income['amount'];
                                $new = array(
                                    'id' => $income_id,
                                    'category' => $ele_income['category'],
                                    'color' => $ele_income['color'],
                                    'icon' => $ele_income['icon'],
                                    'amount' => number_format($t_amount,2)
                                );
                            }
                        }
                        array_push($final_incomes, $new);
                        $t_amount = 0;
                    }
                }

//<<<<<<<<<<<<<<<<==============================================================>>>>>>>>>>>>>>>>>>

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Total Transactions From '. $start_date.' To '. $end_date;
                $response['data'] =[
                    'expenses' =>$final_expenses,
                    'incomes' =>$final_incomes,
                    'TotalExpense' =>number_format($totalExpense['Total'],2),
                    'TotalIncome' => number_format($totalIncome['Total'],2),
                    'Balance' => number_format(($totalIncome['Total'] +$carry_over)- $totalExpense['Total'],2),
                    'BalanceWithOutCarryOver' => number_format(($totalIncome['Total'] )- $totalExpense['Total'],2),
                    'carry_over' => number_format($carry_over,2),
                ];
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    /********************
    Fetch User Transactions
     *********************/

    public function getDailyTransactions(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'filter_date' => 'required|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');

                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
            });


            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {
                $user_id = $request->input('user_id');
                $filter_date = $request->input('filter_date');
                $Alltransactions =[];


                /************************************
                 *      FILTERS FOR TRANSACTIONS    *
                 *      E => Expense, I => Income   *
                 *      R => Repeating, S => Non-Repeating   *
                 ************************************/
                $filter = $request->input('filter');

                $query =Transactions::query()
                    ->where('user_id' ,'=', $user_id)
                    ->where('date' ,'<=', $filter_date);
                $arr_filter = array("E", "I", "R", "S");

                if ($request->has('filter') && (($filter != ' ' && $filter!=0 && $filter != 'A') || in_array($filter, $arr_filter)))
                {
                    if(in_array($filter, $arr_filter)) {
                        echo $filter;
                        if ($filter == 'E') {
                            $query->where('transaction_type', '=', 1);
                        }
                        if ($filter == 'I') {
                            $query->where('transaction_type', '=', 2);
                        }
                        if ($filter == 'R') {
                            $query->where('repeat', '=', 1);
                        }
                        if ($filter == 'S') {
                            $query->where('repeat', '=', 0);
                        }
                    }
                    else
                    {
                        $arr_sub= Category::query()->where('parent_id', $filter)->pluck('id')->toArray();

                        array_push($arr_sub,$filter);
                        $query->whereIn('category_id',$arr_sub);

                    }
                }

                $transactions = $query->get();
                /******************** Filter code ends here ***********************/

                if ($transactions->count()>0) {
                    foreach ($transactions as $transaction) {

                        $transaction_date = $transaction->date;
                        if ($transaction->repeat == 0) {
                            if ($transaction_date == $filter_date) {
                                $category = Category::query()->find($transaction->category_id);

                                if($category) {
                                    if ($category->parent_id!=0)
                                    {
                                        $parent_category = Category::query()->find($category->parent_id);
                                        if ($parent_category)
                                        {
                                            $sub_category = $category;
                                            $category = $parent_category;
                                        }
                                    }

                                    $arr_category = array(
                                        'category'=> array(
                                            'id'=> isset($category)?$category->id:null,
                                            'name' => isset($category)?$category->name:null,
                                            'color' => isset($category)?$category->color:null,
                                            'icon' => isset($category)?$category->icon:null
                                        ),
                                        'sub_category'=> array(
                                            'id'=> isset($sub_category)?$sub_category->id:null,
                                            'name' => isset($sub_category)?$sub_category->name:null,
                                            'color' => isset($sub_category)?$sub_category->color:null,
                                            'icon' => isset($sub_category)?$sub_category->icon:null
                                        ),
                                    );
                                    $arr_paymentMethod=[];

                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                    $arr_paymentMethod['name'] = 'Not Found';
                                    if ($payment_method)
                                    {
                                        $arr_paymentMethod['name'] = $payment_method->name;
                                        $arr_paymentMethod['color'] = $payment_method->color;
                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                    }
                                    $transactionType = $transaction['transaction_type'];
                                    $transaction_type = "Not Found!";

                                    if($transactionType == 1){
                                        $transaction_type = "Expense";
                                    }elseif($transactionType == 2){
                                        $transaction_type = "Income";
                                    }

                                    $arr_transaction = [
                                        'id' => $transaction->id,
                                        'type' => $transaction_type,
                                        'category' =>$arr_category,
                                        'amount' => round($transaction->amount, 2),
                                        'date' => $transaction_date,
                                        'payment_method' => $arr_paymentMethod,
                                    ];

                                    array_push($Alltransactions, $arr_transaction);
                                }
                            }
                        }
//
                        else {
                            if ($transaction_date <= $filter_date) {

                                if ($transaction->date < $filter_date)
                                {
                                    $transaction_date = $filter_date;
                                }

                                $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                if ($repeat_detail) {
                                    $repeat_every_after = $repeat_detail->repeat_every_after;
                                    if ($repeat_detail->repeat_type==1)// FOR DAYS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            if (($interval % $repeat_every_after) == 0) {
                                                $category = Category::query()->find($transaction->category_id);

                                                if($category) {
                                                    if ($category->parent_id!=0)
                                                    {
                                                        $parent_category = Category::query()->find($category->parent_id);
                                                        if ($parent_category)
                                                        {
                                                            $sub_category = $category;
                                                            $category = $parent_category;
                                                        }
                                                    }

                                                    $arr_category = array(
                                                        'category'=> array(
                                                            'id'=> isset($category)?$category->id:null,
                                                            'name' => isset($category)?$category->name:null,
                                                            'color' => isset($category)?$category->color:null,
                                                            'icon' => isset($category)?$category->icon:null
                                                        ),
                                                        'sub_category'=> array(
                                                            'id'=> isset($sub_category)?$sub_category->id:null,
                                                            'name' => isset($sub_category)?$sub_category->name:null,
                                                            'color' => isset($sub_category)?$sub_category->color:null,
                                                            'icon' => isset($sub_category)?$sub_category->icon:null
                                                        ),
                                                    );
                                                    $arr_paymentMethod=[];

                                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                    $arr_paymentMethod['name'] = 'Not Found';
                                                    if ($payment_method)
                                                    {
                                                        $arr_paymentMethod['name'] = $payment_method->name;
                                                        $arr_paymentMethod['color'] = $payment_method->color;
                                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                                    }
                                                    $transactionType = $transaction['transaction_type'];
                                                    $transaction_type = "Not Found!";

                                                    if($transactionType == 1){
                                                        $transaction_type = "Expense";
                                                    }elseif($transactionType == 2){
                                                        $transaction_type = "Income";
                                                    }

                                                    $arr_transaction = [
                                                        'id' => $transaction->id,
                                                        'type' => $transaction_type,
                                                        'category' =>$arr_category,
                                                        'amount' => round($transaction->amount, 2),
                                                        'date' => $transaction_date,
                                                        'payment_method' => $arr_paymentMethod,
                                                    ];

                                                    array_push($Alltransactions, $arr_transaction);
                                                }
                                            }
                                        }
                                    }
                                    if($repeat_detail->repeat_type==2) // FOR WEEKS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($filter_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            $week_interval = $interval/7;


                                            if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                $category = Category::query()->find($transaction->category_id);


                                                if($category) {
                                                    if ($category->parent_id!=0)
                                                    {
                                                        $parent_category = Category::query()->find($category->parent_id);
                                                        if ($parent_category)
                                                        {
                                                            $sub_category = $category;
                                                            $category = $parent_category;
                                                        }
                                                    }

                                                    $arr_category = array(
                                                        'category'=> array(
                                                            'id'=> isset($category)?$category->id:null,
                                                            'name' => isset($category)?$category->name:null,
                                                            'color' => isset($category)?$category->color:null,
                                                            'icon' => isset($category)?$category->icon:null
                                                        ),
                                                        'sub_category'=> array(
                                                            'id'=> isset($sub_category)?$sub_category->id:null,
                                                            'name' => isset($sub_category)?$sub_category->name:null,
                                                            'color' => isset($sub_category)?$sub_category->color:null,
                                                            'icon' => isset($sub_category)?$sub_category->icon:null
                                                        ),
                                                    );
                                                    $arr_paymentMethod=[];

                                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                    $arr_paymentMethod['name'] = 'Not Found';
                                                    if ($payment_method)
                                                    {
                                                        $arr_paymentMethod['name'] = $payment_method->name;
                                                        $arr_paymentMethod['color'] = $payment_method->color;
                                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                                    }
                                                    $transactionType = $transaction['transaction_type'];
                                                    $transaction_type = "Not Found!";

                                                    if($transactionType == 1){
                                                        $transaction_type = "Expense";
                                                    }elseif($transactionType == 2){
                                                        $transaction_type = "Income";
                                                    }

                                                    $arr_transaction = [
                                                        'id' => $transaction->id,
                                                        'type' => $transaction_type,
                                                        'category' =>$arr_category,
                                                        'amount' => round($transaction->amount, 2),
                                                        'date' => $transaction_date,
                                                        'payment_method' => $arr_paymentMethod,
                                                    ];

                                                    array_push($Alltransactions, $arr_transaction);
                                                }
                                            }
                                        }

                                    }
                                    if($repeat_detail->repeat_type==3) // FOR MONTHS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS
                                        if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($filter_date);
                                            $start_day =$datetime1->format('d');
                                            $filter_day =$datetime2->format('d');

                                            if ($start_day == $filter_day) {

                                                $ts1 = strtotime($datetime1);
                                                $ts2 = strtotime($datetime2);

                                                $ts1 = \DateTime::createFromFormat("Y-m-d", $transaction->date);
                                                $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                                $year1 =(int)($ts1->format("Y"));
                                                $year2 = (int)($ts2->format("Y"));

                                                $month1 = (int)($ts1->format("m"));
                                                $month2 = (int)($ts2->format("m"));


                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                if (($diff % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);


                                                    if($category) {
                                                        if ($category->parent_id!=0)
                                                        {
                                                            $parent_category = Category::query()->find($category->parent_id);
                                                            if ($parent_category)
                                                            {
                                                                $sub_category = $category;
                                                                $category = $parent_category;
                                                            }
                                                        }

                                                        $arr_category = array(
                                                            'category'=> array(
                                                                'id'=> isset($category)?$category->id:null,
                                                                'name' => isset($category)?$category->name:null,
                                                                'color' => isset($category)?$category->color:null,
                                                                'icon' => isset($category)?$category->icon:null
                                                            ),
                                                            'sub_category'=> array(
                                                                'id'=> isset($sub_category)?$sub_category->id:null,
                                                                'name' => isset($sub_category)?$sub_category->name:null,
                                                                'color' => isset($sub_category)?$sub_category->color:null,
                                                                'icon' => isset($sub_category)?$sub_category->icon:null
                                                            ),
                                                        );
                                                        $arr_paymentMethod=[];

                                                        $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                        $arr_paymentMethod['name'] = 'Not Found';
                                                        if ($payment_method)
                                                        {
                                                            $arr_paymentMethod['name'] = $payment_method->name;
                                                            $arr_paymentMethod['color'] = $payment_method->color;
                                                            $arr_paymentMethod['icon'] = $payment_method->icon;

                                                        }
                                                        $transactionType = $transaction['transaction_type'];
                                                        $transaction_type = "Not Found!";

                                                        if($transactionType == 1){
                                                            $transaction_type = "Expense";
                                                        }elseif($transactionType == 2){
                                                            $transaction_type = "Income";
                                                        }

                                                        $arr_transaction = [
                                                            'id' => $transaction->id,
                                                            'type' => $transaction_type,
                                                            'category' =>$arr_category,
                                                            'amount' => round($transaction->amount, 2),
                                                            'date' => $transaction_date,
                                                            'payment_method' => $arr_paymentMethod,
                                                        ];
                                                        array_push($Alltransactions, $arr_transaction);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Transactions For '.$filter_date;
                $response['data'] =$Alltransactions;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function getWeeklyTransactions(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'filter_date' => 'required|date',
                "type" =>"required|in:1,2",
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');

                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
            });


            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $type = $request->input('type');

                $value =7;
                $msg ="Weekly";
                if ($type==2)
                {
                    $value =14;
                    $msg ="Bi-Weekly";
                }
                $user_id = $request->input('user_id');
                $filter_date = $request->input('filter_date');
                $start_date = $filter_date;
                $Alltransactions =[];

                $x=1;

                do{

                    if($x>1)
                    {
                        $date = $filter_date;
                        $date1 = str_replace('-', '/', $date);
                        $filter_date = date('Y-m-d',strtotime($date1 . "+1 days"));

                    }

                    /************************************
                     *      FILTERS FOR TRANSACTIONS    *
                     *      E => Expense, I => Income   *
                     *      R => Repeating, S => Non-Repeating   *
                     ************************************/
                    $filter = $request->input('filter');

                    $query =Transactions::query()
                        ->where('user_id' ,'=', $user_id)
                        ->where('date' ,'<=', $filter_date);
                    $arr_filter = array("E", "I", "R", "S");

                    if ($request->has('filter') && (($filter != ' ' && $filter!=0 && $filter != 'A') || in_array($filter, $arr_filter)))
                    {
                        if(in_array($filter, $arr_filter)) {
                            if ($filter == 'E') {
                                $query->where('transaction_type', '=', 1);
                            }
                            if ($filter == 'I') {
                                $query->where('transaction_type', '=', 2);
                            }
                            if ($filter == 'R') {
                                $query->where('repeat', '=', 1);
                            }
                            if ($filter == 'S') {
                                $query->where('repeat', '=', 0);
                            }
                        }
                        else
                        {
                            $arr_sub= Category::query()->where('parent_id', $filter)->pluck('id')->toArray();
                            array_push($arr_sub,$filter);
                            $query->whereIn('category_id',$arr_sub);
                        }
                    }

                    $transactions = $query->get();
                    /******************** Filter code ends here ***********************/

                    if ($transactions->count()>0) {
                        foreach ($transactions as $transaction) {

                            $transaction_date = $transaction->date;
                            if ($transaction->repeat == 0) {
                                if ($transaction->date == $filter_date) {
                                    $category = Category::query()->find($transaction->category_id);

                                    if($category) {
                                        if ($category->parent_id!=0)
                                        {
                                            $parent_category = Category::query()->find($category->parent_id);
                                            if ($parent_category)
                                            {
                                                $sub_category = $category;
                                                $category = $parent_category;
                                            }
                                        }

                                        $arr_category = array(
                                            'category'=> array(
                                                'id'=> isset($category)?$category->id:null,
                                                'name' => isset($category)?$category->name:null,
                                                'color' => isset($category)?$category->color:null,
                                                'icon' => isset($category)?$category->icon:null
                                            ),
                                            'sub_category'=> array(
                                                'id'=> isset($sub_category)?$sub_category->id:null,
                                                'name' => isset($sub_category)?$sub_category->name:null,
                                                'color' => isset($sub_category)?$sub_category->color:null,
                                                'icon' => isset($sub_category)?$sub_category->icon:null
                                            ),
                                        );
                                        $arr_paymentMethod=[];

                                        $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                        $arr_paymentMethod['name'] = 'Not Found';
                                        if ($payment_method)
                                        {
                                            $arr_paymentMethod['name'] = $payment_method->name;
                                            $arr_paymentMethod['color'] = $payment_method->color;
                                            $arr_paymentMethod['icon'] = $payment_method->icon;

                                        }
                                        $transactionType = $transaction['transaction_type'];
                                        $transaction_type = "Not Found!";

                                        if($transactionType == 1){
                                            $transaction_type = "Expense";
                                        }elseif($transactionType == 2){
                                            $transaction_type = "Income";
                                        }

                                        $arr_transaction = [
                                            'id' => $transaction->id,
                                            'type' => $transaction_type,
                                            'category' =>$arr_category,
                                            'amount' => round($transaction->amount, 2),
                                            'date' => $transaction->date,
                                            'payment_method' => $arr_paymentMethod,
                                        ];

                                        array_push($Alltransactions, $arr_transaction);
                                    }
                                }
                            }
//
                            else {

                                if ($transaction->date <= $filter_date) {
                                    if ($transaction->date < $filter_date)
                                    {
                                        $transaction_date = $filter_date;
                                    }
                                    $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                    if ($repeat_detail) {
                                        $repeat_every_after = $repeat_detail->repeat_every_after;
                                        if ($repeat_detail->repeat_type==1)// FOR DAYS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($transaction->date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                if (($interval % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);


                                                    if($category) {
                                                        if ($category->parent_id!=0)
                                                        {
                                                            $parent_category = Category::query()->find($category->parent_id);
                                                            if ($parent_category)
                                                            {
                                                                $sub_category = $category;
                                                                $category = $parent_category;
                                                            }
                                                        }

                                                        $arr_category = array(
                                                            'category'=> array(
                                                                'id'=> isset($category)?$category->id:null,
                                                                'name' => isset($category)?$category->name:null,
                                                                'color' => isset($category)?$category->color:null,
                                                                'icon' => isset($category)?$category->icon:null
                                                            ),
                                                            'sub_category'=> array(
                                                                'id'=> isset($sub_category)?$sub_category->id:null,
                                                                'name' => isset($sub_category)?$sub_category->name:null,
                                                                'color' => isset($sub_category)?$sub_category->color:null,
                                                                'icon' => isset($sub_category)?$sub_category->icon:null
                                                            ),
                                                        );
                                                        $arr_paymentMethod=[];

                                                        $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                        $arr_paymentMethod['name'] = 'Not Found';
                                                        if ($payment_method)
                                                        {
                                                            $arr_paymentMethod['name'] = $payment_method->name;
                                                            $arr_paymentMethod['color'] = $payment_method->color;
                                                            $arr_paymentMethod['icon'] = $payment_method->icon;

                                                        }
                                                        $transactionType = $transaction['transaction_type'];
                                                        $transaction_type = "Not Found!";

                                                        if($transactionType == 1){
                                                            $transaction_type = "Expense";
                                                        }elseif($transactionType == 2){
                                                            $transaction_type = "Income";
                                                        }

                                                        $arr_transaction = [
                                                            'id' => $transaction->id,
                                                            'type' => $transaction_type,
                                                            'category' =>$arr_category,
                                                            'amount' => round($transaction->amount, 2),
                                                            'date' => $transaction_date,
                                                            'payment_method' => $arr_paymentMethod,
                                                        ];

                                                        array_push($Alltransactions, $arr_transaction);
                                                    }
                                                }
                                            }
                                        }
                                        if($repeat_detail->repeat_type==2) // FOR WEEKS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($transaction->date);
                                                $datetime2 = date_create($filter_date);

                                                $interval = date_diff($datetime1, $datetime2);
                                                $interval = $interval->days;
                                                $week_interval = $interval/7;


                                                if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                    $category = Category::query()->find($transaction->category_id);


                                                    if($category) {
                                                        if ($category->parent_id!=0)
                                                        {
                                                            $parent_category = Category::query()->find($category->parent_id);
                                                            if ($parent_category)
                                                            {
                                                                $sub_category = $category;
                                                                $category = $parent_category;
                                                            }
                                                        }

                                                        $arr_category = array(
                                                            'category'=> array(
                                                                'id'=> isset($category)?$category->id:null,
                                                                'name' => isset($category)?$category->name:null,
                                                                'color' => isset($category)?$category->color:null,
                                                                'icon' => isset($category)?$category->icon:null
                                                            ),
                                                            'sub_category'=> array(
                                                                'id'=> isset($sub_category)?$sub_category->id:null,
                                                                'name' => isset($sub_category)?$sub_category->name:null,
                                                                'color' => isset($sub_category)?$sub_category->color:null,
                                                                'icon' => isset($sub_category)?$sub_category->icon:null
                                                            ),
                                                        );
                                                        $arr_paymentMethod=[];

                                                        $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                        $arr_paymentMethod['name'] = 'Not Found';
                                                        if ($payment_method)
                                                        {
                                                            $arr_paymentMethod['name'] = $payment_method->name;
                                                            $arr_paymentMethod['color'] = $payment_method->color;
                                                            $arr_paymentMethod['icon'] = $payment_method->icon;

                                                        }
                                                        $transactionType = $transaction['transaction_type'];
                                                        $transaction_type = "Not Found!";

                                                        if($transactionType == 1){
                                                            $transaction_type = "Expense";
                                                        }elseif($transactionType == 2){
                                                            $transaction_type = "Income";
                                                        }

                                                        $arr_transaction = [
                                                            'id' => $transaction->id,
                                                            'type' => $transaction_type,
                                                            'category' =>$arr_category,
                                                            'amount' => round($transaction->amount, 2),
                                                            'date' => $transaction_date,
                                                            'payment_method' => $arr_paymentMethod,
                                                        ];





                                                        array_push($Alltransactions, $arr_transaction);
                                                    }
                                                }
                                            }

                                        }
                                        if($repeat_detail->repeat_type==3) // FOR MONTHS
                                        {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS
                                            if ($repeat_detail->repeat_end_date == null || $filter_date < $repeat_detail->repeat_end_date) {
                                                $datetime1 = date_create($transaction->date);
                                                $datetime2 = date_create($filter_date);
                                                $start_day =$datetime1->format('d');
                                                $filter_day =$datetime2->format('d');

                                                if ($start_day == $filter_day) {

//                                                    $ts1 = strtotime($datetime1);
//                                                    $ts2 = strtotime($datetime2);

                                                    $ts1 = \DateTime::createFromFormat("Y-m-d", $transaction->date);
                                                    $ts2 = \DateTime::createFromFormat("Y-m-d", $filter_date);

                                                    $year1 =(int)($ts1->format("Y"));
                                                    $year2 = (int)($ts2->format("Y"));

                                                    $month1 = (int)($ts1->format("m"));
                                                    $month2 = (int)($ts2->format("m"));


                                                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                    if (($diff % $repeat_every_after) == 0) {
                                                        $category = Category::query()->find($transaction->category_id);


                                                        if($category) {
                                                            if ($category->parent_id!=0)
                                                            {
                                                                $parent_category = Category::query()->find($category->parent_id);
                                                                if ($parent_category)
                                                                {
                                                                    $sub_category = $category;
                                                                    $category = $parent_category;
                                                                }
                                                            }

                                                            $arr_category = array(
                                                                'category'=> array(
                                                                    'id'=> isset($category)?$category->id:null,
                                                                    'name' => isset($category)?$category->name:null,
                                                                    'color' => isset($category)?$category->color:null,
                                                                    'icon' => isset($category)?$category->icon:null
                                                                ),
                                                                'sub_category'=> array(
                                                                    'id'=> isset($sub_category)?$sub_category->id:null,
                                                                    'name' => isset($sub_category)?$sub_category->name:null,
                                                                    'color' => isset($sub_category)?$sub_category->color:null,
                                                                    'icon' => isset($sub_category)?$sub_category->icon:null
                                                                ),
                                                            );
                                                            $arr_paymentMethod=[];

                                                            $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                            $arr_paymentMethod['name'] = 'Not Found';
                                                            if ($payment_method)
                                                            {
                                                                $arr_paymentMethod['name'] = $payment_method->name;
                                                                $arr_paymentMethod['color'] = $payment_method->color;
                                                                $arr_paymentMethod['icon'] = $payment_method->icon;

                                                            }
                                                            $transactionType = $transaction['transaction_type'];
                                                            $transaction_type = "Not Found!";

                                                            if($transactionType == 1){
                                                                $transaction_type = "Expense";
                                                            }elseif($transactionType == 2){
                                                                $transaction_type = "Income";
                                                            }

                                                            $arr_transaction = [
                                                                'id' => $transaction->id,
                                                                'type' => $transaction_type,
                                                                'category' =>$arr_category,
                                                                'amount' => round($transaction->amount, 2),
                                                                'date' => $transaction_date,
                                                                'payment_method' => $arr_paymentMethod,
                                                            ];

                                                            array_push($Alltransactions, $arr_transaction);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $x++;
                }

                while($x<=$value);

                DB::commit();
                asort($Alltransactions);
                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = $msg.' transactions from '.$start_date.' To '. $filter_date;
                $response['data'] = $Alltransactions;

            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function getDateBetweenTransactions(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');
                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
                if ($request->has('start_date') && $request->has('end_date'))
                {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');

                    if ($start_date > $end_date)
                    {
                        $validator->errors()->add('end_date','End Date should be greater than Start Date.');
                    }
                }
            });

            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $user_id = $request->input('user_id');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                $filter = $request->input('filter');

                if (!($request->has('filter')))
                {
                    $filter='';
                }
                $All_transactions = TransactionHelper::getTransactionsBetweenDates($user_id, $start_date,$end_date, isset($filter)?$filter:'');

                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Total Transactions From '. $start_date.' To '. $end_date;
                $response['data'] =$All_transactions;

            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    /********************
    Delete User Transaction
     *********************/
    public function deleteTransaction(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'id' => 'required',
                'date' => 'required|date',
                'type' =>'required|in:1,2,3',
            ]);

            $validator->after(function ($validator) use ($request) {
                $user_id = $request->input('user_id');
                $id = $request->input('id');

                if ($request->has('user_id') && $user_id != '' && $user_id != 0) {
                    $user = User::query()->find($user_id);
                    if (!$user) {
                        $validator->errors()->add('user_id', 'Please pass a valid user id.');
                    }
                }
                if ($request->has('id') && $id != '' && $id != 0) {
                    $transaction = Transactions::query()->find($id);
                    if (!$transaction) {
                        $validator->errors()->add('id', 'Please pass a valid transaction id.');
                    }
                    else
                    {
                        if ($transaction->user_id != $user_id) {
                            $validator->errors()->add('id', 'Please pass a valid transaction id added by this user only.');
                        }
                    }
                }
            });

            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {

                $id = $request->input('id');
                $user_id = $request->input('user_id');
                $type = $request->input('type');
                $date = $request->input('date');
                $transaction = Transactions::query()->find($id);

                if (!$transaction)
                {
                    throw new \Exception('Transaction not found.');
                }
                $picture = $transaction->picture;


                if ($type==1) {

                    $msg = 'Transaction has been deleted successfully.';
                    /**** Delete All Occurances ****/
                    if ($transaction->repeat == 1) {

                        $msg = 'This transaction\'s all occurrences has been deleted.';
                        $repeat_details = RepeatTransaction::query()
                            ->where('transaction_id', '=', $id)
                            ->where('user_id', '=', $user_id)->first();
                        if ($repeat_details) {
                            $repeat_details->delete();
                        }
                    }

                    $transaction->delete();

                    if($picture!='')
                    {
                        $old_image = base_path('public/uploads/transactions/' . $picture);
                        if (is_file($old_image) && file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }
                }
                elseif($type==2)
                {
                    /**** Delete Just this One ****/
                    if ($transaction->repeat == 1) {

                        $msg = 'This transaction occurrence has been deleted successfully.';

                        $repeat_details = RepeatTransaction::query()
                            ->where('transaction_id', '=', $id)
                            ->where('user_id', '=', $user_id)->first();
                        if ($repeat_details) {


                            if ($repeat_details->repeat_type==1)
                            {
                                $r_type=$repeat_details->repeat_every_after.' days';
                            }elseif ($repeat_details->repeat_type==2)
                            {
                                $r_type=$repeat_details->repeat_every_after.' weeks';
                            }elseif ($repeat_details->repeat_type==3)
                            {
                                $r_type=$repeat_details->repeat_every_after.' months';
                            }else
                            {
                                throw new \Exception('Error while fetching details.');
                            }


                            $date1 = $date;
                            $date2 = str_replace('-', '/', $date1);
                            $next_date = date('Y-m-d', strtotime($date2 . "+".$r_type));

                            $p_date1 = $date;
                            $p_date2 = str_replace('-', '/', $p_date1);
                            $prev_date = date('Y-m-d', strtotime($p_date2 . "-".$r_type));

                            if ($repeat_details->repeat_end_date!='' || $repeat_details->repeat_end_date!=null) {
                                $n_end_date = $repeat_details->repeat_end_date;
                            }else
                            {
                                $n_end_date='';
                            }

                            if ($prev_date<= $transaction->date)
                            {
                                /****** If deleting very first occurrence *******/
                                $transaction->date = $next_date;
                                $transaction->save();
                            }


                            if ($next_date >= $repeat_details->repeat_end_date)
                            {
                                /****** If deleting very last occurrence *******/
                                $repeat_details->repeat_end_date = $prev_date;
                                $repeat_details->save();
                            }

                            if ($prev_date> $transaction->date && $next_date< $repeat_details->repeat_end_date) {

                                /****** If deleting any occurrence falling in middle *******/
                                $repeat_details->repeat_end_date = $prev_date;
                                $repeat_details->save();

                                $n_transaction = $transaction->replicate();
                                $n_transaction->date = $next_date;
                                $n_transaction->save();

                                $n_repeat_detail = $repeat_details->replicate();
                                $n_repeat_detail->transaction_id = $n_transaction->id;
                                $n_repeat_detail->repeat_end_date = $n_end_date;
                                $n_repeat_detail->save();
                            }
                        }
                    }


                }
                elseif($type==3)
                {
                    /**** Delete All future one ****/

                    if ($transaction->repeat == 1) {
                        $msg = 'This transaction\'s all future occurrences has been deleted.';

                        $repeat_details = RepeatTransaction::query()
                            ->where('transaction_id', '=', $id)
                            ->where('user_id', '=', $user_id)->first();
                        if ($repeat_details) {
                            $repeat_details->repeat_end_date= $date;
                            $repeat_details->save();
                        }
                    }
                }
                else
                {
                    throw new \Exception('Invalid delete type attempt.');
                }




                DB::commit();

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = $msg;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }


    //<=============================== DUMYY API'S ===============================>
    public function getTotal()
    {
        return ExpenseHelper::getTotalTransactions(1, '2019-12-11','2019-12-31',2);
    }
    public function getCarryOver()
    {
        return ExpenseHelper::calculateCarryOver(1, '2019-09-05');


    }
}
