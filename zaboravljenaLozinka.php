<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(isset($_POST['submit'])){
    $greska = '';
    $css = 'class="greska"';

    $baza->spojiDB();
    $korime = $_POST['kime'];

    $upit = "SELECT * FROM `Korisnik` WHERE `korisnicko_ime` = '{$korime}'";
    $rezultat = $baza->selectDB($upit);

    if($rezultat == null){
        $greska .= 'Korisnicko ime ' . $korime . ' ne postoji! <br>';
        $smarty->assign('korisnicko_ime', $css);
    }

    if(empty($greska)){
        $nova_lozinka_korisnika = uniqid();
        $sol = 't1o9r5c0aoko99123';
        $lozinka_sha = sha1($sol . $nova_lozinka_korisnika);

        $upit = "UPDATE `Korisnik` SET `lozinka`='{$nova_lozinka_korisnika}',`lozinka_sha1`='{$lozinka_sha}' WHERE `korisnicko_ime`= '{$korime}'";
        $baza->updateDB($upit);

        $upit = "SELECT `email` FROM `Korisnik` WHERE `korisnicko_ime` = '{$korime}'";
        $red = $baza->selectDB($upit)->fetch_assoc();
        $odgovor = $red['email'];

        $link = "Nova lozinka za vaš korisnički račun je: " . $nova_lozinka_korisnika;
        $mail_to = $odgovor;
        $mail_subject = "Dječji vrtići - promjena lozinke";
        $mail_body = $link;
        $mail_from = "From: nova-lozinka@djecji-vrtici.hr";
        mail($mail_to, $mail_subject, $mail_body, $mail_from);

        $smarty->assign('poruka', 'Poslana vam je nova lozinka putema e-pošte');

        Dnevnik::DodajAktivnost($korime, 1, 'Zaboravljena lozinka', Postavke::VirtualnoVrijeme());
    }
    else{
        $smarty->assign('greska', $greska);
    }

    $baza->zatvoriDB();
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/zaboravljenaLozinka.tpl');
$smarty->display('templates/podnozje.tpl');