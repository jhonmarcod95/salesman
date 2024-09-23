<?php

namespace App\Services;

use DateTime;

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
}