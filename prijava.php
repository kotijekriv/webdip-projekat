<?php
include_once('zaglavlje.php');
include_once('podnozje.php');

if(!isset($_SERVER['HTTPS'])){
    header("Location: {$link}");
}

if(isset($_POST['submit'])) {
    $korisnicko_ime = $_POST['kime'];
    $sol = 't1o9r5c0aoko99123';
    $lozinka = sha1($sol . $_POST['lozinka']);
    $greska = '';
    $css = 'style="background-color: RGBA(255,0,0,0.5)"';

    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'submit') {
            $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
            $smarty->assign($key, $css);
        }
    }

    if(empty($greska)){
        $baza->spojiDB();
        $upit = "SELECT `korisnicko_ime`, `vrsta_korisnika`,  `status_korisnika` FROM `Korisnik`
                WHERE `korisnicko_ime` = '$korisnicko_ime' AND `lozinka_sha1` = '$lozinka'";
        $rezultat = $baza->selectDB($upit)->fetch_assoc();

        if(isset($rezultat) && Sesija::dajKorisnika() == null && $rezultat['status_korisnika'] == 0){
            Sesija::kreirajKorisnika($korisnicko_ime, $rezultat['vrsta_korisnika']);

            if(isset($_POST['zapamtime'])){
                $trajanje = strtotime(Postavke::VirtualnoVrijeme()) + Postavke::TrajanjeKolacicaPrijave()*3600;
                setcookie('prijava', $korisnicko_ime, $trajanje);
            }

            $upit = "UPDATE Korisnik SET neuspjesne_prijave = 0 WHERE korisnicko_ime = '{$korisnicko_ime}'";
            $baza->updateDB($upit);

            Dnevnik::DodajAktivnost($korisnicko_ime, 1, 'Prijava u sustav', Postavke::VirtualnoVrijeme());

            header('Location: index.php');
        }
        else if ($rezultat['status_korisnika'] == 1){
            Dnevnik::DodajAktivnost($korisnicko_ime, 1, 'Neuspješna prijava', Postavke::VirtualnoVrijeme());
            $smarty->assign('poruka', 'Korisnički račun nije aktiviran!');
        }
        else if ($rezultat == null){
            $brojPokusaja = Postavke::BrojPokusajaPrijave();

            $upit = "SELECT * FROM Korisnik WHERE korisnicko_ime = '{$korisnicko_ime}'";
            $rezultat = $baza->selectDB($upit)->fetch_assoc();

            if($rezultat != null){
                $brojPrijava = $rezultat['neuspjesne_prijave'];
                $brojPrijava++;

                if($brojPrijava == $brojPokusaja){
                    $upit = "UPDATE Korisnik SET neuspjesne_prijave = {$brojPrijava}, status_korisnika = 1 WHERE korisnicko_ime = '{$korisnicko_ime}'";
                    $baza->updateDB($upit);
                    $smarty->assign('poruka', 'Račun je blokiran!');

                    Dnevnik::DodajAktivnost($korisnicko_ime, 1, 'Račun je blokiran', Postavke::VirtualnoVrijeme());
                }
                else{
                    $upit = "UPDATE Korisnik SET neuspjesne_prijave = {$brojPrijava} WHERE korisnicko_ime = '{$korisnicko_ime}'";
                    $baza->updateDB($upit);
                    $smarty->assign('poruka', 'Neuspješna prijava, pokušajte ponovo!');

                    Dnevnik::DodajAktivnost($korisnicko_ime, 1, 'Neuspješna prijava', Postavke::VirtualnoVrijeme());
                }
            }
        }
        else if(Sesija::dajKorisnika() != null){
            $smarty->assign('poruka', 'Već ste prijavljeni!');
        }

        $baza->zatvoriDB();
    }
    else{
        $smarty->assign('greska', $greska);
    }
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/prijava.tpl');
$smarty->display('templates/podnozje.tpl');