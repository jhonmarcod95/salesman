<?php


function get_google_map_coordinates($url_str){
    $status = 'E';

    $map_string = $url_str;
    $map_strings = explode('/', $map_string);

    $coordinates = [];
    foreach ($map_strings as $map_string){
        if (strpos($map_string, '@') !== false) {
            $map_string = str_replace('@', '', $map_string); // remove @ sign
            $map_string = explode(',', $map_string); // 0 => lat, 1 => lng, 2 => zoom
            $coordinates = $map_string;
            $status = 'S';
            break;
        }
    }

    return [
        'status' => $status,
        'coordinates' => [
            'lat' => $coordinates[0],
            'lng' => $coordinates[1],
        ]
    ];
}

function get_google_map_place($url_str){
    $status = 'E';

    $map_string = $url_str;
    $map_strings = explode('/', $map_string);

    $place = '';
    foreach ($map_strings as $k => $map_string){
        if ($map_string == 'place') {
            $place = $map_strings[$k + 1];
            $place = urldecode($place);
            // $place = str_replace('+', ' ', $place);
            $status = 'S';
            break;
        }
    }

    return [
        'status' => $status,
        'place' => $place
    ];
}