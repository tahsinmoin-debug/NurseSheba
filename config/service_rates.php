<?php

/**
 * NurseSheba Service Rates Configuration (BDT per hour)
 * Base rate varies by service type.
 * Experience bonus: +50 BDT per year of experience (capped at 5 years = +250 BDT).
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Base Hourly Rates by Service Type (BDT)
    |--------------------------------------------------------------------------
    */
    'base_rates' => [
        'General Nursing'  => 500,
        'Post-Surgery Care' => 800,
        'Elderly Care'      => 600,
        'Pediatric Care'    => 650,
        'Wound Dressing'    => 550,
        'Injection/IV'      => 500,
        'Physiotherapy'     => 700,
        'ICU Care'          => 1200,
        'Other'             => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Experience Bonus (BDT per year, capped at max_experience_years)
    |--------------------------------------------------------------------------
    */
    'experience_bonus_per_year' => 50,
    'max_experience_years'      => 5,

    /*
    |--------------------------------------------------------------------------
    | Minimum & Maximum Hourly Rate
    |--------------------------------------------------------------------------
    */
    'min_rate' => 400,
    'max_rate' => 2000,

];
