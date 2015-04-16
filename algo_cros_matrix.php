<?php

require_once '__functions__.php';
if (php_sapi_name() == 'cli') {
    $rows = $cols = false;
    if ($argc > 1) {
        $rows = isset($argv[1]) ? $argv[1] : false;
        $cols = isset($argv[2]) ? $argv[2] : $rows;
    }
    if (!$rows || !$cols) {
        write_stream("Enter number of rows and columns:");
        fscanf(STDIN, "%d %d\n", $rows, $cols);
        if (!$cols) {
            $cols = $rows;
        }
    }
    write_stream("Please sit back we are drawing a diagonal matrix({$rows}x{$cols}):", true);
    for ($i = 0; $i < round($rows / 3); $i++) {
        write_stream(".");
        usleep(99999);
    }

    write_stream(PHP_EOL, true);

    if ($rows > 5) {
        diagnol_matrix_border($rows, $cols);
    } else {
        diagnol_matrix($rows, $cols);
    }
}
?>