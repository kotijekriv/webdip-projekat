<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 2){
    header('Location: index.php');
}


if(isset($_POST['potvrdi'])) {

    $greska = '';
    $css = 'style="background-color: RGBA(255,0,0,0.5)"';
    $baza->spojiDB();

    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'potvrdi') {
            $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
            $smarty->assign($key, $css);
        }
    }

    if(empty($greska)){
        $datum = $_POST['datum'];
        $potvrda = $_POST['prisutnost'][0];
        $dijete = $_POST['djeca'];

        $upit = "INSERT INTO `Evidencija_dolazaka`(`datum_dolazaka`, `potvrda_dolaska`, `dijete`) VALUES 
                ('{$datum}', '{$potvrda}', '{$dijete}')";
        $baza->updateDB($upit);

        $smarty->assign('poruka', 'Uspješno ste unijeli novu evidenciju!');

        Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Slanje nove evidencije', Postavke::VirtualnoVrijeme());
    }
    else{
        $smarty->assign('pogreska', $greska);
    }
    $baza->zatvoriDB();
}

$smarty->display('templates/zaglavlje.tpl');
$smarty->display('templates/obrazac-nova-evidencija-voditelj.tpl');
$smarty->display('templates/podnozje.tpl');
