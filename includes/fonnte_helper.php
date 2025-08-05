<?php

/**
 * Mengirim pesan WhatsApp menggunakan API Fonnte.
 *
 * @param string $target Nomor tujuan dengan format '08xxxxxxxxxx'.
 * @param string $message Isi pesan yang akan dikirim.
 * @return string Respons dari server Fonnte.
 */
function sendWhatsApp($target, $message)
{
    // GANTI DENGAN API KEY ANDA DARI FONNTE
    $apiKey = 'LZdd8Fn4qVDa3Hk3QZwR';

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => $target,
            'message' => $message,
            'countryCode' => '62', // Kode negara Indonesia
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: " . $apiKey
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
