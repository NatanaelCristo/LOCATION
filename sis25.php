<?php
    ob_start();
	error_reporting(-1);
	$timezone = "Asia/Jakarta";
	date_default_timezone_set($timezone);
	session_start();
	set_time_limit(0);
	
    // Database configuration
    $servername = "localhost";
    $username = "dste9565_head_usr";
    $password = "Digdaya@2025";
    $dbname = "dste9565_headoffice";

    // Create connection
    $konsis25 = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($konsis25->connect_error) {
        die("Connection failed: " . $konsis25->connect_error);
    }


    define("ALAMAT_URL", "www.dstmobil.com");
    define("TOKEN", "Av6RqjJmOfQhX9psCyg74tgPGv7kpsrcqlO7pz6jITpBdWWaejdOgXdklmpa18AZ");
    
	$servername="";
	$username="";
	$password="";
	$dbname="";
	
	// Daftar API endpoint dan nama cabangnya
    $apiCabangs = [
        "https://jakarta.dstmobil.com/api/tabel_servis" => "Jakarta",
        "https://tangerang.dstmobil.com/api/tabel_servis" => "Tangerang"
    ];
    
    // API Reservasi Bengkel
    $apiReservasi = [
        "Jakarta" => "https://jakarta.dstmobil.com/api/reservasi_bengkel",
        "Tangerang" => "https://tangerang.dstmobil.com/api/reservasi_bengkel"
    ];
    
    // API Jumlah Antrian
    $apiAntrian = [
        "Jakarta" => "https://jakarta.dstmobil.com/api/jumlah_antrian",
        "Tangerang" => "https://tangerang.dstmobil.com/api/jumlah_antrian"
    ];
    
?>