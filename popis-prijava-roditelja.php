<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 3){
    header('Location: index.php');
}

Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled prijava', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/popis-prijava-roditelja.tpl');
$smarty->display('templates/podnozje.tpl');