<?php

if (! function_exists('csv_to_array')) {

    /**
     * Convert csv to array
     * 
     * @param mixed $filename
     * @param mixed $header
     * 
     * @return mixed
     */
    function csv_to_array($filename, $header)
    {
        $delimiter = ',';
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
}