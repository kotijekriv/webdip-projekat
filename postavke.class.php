<?php
include_once('vanjske_biblioteke/baza.class.php');

class Postavke{
	private static function DohvatiPostavke(){
		$baza = new Baza();
		$baza->spojiDB();
		$upit = "SELECT * FROM `Postavke_sustava` WHERE postavke_id = 1";
		$rezultat = $baza->selectDB($upit);
		$baza->zatvoriDB();
		
		return $rezultat->fetch_assoc();
	}
	
	public static function VirtualnoVrijeme(){
		$postavke = Postavke::DohvatiPostavke();
		$vrijeme = time() + $postavke['pomak_sati']*3600;

		return date("Y-m-d H:i:s", $vrijeme);		
	}

	public static function TrajanjeAktivacijskogLinka(){
	    $postavke = Postavke::DohvatiPostavke();
	    return $postavke['aktivacija_sati'];
    }

    public static function TrajanjeKolacicaPrijave(){
	    $postavke = Postavke::DohvatiPostavke();
	    return $postavke['kolacic_prijava_sati'];
    }

    public static function BrojPokusajaPrijave(){
	    $postavke = Postavke::DohvatiPostavke();
	    return $postavke['broj_pokusaja_prijave'];
    }

    public static function TrajanjeSesije(){
        $postavke = Postavke::DohvatiPostavke();
        return $postavke['trajanje_sesije'] * 3600;
    }

    public static function Stanicenje(){
        $postavke = Postavke::DohvatiPostavke();
        return $postavke['stranicenje'];
    }

    public static function UnosSlikeDjeteta($slika, $size){
        if ($slika['error'] > 0) {
            switch ($slika['error']) {
                case 1: return 'Veličina slike je veća od ' . ini_get('upload_max_filesize');
                case 2: return 'Veličina slike je veća od ' . $size;
                case 3: return 'Slika je djelomično prenesena';
                case 4: return 'Slika nije prenesena';
            }
        }

        if (preg_match('/image/i', $slika['type']) == false) {
            return 'Datoteka ' . $slika['name'] . ' nije u odgovarajućem formatu';
        }
        $upfile = 'multimedija/slikeDjeca/' . $slika['name'];

        if (is_uploaded_file($slika['tmp_name'])) {
            if (!move_uploaded_file($slika['tmp_name'], $upfile)) {
                return 'Nije moguće prenijeti datoteku na odredište';
            }
        } else {
            return 'Mogući napad prijenosom - datoteka: ' . $slika['name'];
        }

        return '';
    }
}
