<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled popisa djece', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/popis-djece.tpl');
$smarty->display('templates/podnozje.tpl');