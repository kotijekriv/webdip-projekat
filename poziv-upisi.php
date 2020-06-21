<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled poziva za upis', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/poziv-upisi.tpl');
$smarty->display('templates/podnozje.tpl');
