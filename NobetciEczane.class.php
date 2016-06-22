<?php
/**
* 	@author: Vehbi AKDOGAN
* 	@mail: mf.leqelyy@gmail.com || info@vehbiakdogan.com
* 	@website: vehbiakdogan.com
*/

class NobetciEczane {

    private $adres = "http://www.hastanebul.com.tr/nobetci-eczaneler";

    private $sehir; // Girilecek İl
    private $gelenVeri; // Çektiğimiz Veriler
    private $verilerArray = array(); // göndereceğimiz jSon Veya Dizi Türünde Parametre



    /**
     *
     * @param String $il
     *
     * @void
     */
    public function __construct($il){
        $this->sehir = $il;
        $this->gelenVeri = file_get_contents("http://www.hastanebul.com.tr/".$this->EnCevir($this->sehir)."-nobetci-eczaneler");
        if($this->gelenVeri === FALSE) {
            exit("Lütfen Geçerli Bir İl Giriniz.");
        }
        //echo $this->gelenVeri;
        $this->parcala();
    }

    private function parcala(){
        preg_match_all('#<div class="panel-heading">(.*?)</div>#si',$this->gelenVeri,$basliklar);
        preg_match_all('#<div class="panel-body pharmacyonduty">(.*?)</div>#si',$this->gelenVeri,$detaylar);

        if(empty($basliklar[0])) die("Geçerli Bir İl Giriniz.");

        for($i=0;$i<count($basliklar[1]);$i++) {
            $seo_baslik = $this->EnCevir(strip_tags(trim($basliklar[1][$i])));
            if(($seo_baslik != null) && ($seo_baslik != 'reklam') && ($seo_baslik != 'google-reklam')){
                $this->verilerArray[$i]["eczaneAdi"] = strip_tags(trim($basliklar[1][$i]));
                $bol   = explode("<br>",$detaylar[1][$i]);
                $bol_2 = explode('<br />',$bol[0]);
                $this->verilerArray[$i]["eczaneAdres"] = strip_tags(trim($bol_2[1]));
                $this->verilerArray[$i]["eczaneTelefon"] = strip_tags(trim($bol_2[4]));
            }
        }

    }

    /**
     *
     * @param String $type
     *
     * @param defaultType array
     *
     * @param json,array,text
     *
     * @return
     */
    public function Getir($type="array") {
        if($type == "json") {

            return json_encode($this->verilerArray);

        }else if($type == "text") {
            $metin="";
            foreach( $this->verilerArray as $veri) {
                $metin.=$veri["eczaneAdi"]."|".$veri["eczaneAdres"]."|".$veri['eczaneTelefon']."==";
            }
            return $metin;
        }else {
            return $this->verilerArray;
        }


    }





    /**
     *
     * @param String $s
     *
     * @return $s
     */
    private function EnCevir($s) {
        $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
        $eng = array('s','s','i','i','g','g','u','u','o','o','c','c');
        $s = str_replace($tr,$eng,$s);
        $s = strtolower($s);
        $s = preg_replace('/&.+?;/', '', $s);
        $s = preg_replace('/[^%a-z0-9 _-]/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('|-+|', '-', $s);
        $s = trim($s, '-');

        return $s;
    }






}

?>
