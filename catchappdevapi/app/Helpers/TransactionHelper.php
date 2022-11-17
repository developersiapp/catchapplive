<?php


namespace App\Helpers;


use App\Model\Category;
use App\Model\PaymentMethod;
use App\Model\RepeatTransaction;
use App\Model\Transactions;

class TransactionHelper
{
    public static function  getTransactionsBetweenDates($user_id,  $end_date,$start_date,$filter)
    {
        $datetime1 = date_create($start_date);
        $datetime2 = date_create($end_date);

        $interval = date_diff($datetime1, $datetime2);
        $value = ($interval->days)+1;

        $Alltransactions = [];
        if ($end_date>$start_date ) {
            $value = '-' . $value;
        }
        $x = 1;

        if ($value < 0) {
            $Alltransactions=[];
        } else {
            do {
                if ($x > 1) {
                    $date = $start_date;
                    $date1 = str_replace('-', '/', $date);
                    $start_date = date('Y-m-d', strtotime($date1 . "-1 days"));
                }
                echo $start_date;

                /************************************
                 *      FILTERS FOR TRANSACTIONS    *
                 *      E => Expense, I => Income   *
                 *      R => Repeating, S => Non-Repeating   *
                 ************************************/

                $query =Transactions::query()
                    ->where('user_id', '=', $user_id)
                    ->where('date', '<=', $start_date);
                $arr_filter = array("E", "I", "R", "S");

                if ((($filter != ' ' && $filter!=0) || in_array($filter, $arr_filter)))
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

                if ($transactions->count() > 0) {
                    foreach ($transactions as $transaction) {

                        $transaction_date = $transaction->date;
                        if ($transaction->repeat == 0) {
                            if ($transaction->date == $start_date) {
                                $category = Category::query()->find($transaction->category_id);

                                if ($category) {
                                    if ($category->parent_id != 0) {
                                        $parent_category = Category::query()->find($category->parent_id);
                                        if ($parent_category) {
                                            $sub_category = $category;
                                            $category = $parent_category;
                                        }
                                    }

                                    $arr_category = array(
                                        'category' => array(
                                            'id' => isset($category) ? $category->id : null,
                                            'name' => isset($category) ? $category->name : null,
                                            'color' => isset($category) ? $category->color : null,
                                            'icon' => isset($category) ? $category->icon : null
                                        ),
                                        'sub_category' => array(
                                            'id' => isset($sub_category) ? $sub_category->id : null,
                                            'name' => isset($sub_category) ? $sub_category->name : null,
                                            'color' => isset($sub_category) ? $sub_category->color : null,
                                            'icon' => isset($sub_category) ? $sub_category->icon : null
                                        ),
                                    );
                                    $arr_paymentMethod = [];

                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                    $arr_paymentMethod['name'] = 'Not Found';
                                    if ($payment_method) {
                                        $arr_paymentMethod['name'] = $payment_method->name;
                                        $arr_paymentMethod['color'] = $payment_method->color;
                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                    }
                                    $transactionType = $transaction['transaction_type'];
                                    $transaction_type = "Not Found!";

                                    if ($transactionType == 1) {
                                        $transaction_type = "Expense";
                                    } elseif ($transactionType == 2) {
                                        $transaction_type = "Income";
                                    }

                                    $arr_transaction = [
                                        'id' => $transaction->id,
                                        'type' => $transaction_type,
                                        'category' => $arr_category,
                                        'amount' => round($transaction->amount, 2),
                                        'date' => $transaction->date,
                                        'payment_method' => $arr_paymentMethod,
                                    ];


                                    array_push($Alltransactions, $arr_transaction);
                                }
                            }
                        } //
                        else {
                            if ($transaction_date <= $start_date) {
                                if ($transaction->date< $start_date)
                                {
                                    $transaction_date = $start_date;
                                }


                                $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                if ($repeat_detail) {
                                    $repeat_every_after = $repeat_detail->repeat_every_after;
                                    if ($repeat_detail->repeat_type == 1)// FOR DAYS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N DAYS
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($start_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            if (($interval % $repeat_every_after) == 0) {
                                                $category = Category::query()->find($transaction->category_id);
                                                if ($category) {

                                                    if ($category->parent_id != 0) {
                                                        $parent_category = Category::query()->find($category->parent_id);
                                                        if ($parent_category) {
                                                            $sub_category = $category;
                                                            $category = $parent_category;
                                                        }
                                                    }

                                                    $arr_category = array(
                                                        'category' => array(
                                                            'id' => isset($category) ? $category->id : null,
                                                            'name' => isset($category) ? $category->name : null,
                                                            'color' => isset($category) ? $category->color : null,
                                                            'icon' => isset($category) ? $category->icon : null
                                                        ),
                                                        'sub_category' => array(
                                                            'id' => isset($sub_category) ? $sub_category->id : null,
                                                            'name' => isset($sub_category) ? $sub_category->name : null,
                                                            'color' => isset($sub_category) ? $sub_category->color : null,
                                                            'icon' => isset($sub_category) ? $sub_category->icon : null
                                                        ),
                                                    );
                                                    $arr_paymentMethod = [];

                                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                    $arr_paymentMethod['name'] = 'Not Found';
                                                    if ($payment_method) {
                                                        $arr_paymentMethod['name'] = $payment_method->name;
                                                        $arr_paymentMethod['color'] = $payment_method->color;
                                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                                    }
                                                    $transactionType = $transaction['transaction_type'];
                                                    $transaction_type = "Not Found!";

                                                    if ($transactionType == 1) {
                                                        $transaction_type = "Expense";
                                                    } elseif ($transactionType == 2) {
                                                        $transaction_type = "Income";
                                                    }

                                                    $arr_transaction = [
                                                        'id' => $transaction->id,
                                                        'type' => $transaction_type,
                                                        'category' => $arr_category,
                                                        'amount' => round($transaction->amount, 2),
                                                        'date' => $transaction_date,
                                                        'payment_method' => $arr_paymentMethod,
                                                    ];

                                                    array_push($Alltransactions, $arr_transaction);
                                                }
                                            }
                                        }
                                    }
                                    if ($repeat_detail->repeat_type == 2) // FOR WEEKS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($start_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            $week_interval = $interval / 7;


                                            if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval >= 7) {
                                                $category = Category::query()->find($transaction->category_id);


                                                if ($category) {
                                                                                                     if ($category->parent_id != 0) {
                                                        $parent_category = Category::query()->find($category->parent_id);
                                                        if ($parent_category) {
                                                            $sub_category = $category;
                                                            $category = $parent_category;
                                                        }
                                                    }

                                                    $arr_category = array(
                                                        'category' => array(
                                                            'id' => isset($category) ? $category->id : null,
                                                            'name' => isset($category) ? $category->name : null,
                                                            'color' => isset($category) ? $category->color : null,
                                                            'icon' => isset($category) ? $category->icon : null
                                                        ),
                                                        'sub_category' => array(
                                                            'id' => isset($sub_category) ? $sub_category->id : null,
                                                            'name' => isset($sub_category) ? $sub_category->name : null,
                                                            'color' => isset($sub_category) ? $sub_category->color : null,
                                                            'icon' => isset($sub_category) ? $sub_category->icon : null
                                                        ),
                                                    );
                                                    $arr_paymentMethod = [];

                                                    $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                    $arr_paymentMethod['name'] = 'Not Found';
                                                    if ($payment_method) {
                                                        $arr_paymentMethod['name'] = $payment_method->name;
                                                        $arr_paymentMethod['color'] = $payment_method->color;
                                                        $arr_paymentMethod['icon'] = $payment_method->icon;

                                                    }
                                                    $transactionType = $transaction['transaction_type'];
                                                    $transaction_type = "Not Found!";

                                                    if ($transactionType == 1) {
                                                        $transaction_type = "Expense";
                                                    } elseif ($transactionType == 2) {
                                                        $transaction_type = "Income";
                                                    }

                                                    $arr_transaction = [
                                                        'id' => $transaction->id,
                                                        'type' => $transaction_type,
                                                        'category' => $arr_category,
                                                        'amount' => round($transaction->amount, 2),
                                                        'date' => $transaction_date,
                                                        'payment_method' => $arr_paymentMethod,
                                                    ];


                                                    array_push($Alltransactions, $arr_transaction);
                                                }
                                            }
                                        }

                                    }
                                    if ($repeat_detail->repeat_type == 3) // FOR MONTHS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N MONTHS
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {

                                            $datetime1 = date_create($transaction->date);
                                            $datetime2 = date_create($start_date);
                                            $start_day = $datetime1->format('d');
                                            $filter_day = $datetime2->format('d');

                                            if ($start_day == $filter_day) {
                                                $ts1 = \DateTime::createFromFormat("Y-m-d", $transaction->date);
                                                $ts2 = \DateTime::createFromFormat("Y-m-d", $start_date);

                                                $year1 =(int)($ts1->format("Y"));
                                                $year2 = (int)($ts2->format("Y"));

                                                $month1 = (int)($ts1->format("m"));
                                                $month2 = (int)($ts2->format("m"));


                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

                                                if (($diff % $repeat_every_after) == 0) {
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if ($category) {

                                                        if ($category->parent_id != 0) {
                                                            $parent_category = Category::query()->find($category->parent_id);
                                                            if ($parent_category) {
                                                                $sub_category = $category;
                                                                $category = $parent_category;
                                                            }
                                                        }

                                                        $arr_category = array(
                                                            'category' => array(
                                                                'id' => isset($category) ? $category->id : null,
                                                                'name' => isset($category) ? $category->name : null,
                                                                'color' => isset($category) ? $category->color : null,
                                                                'icon' => isset($category) ? $category->icon : null
                                                            ),
                                                            'sub_category' => array(
                                                                'id' => isset($sub_category) ? $sub_category->id : null,
                                                                'name' => isset($sub_category) ? $sub_category->name : null,
                                                                'color' => isset($sub_category) ? $sub_category->color : null,
                                                                'icon' => isset($sub_category) ? $sub_category->icon : null
                                                            ),
                                                        );
                                                        $arr_paymentMethod = [];

                                                        $payment_method = PaymentMethod::query()->find($transaction->payment_method);

                                                        $arr_paymentMethod['name'] = 'Not Found';
                                                        if ($payment_method) {
                                                            $arr_paymentMethod['name'] = $payment_method->name;
                                                            $arr_paymentMethod['color'] = $payment_method->color;
                                                            $arr_paymentMethod['icon'] = $payment_method->icon;

                                                        }
                                                        $transactionType = $transaction['transaction_type'];
                                                        $transaction_type = "Not Found!";

                                                        if ($transactionType == 1) {
                                                            $transaction_type = "Expense";
                                                        } elseif ($transactionType == 2) {
                                                            $transaction_type = "Income";
                                                        }

                                                        $arr_transaction = [
                                                            'id' => $transaction->id,
                                                            'type' => $transaction_type,
                                                            'category' => $arr_category,
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
            while ($x <= $value);
        }

        return $Alltransactions;
    }



    public static function  getTotalTransactions($user_id, $start_date, $end_date, $type_val)
    {
        $datetime1 = date_create($start_date);
        $datetime2 = date_create($end_date);

        $interval = date_diff($datetime1, $datetime2);
        $value = $interval->days;


        if ($start_date > $end_date)
        {
            $value= '-'.$value;
        }
        $total = 0;
        $transactions = [];
        $x = 1;
        $type = Transactions::$transaction_type{$type_val};
        if ($value<0) {
            $arr_transaction = ['Type' => $type, 'Total' => round($total, 2), 'Transactions' => $transactions];
        }else {
            do {
                if ($x > 1) {
                    $date = $start_date;
                    $date1 = str_replace('-', '/', $date);
                    $start_date = date('Y-m-d', strtotime($date1 . "+1 days"));
                }
                $transactionTransactions = Transactions::query()
                    ->where('user_id', '=', $user_id)
                    ->where('transaction_type', '=', $type_val)
                    ->where('date', '<=', $start_date)
                    ->get();

                if ($transactionTransactions->count() > 0) {
                    foreach ($transactionTransactions as $transaction) {
                        $transacion_date = $transaction->date;
                        if ($transaction->repeat == 0) {
                            if ($transacion_date == $start_date) {
                                $category = Category::query()->find($transaction->category_id);
                                if ($category) {
                                    $total += round($transaction->amount, 2);

                                    $transaction = [
                                        'id' => $category->id,
                                        'category' => $category->name,
                                        'color' => $category->color,
                                        'icon' => $category->icon,
                                        'amount' => round($transaction->amount, 2),
                                        'type' => 'single'
                                    ];

                                    array_push($transactions, $transaction);
                                }
                            }
                        } //
                        else {
                            if ($transacion_date <= $start_date) {
                                $repeat_detail = RepeatTransaction::query()->where('transaction_id', '=', $transaction->id)->first();

                                if ($repeat_detail) {
                                    $repeat_every_after = $repeat_detail->repeat_every_after;
                                    if ($repeat_detail->repeat_type == 1)// FOR DAYS
                                    {
                                        /******************* CALCULATE FOR REPEAT AFTER N DAYS ***********************/
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transacion_date);
                                            $datetime2 = date_create($start_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            if (($interval % $repeat_every_after) == 0) {
                                                $category = Category::query()->find($transaction->category_id);
                                                if ($category) {
                                                    $total += round($transaction->amount, 2);
                                                    $transaction = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => round($transaction->amount, 2),
                                                        'type' => 'repeating'
                                                    ];

                                                    array_push($transactions, $transaction);
                                                }
                                            }
                                        }
                                    }
                                    if ($repeat_detail->repeat_type == 2) // FOR WEEKS
                                    {
//                                        CALCULATE FOR REPEAT AFTER N WEEKS
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transacion_date);
                                            $datetime2 = date_create($start_date);

                                            $interval = date_diff($datetime1, $datetime2);
                                            $interval = $interval->days;
                                            $week_interval = $interval / 7;


                                            if (!(is_float($week_interval)) && ($week_interval % $repeat_every_after) == 0 && $interval>=7) {
                                                $category = Category::query()->find($transaction->category_id);
                                                if ($category) {
                                                    $total += round($transaction->amount, 2);
                                                    $transaction = [
                                                        'id' => $category->id,
                                                        'category' => $category->name,
                                                        'color' => $category->color,
                                                        'icon' => $category->icon,
                                                        'amount' => round($transaction->amount, 2),
                                                        'type' => 'repeating'
                                                    ];

                                                    array_push($transactions, $transaction);
                                                }
                                            }
                                        }

                                    }
                                    if ($repeat_detail->repeat_type == 3) // FOR MONTHS
                                    {
                                        /***** CALCULATE FOR REPEAT AFTER N MONTHS*****/
                                        if ($repeat_detail->repeat_end_date == null || $start_date < $repeat_detail->repeat_end_date) {
                                            $datetime1 = date_create($transacion_date);
                                            $datetime2 = date_create($start_date);
                                            $start_day = $datetime1->format('d');
                                            $filter_day = $datetime2->format('d');
                                            if ($start_day == $filter_day) {
                                                $ts1 = \DateTime::createFromFormat("Y-m-d", $transacion_date);
                                                $ts2 = \DateTime::createFromFormat("Y-m-d", $start_date);

                                                $year1 =(int)($ts1->format("Y"));
                                                $year2 = (int)($ts2->format("Y"));

                                                $month1 = (int)($ts1->format("m"));
                                                $month2 = (int)($ts2->format("m"));

                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                if (($diff%$repeat_every_after)==0) {
                                                    ////////////////////////////////////////////////
                                                    $category = Category::query()->find($transaction->category_id);

                                                    if ($category) {
                                                        $total += $transaction->amount;
                                                        $transaction = [
                                                            'id' => $category->id,
                                                            'category' => $category->name,
                                                            'color' => $category->color,
                                                            'icon' => $category->icon,
                                                            'amount' => round($transaction->amount, 2),
                                                            'type' => 'repeating'
                                                        ];

                                                        array_push($transactions, $transaction);
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
            } while ($x <= $value);
            $arr_transaction = ['Type' => $type, 'Total' => round($total, 2), 'Transactions' => $transactions];
        }

        return $arr_transaction;
    }
}