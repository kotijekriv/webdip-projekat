<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled dječjih vrtića', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/index.tpl');
$smarty->display('templates/podnozje.tpl');