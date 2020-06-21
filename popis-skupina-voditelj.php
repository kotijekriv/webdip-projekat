<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 2){
    header('Location: index.php');
}

$baza->spojiDB();
$korime = $_SESSION['korisnik'];

$upit = "SELECT dv.id_vrtic 
            FROM Djecji_vrtic AS dv, Korisnik AS k 
                WHERE k.id_Korisnik = dv.moderator AND k.korisnicko_ime = '{$korime}'";
$rezultat = $baza->selectDB($upit)->fetch_assoc();

$baza->zatvoriDB();

$idVrtic = $rezultat['id_vrtic'];

$smarty->assign('vrtic', $idVrtic);



Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Pregled skupina', Postavke::VirtualnoVrijeme());

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/popis-skupina-voditelj.tpl');
$smarty->display('templates/podnozje.tpl');