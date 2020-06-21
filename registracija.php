<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(isset($_POST['submit'])){
    $greska = '';
    $css = 'class="greska"';

    $baza->spojiDB();

    foreach($_POST as $key => $value){
        if(empty($value) && $key != 'submit' && $key != 'g-recaptcha-response'){
            $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
            $smarty->assign($key, $css);
        }
        else if($key == 'korisnicko_ime'){
            $upit = "SELECT * FROM `Korisnik` WHERE `korisnicko_ime` = '{$value}'";
            $rezultat = $baza->selectDB($upit)->fetch_array();

            if($rezultat != null){
                $greska .= 'Korisnicko ime ' . $value . ' već postoji <br>';
                $smarty->assign($key, $css);
            }
            if (strlen($value) < 3){
                $greska .= 'Korisnicko ime mora sadržavati najmanje tri znaka <br>';
                $smarty->assign($key, $css);
            }
        }
        else if($key == 'email'){
            $upit = "SELECT * FROM `Korisnik` WHERE `email` = '{$value}'";
            $rezultat = $baza->selectDB($upit)->fetch_array();

            if($rezultat != null){
                $greska .= 'Email ' . $value . ' već postoji <br>';
                $smarty->assign($key, $css);
            }
        }
        else if($key == 'birthday'){
            $godina = date('Y', strtotime($value));
            if($godina < 1900 || $godina > 2020){
                $greska .= 'Godina rođenja mora biti u rasponu od 1900 do 2020 <br>';
                $smarty->assign($key, $css);
            }
        }
        else if($key == 'lozinka'){
            if($value != $_POST['potvrdi_lozinku']){
                $greska .= 'Lozinka i potvrda lozinke moraju biti jednake <br>';
                $smarty->assign($key, $css);
                $smarty->assign('potvrdi_lozinku', $css);
            }
        }
        else if($key == 'oib'){
            if(strlen($value) != 11){
                $greska .= 'Oib mora imati 11 znamenaka <br>';
                $smarty->assign($key, $css);
                $smarty->assign($key, $css);
            }
        }
    }

    if(empty($greska)){
        $oib = $_POST['oib'];
        $ime = $_POST['ime'];
        $prezime = $_POST['prezime'];
        $godinarodenja = $_POST['birthday'];
        $adresa = $_POST['adresa'];
        $mobitel = $_POST['mobitel'];
        $email = $_POST['email'];
        $korime = $_POST['korisnicko_ime'];
        $lozinka = $_POST['lozinka'];
        $sol = 't1o9r5c0aoko99123';
        $lozinka_sha = sha1($sol . $_POST['lozinka']);
        $vrijeme = Postavke::VirtualnoVrijeme();
        $linkzaaktivaciju = sha1($korime);

        $upit = "INSERT INTO `Korisnik` (`korisnicko_ime`, `lozinka`, `lozinka_sha1`, `ime_korisnik`, `prezime_korisnik`, `datum_rodenja`, `oib`, `email`, `broj_mobitela`, 
                `datum_prihvacanja_uvjeta`, `link_za_aktivaciju`, `vrsta_korisnika`, `status_korisnika`, `adresa`) VALUES ('{$korime}', '{$lozinka}', '{$lozinka_sha}', '{$ime}', 
                '{$prezime}', '{$godinarodenja}', '{$oib}', '{$email}', '{$mobitel}', '{$vrijeme}', '{$linkzaaktivaciju}', 3, 1, '{$adresa}')";
        $baza->updateDB($upit);

        $link = "http://barka.foi.hr/WebDiP/2019_projekti/WebDiP2019x107/skripte/AktivacijaKorisnika.php?aktivacijskikod=" . sha1($_POST['korisnicko_ime']);
        $mail_to = $_POST['email'];
        $mail_subject = "Dječji vrtići - aktivacija korisničkog računa";
        $mail_body = $link;
        $mail_from = "From: aktivacija-racuna@djecji-vrtici.hr";
        mail($mail_to, $mail_subject, $mail_body, $mail_from);

        $smarty->assign('poruka', 'Uspješna registracija');

        Dnevnik::DodajAktivnost($korime, 2, 'Registracija korisnika', Postavke::VirtualnoVrijeme());
    }
    else{
        $smarty->assign('greska', $greska);
    }
    $baza->zatvoriDB();
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/registracija.tpl');
$smarty->display('templates/podnozje.tpl');
