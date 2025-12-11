<?php

if (!function_exists('date_indo')) {
    function date_indo($date) {
        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        
        $split = explode('-', $date);
        $hari = date('l', strtotime($date));
        
        switch($hari){
            case 'Sunday': $hari = 'Minggu'; break;
            case 'Monday': $hari = 'Senin'; break;
            case 'Tuesday': $hari = 'Selasa'; break;
            case 'Wednesday': $hari = 'Rabu'; break;
            case 'Thursday': $hari = 'Kamis'; break;
            case 'Friday': $hari = 'Jumat'; break;
            case 'Saturday': $hari = 'Sabtu'; break;
        }
        
        return $hari . ', ' . $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
}