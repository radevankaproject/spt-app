<?php

if (!function_exists('formatNip')) {
    function formatNip($nip)
{
    // Pastikan hanya angka
    $nip = preg_replace('/[^0-9]/', '', $nip);

    if (strlen($nip) !== 18) {
        return $nip; // atau bisa return 'Format NIP salah';
    }

    $tglLahir = substr($nip, 0, 8);     // 19900815
    $tmtCpns  = substr($nip, 8, 6);     // 201601
    $jk       = substr($nip, 14, 1);    // 1
    $urut     = substr($nip, 15, 3);    // 005

    return "$tglLahir $tmtCpns $jk $urut";
}

}
