<?php
include ('zaglavlje.php');
include ('podnozje.php');

if(!isset($_SESSION['uloga'])){
    header('Location: prijava.php');
}
else if($_SESSION['uloga'] > 2){
    header('Location: index.php');
}

if(preg_match('/id=/mi', $link) || preg_match('/skupina=/mi', $link)) {
    if (preg_match('/id=/mi', $link)) {
        $id = substr($link, strpos($link, "id=") + 3, strlen($link));
        $smarty->assign('idVrtic', $id);
    } else if (preg_match('/skupina=/mi', $link)) {
        $skupina = substr($link, strpos($link, "skupina=") + 8, strlen($link));

        $baza->spojiDB();
        $upit = "SELECT * FROM Skupina WHERE id_skupina = {$skupina}";
        $rezultat = $baza->selectDB($upit)->fetch_assoc();
        $baza->zatvoriDB();

        $smarty->assign('val1', $rezultat['id_skupina']);
        $smarty->assign('val2', $rezultat['djecji_vrtic']);
        $smarty->assign('val3', $rezultat['naziv']);
        $smarty->assign('val4', $rezultat['cijena']);
        $smarty->assign('val5', $rezultat['broj_mjesta']);
    }

    if (isset($_POST['potvrdi'])) {
        $greska = '';
        $css = 'class="greska"';
        $baza->spojiDB();

        foreach ($_POST as $key => $value) {
            if (empty($value) && $key != 'potvrdi' && $key != 'id-skupina' && $key != 'id-vrtic') {
                $greska .= 'Polje ' . $key . ' ne smije biti prazno <br>';
                $smarty->assign($key, $css);
            } else if ($key == 'cijena') {
                if (strlen($value) != 7 && !is_numeric($value)) {
                    $greska .= 'Pogrešna cijena! <br>';
                    $smarty->assign($key, $css);
                }
            } else if ($key == 'mjesta') {
                if (strlen($value) != 7 && !is_numeric($value)) {
                    $greska .= 'Pogrešan broj mjesta! <br>';
                    $smarty->assign($key, $css);
                }
            }
        }

        if (empty($greska)) {
            $idVrtic = $_POST['id-vrtic'];
            $naziv = $_POST['naziv'];
            $cijena = $_POST['cijena'];
            $mjesta = $_POST['mjesta'];


            if (isset($id)){

                $upit = "INSERT INTO `Skupina`(`naziv`, `djecji_vrtic`, `cijena`, `broj_mjesta`) 
                            VALUES ('{$naziv}', {$idVrtic}, {$cijena}, {$mjesta})";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješan unos podataka o skupini!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Unos nove skupine', Postavke::VirtualnoVrijeme());

            } else {
                $idSkupina = $_POST['id-skupina'];

                $upit = "UPDATE `Skupina` 
                            SET `naziv`='{$naziv}',`cijena`={$cijena},`broj_mjesta`={$mjesta} 
                                WHERE `id_skupina` = {$idSkupina}";
                $baza->updateDB($upit);

                $smarty->assign('poruka', 'Uspješno ažuriranje podataka o skupini!');
                Dnevnik::DodajAktivnost($korisnikDnevnik, 2, 'Ažuriranje podataka o skupini', Postavke::VirtualnoVrijeme());
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
$smarty->display('templates/obrazac-nova-skupina-voditelj.tpl');
$smarty->display('templates/podnozje.tpl');
