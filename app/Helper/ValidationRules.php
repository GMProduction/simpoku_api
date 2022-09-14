<?php


namespace App\Helper;


use Illuminate\Validation\Rule;

class ValidationRules
{
    const EVENT_CREATE_RULE = [
        'specialist' => 'required|exists:specialists,id',
        'title' => 'required',
        'description' => 'required',
        'image' => 'image',
        'start_at' => 'required',
        'finish_at' => 'required',
        'location' => 'required',
        'latitude' => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,15})?$/',
        'longitude' => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,15})?$/',
        'announcement' => 'file'
    ];

//    const EVENT_CREATE_MESSAGES = [
//        'specialist.re'
//    ];

    const SLIDER_CREATE_RULE = [
        'image' => 'required|image',
        'url' => 'required|url'
    ];

    const SLIDER_PATCH_RULE = [
        'image' => 'image',
        'url' => 'required|url'
    ];
}
