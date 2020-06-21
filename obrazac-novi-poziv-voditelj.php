<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 2){
    header('Location: index.php');
}

if(preg_match('/id=/mi', $link) || preg_match('/poziv=/mi', $link)) {
    if (preg_match('/id=/mi', $link)) {
        $id = substr($link, strpos($link, "id=") + 3, strlen($link));
        $smarty->assign('idVrtic', $id);
    } else if (preg_match('/poziv=/mi', $link)) {
        $poziv = substr($link, strpos($link, "poziv=") + 6, strlen($link));

        $baza->spojiDB();
        $upit = "SELECT * FROM Javni_poziv_upis WHERE id_poziv = {$poziv}";
        $rezultat = $baza->selectDB($upit)->fetch_assoc();
        $baza->zatvoriDB();

        $smarty->assign('val1', $rezultat['id_poziv']);
        $smarty->assign('val2', $rezultat['djecji_vrtic']);
        $smarty->assign('val3', $rezultat['datum_pocetka']);
        $smarty->assign('val4', $rezultat['datum_zavrsetka']);
        $smarty->assign('val5', $rezultat['broj_upisnih_mjesta']);
    }

    if (isset($_POST['potvrdi'])) {
        $greska = '';
        $css = 'class="greska"';
        $baza->spojiDB();

        foreach ($_POST as $key => $value) {
            if (empty($value) && $key != 'potvrdi' && $key != 'id-poziv' && $key != 'id-vrtic') {
                $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
                $smarty->assign($key, $css);
            } else if ($key == 'mjesta') {
                if (strlen($value) != 7 && !is_numeric($value)) {
                    $greska .= 'Pogrešan broj mjesta! <br>';
                    $smarty->assign($key, $css);
                }
            }
        }

        if (empty($greska)) {
            $idVrtic = $_POST['id-vrtic'];
            $pocetak = $_POST['pocetak'];
            $zavrsetak = $_POST['zavrsetak'];
            $mjesta = $_POST['mjesta'];


            if (isset($id)){

                $upit = "INSERT INTO `Javni_poziv_upis`(`datum_pocetka`, `datum_zavrsetka`, `broj_upisnih_mjesta`, `djecji_vrtic`) 
                            VALUES ('{$pocetak}', '{$zavrsetak}', {$mjesta}, {$idVrtic})";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješan unos podataka o pozivu!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Unos nove poziva', Postavke::VirtualnoVrijeme());

            } else {
                $idPoziv = $_POST['id-poziv'];

                $upit = "UPDATE `Javni_poziv_upis` 
                            SET `datum_pocetka`='{$pocetak}',`datum_zavrsetka`='{$zavrsetak}',`broj_upisnih_mjesta`={$mjesta},`djecji_vrtic`={$idVrtic} 
                                WHERE `id_poziv` = {$idPoziv}";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješno ažuriranje podataka o pozivu!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Ažuriranje podataka o pozivu', Postavke::VirtualnoVrijeme());
            }
        } else {
            $smarty->assign('greska', $greska);
        }

        $baza->zatvoriDB();
    }
} else{
    header('Location: index.php');
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/obrazac-novi-poziv-voditelj.tpl');
$smarty->display('templates/podnozje.tpl');
