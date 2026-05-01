<?php
// * User functions

// * Functions required for reporting template
function group_by($data, $key)
{
    $result = array();

    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[''][] = $val;
        }
    }

    return array_values($result);
}

function sum($array, $key)
{
    $sum = 0;

    foreach ($array as $item) {
        $sum = $sum + $item[$key];
    }
    return $sum;
}

function custom_format($value, $type)
{
    switch ($type) {
        case 'number':
            return number_format($value, 2);
        case 'integer':
            return intval($value);
        case 'date':
            return $value;
        case 'money':
            return '$' . number_format($value, 2);
        case 'icon':
            return "<i class='fa fa-$value'></i>";
        case 'link':
            if($value == "") return "";
            return "<a href='$value' target='_blank'><i class='fa fa-link'></i></a>";
        default:
            return $value;
    }
}
