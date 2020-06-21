<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
    exit();
}
else if($_SESSION['uloga'] > 3){
    header('Location: index.php');
    exit();
}

if(preg_match('/id=/mi', $link) || preg_match('/dijete=/mi', $link)) {
    if (preg_match('/id=/mi', $link)) {
        $id = substr($link, strpos($link, "id=") + 3, strlen($link));
        $smarty->assign('dijete', $id);
    } else if (preg_match('/dijete=/mi', $link)) {
        $dijete = substr($link, strpos($link, "dijete=") + 7, strlen($link));

        $baza->spojiDB();
        $upit = "SELECT * FROM Dijete WHERE id_dijete = {$dijete}";
        $rezultat = $baza->selectDB($upit)->fetch_assoc();
        $baza->zatvoriDB();

        $smarty->assign('val1', $rezultat['ime_dijete']);
        $smarty->assign('val2', $rezultat['prezime_dijete']);
        $smarty->assign('val3', $rezultat['datum_rodenja']);
        $smarty->assign('val4', $rezultat['oib']);
        $smarty->assign('val5', $rezultat['dozvola_koristenja']);
    }

    if (isset($_POST['potvrdi'])) {
        $greska = '';
        $css = 'class="greska"';
        $baza->spojiDB();

        foreach ($_POST as $key => $value) {
            if (empty($value) && $key != 'potvrdi' && $key != 'id-prijave') {
                $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
                $smarty->assign($key, $css);
            } else if ($key == 'oib') {
                if (strlen($value) != 11 && !is_numeric($value)) {
                    $greska .= 'OIB mora imati 11 znamenaka <br>';
                    $smarty->assign($key, $css);
                }
            } else if ($key == 'birthday') {
                $godina = date('Y', strtotime($value));
                if ($godina < 1900 || $godina > 2020) {
                    $greska .= 'Godina rođenja mora biti u rasponu od 1900 do 2020 <br>';
                    $smarty->assign($key, $css);
                }
            }
        }

        $image = $_FILES['upload'];
        $poruka = Postavke::UnosSlikeDjeteta($_FILES['upload'], $_POST["MAX_FILE_SIZE"]);
        if (!empty($poruka)) {
            $greska .= $poruka . '<br>';
        }

        if (empty($greska)) {
            $oib = $_POST['oib'];
            $ime = $_POST['ime'];
            $prezime = $_POST['prezime'];
            $datumrodenja = $_POST['birthday'];
            $slika = $_FILES['upload']['name'];

            if (isset($_POST['privola'])) {
                $privola = 1;
            } else {
                $privola = 0;
            }

            if (isset($id)){
                $upit = "SELECT `roditelj`, `skupina` FROM `Prijava_za_upis_u_vrtic` WHERE `id_prijave` = {$id}";
                $rezultat = $baza->updateDB($upit)->fetch_assoc();
                $roditelj = $rezultat['roditelj'];
                $skupina = $rezultat['skupina'];

                $upit = "INSERT INTO `Dijete`(`ime_dijete`, `prezime_dijete`, `datum_rodenja`, `oib`, `slika`, `dozvola_koristenja`, `roditelj`, `Skupina_id_skupina`)
                    VALUES ('{$ime}', '{$prezime}', '{$datumrodenja}', '{$oib}', '{$slika}', {$privola}, {$roditelj}, {$skupina})";
                $baza->updateDB($upit);

                $upit = "UPDATE `Prijava_za_upis_u_vrtic` SET `status_prijave`=1 WHERE id_prijave = {$id}";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješan unos podataka o djetetu!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Unos djeteta', Postavke::VirtualnoVrijeme());
            } else {
                $upit = "UPDATE `Dijete` SET `ime_dijete`='{$ime}',`prezime_dijete`='{$prezime}',`datum_rodenja`='{$datumrodenja}',
                    `oib`='{$oib}',`slika`='{$slika}',`dozvola_koristenja`={$privola} WHERE `id_dijete` = {$dijete}";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješno ažuriranje podataka o djetetu!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Ažuriranje podataka o djetetu', Postavke::VirtualnoVrijeme());
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
$smarty->display('templates/obrazac-unos-djece.tpl');
$smarty->display('templates/podnozje.tpl');

