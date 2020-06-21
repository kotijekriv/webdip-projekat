<?php
$direktorij = substr(getcwd(), 0, 57);

include_once($direktorij . '/vanjske_biblioteke/smarty/libs/Smarty.class.php');
include_once($direktorij . '/vanjske_biblioteke/sesija.class.php');
include_once($direktorij . '/vanjske_biblioteke/baza.class.php');
include_once($direktorij . '/dnevnik.class.php');
include_once($direktorij . '/postavke.class.php');

Sesija::kreirajSesiju();

$smarty = new Smarty();
$baza = new Baza();

$putanja = 'http://barka.foi.hr' . substr($_SERVER['REQUEST_URI'], 0, 37);
$link = 'http://barka.foi.hr' . $_SERVER['REQUEST_URI'];

if(isset($_SERVER['HTTPS']) && !preg_match('/prijava.php/mi', $link)){
    header("Location: {$link}");
}
else{
    $putanja = 'https://barka.foi.hr' . substr($_SERVER['REQUEST_URI'], 0, 37);
    $link = 'https://barka.foi.hr' . $_SERVER['REQUEST_URI'];
}

if(preg_match('/WebDiP2019x107\/?$/mi', $link)){
    header("Location: index.php");
}

$smarty->assign('putanja', $link);
$smarty->assign('direktorij', $putanja);

if(preg_match('/index.php/mi', $link)) $smarty->assign('naslov', 'Dječji vrtići');
else if(preg_match('/popis-djece.php/mi', $link)) $smarty->assign('naslov', 'Popis djece');
else if(preg_match('/poziv-upisi.php/mi', $link)) $smarty->assign('naslov', 'Pozivi za upis');
else if(preg_match('/registracija.php/mi', $link)) $smarty->assign('naslov', 'Registracija');
else if(preg_match('/prijava.php/mi', $link)) $smarty->assign('naslov', 'Prijava');
else if(preg_match('/korisnici.php/mi', $link)) $smarty->assign('naslov', 'Korisnici');
else if(preg_match('/zaboravljenaLozinka.php/mi', $link)) $smarty->assign('naslov', 'Zaboravljena lozinka');
else if(preg_match('/popis-racuna.php/mi', $link)) $smarty->assign('naslov', 'Popis računa');
else if(preg_match('/popis-dolazaka.php/mi', $link)) $smarty->assign('naslov', 'Popis dolazaka');
else if(preg_match('/obrazac-slanje-prijave.php/mi', $link)) $smarty->assign('naslov', 'Slanje prijave');
else if(preg_match('/popis-prijava-roditelja.php/mi', $link)) $smarty->assign('naslov', 'Popis prijava - roditelj');
else if(preg_match('/obrazac-unos-djece.php/mi', $link)) $smarty->assign('naslov', 'Unos djeteta');
else if(preg_match('/popis-djece-roditelj.php/mi', $link)) $smarty->assign('naslov', 'Popis djece - roditelj');
else if(preg_match('/kontrolna-ploca-roditelj.php/mi', $link)) $smarty->assign('naslov', 'Kontrolna ploča - roditelj');
else if(preg_match('/kontrolna-ploca-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Kontrolna ploča - voditelj');
else if(preg_match('/kontrolna-ploca-admin.php/mi', $link)) $smarty->assign('naslov', 'Kontrolna ploča - admin');
else if(preg_match('/popis-skupina-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Popis skupina - voditelj');
else if(preg_match('/obrazac-nova-skupina-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Unos nove skupine - voditelj');
else if(preg_match('/popis-javni-pozivi-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Popis javnih poziva - voditelj');
else if(preg_match('/obrazac-novi-poziv-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Unos novog poziva - voditelj');
else if(preg_match('/popis-prijava-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Popis prijava - voditelj');
else if(preg_match('/popis-evidencija-dolazaka-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Evidencija dolaska - voditelj');
else if(preg_match('/statistika-racuna-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Statistika racuna - voditelj');
else if(preg_match('/obrazac-nova-evidencija-voditelj.php/mi', $link)) $smarty->assign('naslov', 'Evidencija djece - voditelj');
else if(preg_match('/obrazac-konfiguracija-sustava-admin.php/mi', $link)) $smarty->assign('naslov', 'Konfiguracija sustava - admin');
else if(preg_match('/popis-dnevnik-rada-admin.php/mi', $link)) $smarty->assign('naslov', 'Dnevnik rada - admin');
else if(preg_match('/upravljanje-blokadama-admin.php/mi', $link)) $smarty->assign('naslov', 'Korisnici - admin');
else if(preg_match('/popis-vrtica-detaljno-admin.php/mi', $link)) $smarty->assign('naslov', 'Vrtići - admin');

if(isset($_SESSION['uloga']) && $_SESSION['uloga'] < 4) $smarty->assign('registrirani', true);
if(isset($_SESSION['uloga']) && $_SESSION['uloga'] < 3) $smarty->assign('voditelj', true);
if(isset($_SESSION['uloga']) && $_SESSION['uloga'] == 1) $smarty->assign('admin', true);
if(!isset($_SESSION['uloga'])) $smarty->assign('neregistrirani', true);

if(isset($_SESSION['korisnik'])){
    $smarty->assign('nazivPrijave', 'Odjava');
    $smarty->assign('url', $putanja . 'index.php');
}
else{
    $smarty->assign('nazivPrijave', 'Prijava');
    $smarty->assign('url', $putanja . 'prijava.php');
}

if(isset($_GET['prijava-odjava']) && isset($_SESSION['korisnik'])){
    Sesija::obrisiSesiju();
    header("Location: {$putanja}index.php");
}

if(isset($_COOKIE['prijava'])){
    $smarty->assign('prijava', $_COOKIE['prijava']);
}

$korisnikDnevnik = 'neregistriraniKorisnik';
if(Sesija::dajKorisnika() != null){
    $korisnikDnevnik = Sesija::dajKorisnika()['korisnik'];
}

