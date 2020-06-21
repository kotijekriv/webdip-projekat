<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 1){
    header('Location: index.php');
}

$poziv = substr($link, strpos($link, "poziv=") + 6, strlen($link));

$baza->spojiDB();
$upit = "SELECT * FROM Postavke_sustava WHERE postavke_id = 1";

$rezultat = $baza->selectDB($upit)->fetch_assoc();
$baza->zatvoriDB();

$smarty->assign('val1', $rezultat['pomak_sati']);
$smarty->assign('val2', $rezultat['aktivacija_sati']);
$smarty->assign('val3', $rezultat['broj_pokusaja_prijave']);
$smarty->assign('val4', $rezultat['kolacic_prijava_sati']);
$smarty->assign('val5', $rezultat['stranicenje']);
$smarty->assign('val6', $rezultat['trajanje_sesije']);


if (isset($_POST['potvrdi'])) {
    $greska = '';
    $css = 'class="greska"';
    $baza->spojiDB();

    foreach ($_POST as $key => $value) {
        if (empty($value) && $key != 'potvrdi') {
            $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
            $smarty->assign($key, $css);
        } else if ($value > 0 && !is_numeric($value)) {
                $greska .= 'Unesite broj veći od 0 <br>';
                $smarty->assign($key, $css);
        }
    }

    if (empty($greska)) {
        $pomak = $_POST['pomak'];
        $aktivacija = $_POST['aktivacija'];
        $pokusaju = $_POST['pokusaju'];
        $kolacici = $_POST['kolacici'];
        $stranicenje = $_POST['stranicenje'];
        $sesija = $_POST['sesija'];


        $upit = "UPDATE `Postavke_sustava` 
                    SET `pomak_sati`={$pomak},`aktivacija_sati`={$aktivacija},`broj_pokusaja_prijave`={$pokusaju},`kolacic_prijava_sati`={$kolacici},`stranicenje`={$stranicenje},`trajanje_sesije`={$sesija} 
                        WHERE `postavke_id`= 1";

        $baza->updateDB($upit);

        $smarty->assign('poruka', 'Uspješno ažuriranje konfiguracija!');
        Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Ažuriranje konfiguracije', Postavke::VirtualnoVrijeme());

    } else {
        $smarty->assign('greska', $greska);
    }

    $baza->zatvoriDB();

}
$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/obrazac-konfiguracija-sustava-admin.tpl');
$smarty->display('templates/podnozje.tpl');
