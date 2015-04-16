<?php

global $yn_arr;
global $_in_type;

$_in_type = [
    'int' => '%d',
    'string' => '%s',
    'float' => '%f',
];
$yn_arr = [
    'yes', 'no'
];

function validate_yes_no($str) {
    global $yn_arr;
    if (!in_array(strtolower($str), $yn_arr)) {
        write_stream('Please write either "Yes" OR "No".');
        fscanf(STDIN, "%s\n", $str);
        return validate_yes_no($str);
    }
    return $str;
}

function read_input($type = '%d') {
    fscanf(STDIN, "%d\n", $var);
    return $var;
}

function write_stream($msg, $new_line = false) {
    if ($new_line) {
        $patern = "%s\n";
    } else {
        $patern = "%s";
    }
    fprintf(STDOUT, $patern, $msg);
}

function add_numbers($digit = 0) {
    if (is_numeric($digit) && $digit > 0) {
        $num = $digit;
    } elseif (is_float($digit) && $digit > 0) {
        $num = round($digit);
    } else {
        return 0;
    }
    $_n_arr = str_split($digit);
    $_d_sum = 0;
    foreach ($_n_arr as $k => $v) {
        $_d_sum += $v;
    }
    if ($_d_sum > 10) {
        $_d_sum = add_numbers($_d_sum);
    }
    return $_d_sum;
}

function diagnol_matrix($rows, $cols) {
    if ($rows == $cols) {
        if ($rows > 0) {
            $i = 1;
            $_rows = $rows;
            while ($i <= $rows) {
                for ($j = 1; $j <= $cols; $j++) {
                    print (($i == $j || $j == $_rows)) ? " 0 " : ' ^ ';
                    usleep(99999);
                }
                echo PHP_EOL;
                $i++;
                $_rows--;
            }
        }
    } else {
        write_stream("If you are writing an diagonal matrix the be sure that number of rows should be equal to number to columns.:", true);
    }
}

function diagnol_matrix_border($rows, $cols) {
    if ($rows != $cols) {
        write_stream("If you are writing an diagonal matrix the be sure that number of rows should be equal to number to columns.:", true);
        die();
    }
    $i = 1;
    $_rows = $rows;
    while ($i <= $rows) {
        for ($j = 1; $j <= $cols; $j++) {
            print (($i == (($rows - 1) / 2) + 1) || ($j == (($rows - 1) / 2) + 1) || ($i == $j || $j == $_rows) || ($i == $j || $j == 1) || ($i == $j || $j == $cols) || ($i == 1) || ($i == $rows)) ? " + " : "   ";
            usleep(9999);
        }
        echo PHP_EOL;
        $i++;
        $_rows--;
    }
}

function generate_token($key, $secret, $expires = 27) {
    $time = time();
    $expire = "+{$expires} minutes";
    $hash_array = array(
        'key' => $key,
        'secret' => $secret,
        'time' => $time,
        'expires' => strtotime($expire, $time)
    );

    $_arr_str = array();
    foreach ($hash_array as $key => $value) {
        $_arr_str[] = "{$key}=$value";
    }
    $hash_str = '__' . implode("@@__", $_arr_str) . '@@__';
    $hash_str = hash_hmac('sha256', base64_encode($hash_str), $secret);

    return $hash_str;
}

function string_check_special_chars($_string = '') {
    $special_chars = array(
        ':-)', ':lol:', ':cheese:', ':)', ';-)', ';)', ':smirk:',
        ':roll:', ':-S', ':wow:', ':bug:', ':-P', '%-P', ';-P',
        ':P', ':blank:', ':long:', ':ohh:', ':grrr:', ':gulp:',
        '8-/', ':down:', ':red:', ':sick:', ':shut:', ':-/',
        '>:(', ':mad:', '>:-(', ':angry:', ':zip:', ':kiss:',
        ':ahhh:', ':coolsmile:', ':coolsmirk:', ':coolgrin:',
        ':coolhmm:', ':coolmad:', ':coolcheese:', ':vampire:',
        ':snake:', ':exclaim:', ':question:',
        '!', '@', '#', '$', '%', '^', '&', '(', ')', '-', '+',
        '=', '|', '\\', '?', '/', ',', '`', '~', ']', '[', '{',
        '}', '<', '>', '"', "'", "*", ":", ";",
    );
    return count(array_intersect(str_split($_string), $special_chars));
}

?>