<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 2){
    header('Location: index.php');
}

Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled plaćenih/neplaćenih računa', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/statistika-racuna-voditelj.tpl');
$smarty->display('templates/podnozje.tpl');