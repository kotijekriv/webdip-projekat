<?php
include_once('vanjske_biblioteke/baza.class.php');

class Dnevnik {
    public static function DodajAktivnost($korisnik, $dogadaj, $opis, $datum){
        $baza= new Baza();
        $baza->spojiDB();
        $upit_id = "SELECT id_Korisnik FROM `Korisnik` WHERE korisnicko_ime='{$korisnik}'";
        $id = $baza->selectDB($upit_id)->fetch_array()[0];
        $upit = "INSERT INTO `Dnevnik_rada`(`korisnik`, `dogadaj`, `opis_dogadaja`, `vrijeme`) VALUES ({$id}, {$dogadaj}, '{$opis}', '{$datum}')";
        $baza->updateDB($upit);
        $baza->zatvoriDB();
    }
}