<?php

namespace App\Services;

use App\User;
use DateTime;
use App\Expense;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ExpenseService {
    /**
     * Handle generating weekly date per month
     *
     * @param [type] $month_year
     */
    function getWeekRangesOfMonthStartingMonday($month_year) {
        $month_date = explode('-', $month_year);
        $year = $month_date[0];
        $month = $month_date[1];

        // Create a DateTime object for the first day and the last day of the given month
        $startOfMonth = new DateTime("{$year}-{$month}-01");
        $endOfMonth = new DateTime("{$year}-{$month}-01");
        $endOfMonth->modify('last day of this month');

        // Move the start to the first Sunday on or after the 1st day of the month
        if ($startOfMonth->format('w') != 0) { // Sunday = 0 in PHP's 'w' format
            $startOfMonth->modify('last Sunday');
        }

        $weekRanges = [];

        // Loop through the month and calculate each week
        while ($startOfMonth <= $endOfMonth) {
            $weekStart = clone $startOfMonth;
            $weekEnd = clone $startOfMonth;
            $weekEnd->modify('next Saturday');

            // Ensure the start date is not before the 1st of the month
            if ($weekStart < new DateTime("{$year}-{$month}-01")) {
                $weekStart = new DateTime("{$year}-{$month}-01");
            }

            // Ensure the end date is not after the last day of the month
            if ($weekEnd > $endOfMonth) {
                $weekEnd = clone $endOfMonth;
            }

            // Add the week range to the result
            $weekRanges[] = [
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d')
            ];

            // Move to the next Sunday
            $startOfMonth->modify('next Sunday');
        }

        return $weekRanges;
    }

    public function getWeeklyVerifiedDetails() {
        // $weekly_expense = User::where('id')->with([

        // ]);
    }

    public function computeVerifiedAndRejected($expenses, $is_10th_day = false)
    {
        $total_expense_amount = 0;
        $verified_amount = 0;
        $unverified_amount = 0;
        $rejected_amount = 0;

        foreach ($expenses as $expense) {
            $is_credited = ($is_10th_day && in_array($expense->status_id,[3,4])) ? true : false;

            if ($expense->verified_status_id == 1) {
                $verified_amount = $verified_amount + $expense->amount;
            }

            if ($expense->verified_status_id == 3) {
                // compute rejected with remarks no.4
                if ($expense->expense_rejected_reason_id == 4) {
                    if($is_credited) $rejected_amount = $rejected_amount + $expense->rejected_deducted_amount;

                    //Add remaining amount to approved amount after deduction
                    $verified_amount = $verified_amount + ($expense->amount - $expense->rejected_deducted_amount);
                } else {
                    if($is_credited) $rejected_amount = $rejected_amount + $expense->amount;
                }
            }

            $unverified_statuses = [0,2];
            if(in_array($expense->verified_status_id, $unverified_statuses)) {
                if($is_credited) $unverified_amount = $unverified_amount + $expense->amount;
            }

            $total_expense_amount = $total_expense_amount + $expense->amount;
        }

        return [
            'total_expense_amount' => $total_expense_amount,
            'verified_amount' => $verified_amount,
            'unverified_amount' => $unverified_amount,
            'rejected_amount' => $rejected_amount
        ];
    }

    public static function sendSingleWebexNotif($email, $cardItems, $accessToken = false) {
        try {
            $httpClient = new Client();
            $webexBotAPI = 'https://api.ciscospark.com/v1/messages';
            $accessToken = env('WEBEX_NOTIF_ACCESS_TOKEN');

            $webexCard = [
                array(
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" =>  [
                        "type" => "AdaptiveCard",
                        "body" => [
                            [
                                "type" => "ColumnSet",
                                "columns" => [
                                    [
                                        "type" => "Column",
                                        "width" => 2,
                                        "items" => $cardItems
                                    ]
                                ]
                            ]
                        ],
                        '$schema' => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.2"
                    ]
                )
            ];

            $body = [
                'toPersonEmail' => $email,
                "text" => "SALESFORCE APP",
                "attachments" =>  $webexCard
            ];

            $httpClient->post(
                $webexBotAPI,
                [
                    RequestOptions::BODY => json_encode($body),
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer $accessToken"
                    ]
                ]
            );
            return true;
        } catch (\Throwable $th) {
            // Error::create(['message' => $th->getMessage(),'model' => 'App\DocumentInbuond','user_id' => Auth::user()->id]);
            return false;
        }
    }


}