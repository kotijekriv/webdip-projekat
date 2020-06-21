<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 3){
    header('Location: index.php');
}



if(preg_match('/\?id=/mi', $link)){
    $id = substr($link, strpos($link,"id=") + 3, 89);
    $smarty->assign('prijava', $id);
    $smarty->assign('gumb', 'AŽURIRAJ PRIJAVU');
}
else{
    $smarty->assign('gumb', 'POŠALJI PRIJAVU');
}

if(isset($_POST['potvrdi'])) {

    $greska = '';
    $css = 'style="background-color: RGBA(255,0,0,0.5)"';
    $baza->spojiDB();

    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'potvrdi' && $key !== 'id-prijave') {
            $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
            $smarty->assign($key, $css);
        }
    }

    if(empty($greska)){

        $poziv = $_POST['vrtici'];
        $skupina = $_POST['skupine'];

        if(empty($_POST['id-prijave'])){
            $vrijeme = Postavke::VirtualnoVrijeme();
            $korime = $_SESSION['korisnik'];

            $upit = "SELECT id_Korisnik FROM `Korisnik` WHERE korisnicko_ime = '{$korime}'";
            $roditelj = $baza->selectDB($upit)->fetch_array()[0];

            $upit = "INSERT INTO `Prijava_za_upis_u_vrtic`(`datum_prijave`, `roditelj`, `status_prijave`, `skupina`, `javni_poziv`) VALUES 
                    ('{$vrijeme}', '{$roditelj}', 5, '{$skupina}', '{$poziv}')";
            $baza->updateDB($upit);

            $smarty->assign('pogreska', 'Uspješno ste poslali prijavu za vrtić!');

            Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Slanje nove prijave', Postavke::VirtualnoVrijeme());
        }
        else{
            $idPrijave = $_POST['id-prijave'];
            $upit = "UPDATE `Prijava_za_upis_u_vrtic` SET `skupina`={$skupina},`javni_poziv`={$poziv} WHERE id_prijave = {$idPrijave}";
            $baza->updateDB($upit);

            $smarty->assign('pogreska', 'Uspješno ste ažurirali prijavu za vrtić!');

            Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Ažuriranje prijave', Postavke::VirtualnoVrijeme());
        }


    }
    else{
        $smarty->assign('pogreska', $greska);
    }
    $baza->zatvoriDB();
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/obrazac-za-slanje-prijave.tpl');
$smarty->display('templates/podnozje.tpl');
