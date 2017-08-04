<?php
$dbip   = "host";
$dbid   = "id";
$dbpass = "pw";

$db = mysql_connect($dbip, $dbid, $dbpass) or die(mysql_error());
mysql_select_db("db", $db) or die(mysql_error());
mysql_query("set character_set_client='utf8'");
mysql_query("set character_set_results='utf8'");
mysql_query("set collation_connection='utf8_general_ci'");

if ($mybb->usergroup['showforumteam'] == 1) {
    $yetkiAraclari = "
  <ul class='yetkiliAraclari'>
  	<h1>Yetkili Araçları</h1>
  	<li><a href='#' class='duzenle'><i class='fa fa-pencil'></i> Cezayı Düzenle</a></li>
  </ul>";
    if ($mybb->user['usergroup'] == "8" OR $mybb->user['usergroup'] == "4") {
        $yoneticiAraclari = "
    <ul class='yetkiliAraclari'>
    	<h1>Yönetici Araçları</h1>
    	<li><a href='#' class='mute' original-title='Mikrofon Yasağı'><i class='fa fa-microphone'></i> Mute</a></li>
    	<li><a href='#' class='gag' original-title='Yazı Yasağı'><i class='fa fa-comment'></i> Gag</a></li>
    	<li><a href='#' class='undo'><i class='fa fa-undo'></i> Kaldır</a></li>
    	<li><a href='#' class='sil'><i class='fa fa-trash'></i> Sil</a></li>
    </ul>";
        $yoneticiiparat   = "<li><a href='#' class='aratip' original-title='Bu ban ile aynı olan IP kayıtlarını gösterir.'><i class='fa fa-globe'></i> IP'ye Göre Arat</a></li>";
    }
}


$ing_aylar  = array(
    "January",
    "February",
    "March",
    "May",
    "April",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
);
$tr_aylar   = array(
    "Ocak",
    "Şubat",
    "Mart",
    "Nisan",
    "Mayıs",
    "Haziran",
    "Temmuz",
    "Ağustos",
    "Eylül",
    "Ekim",
    "Kasım",
    "Aralık"
);
$ing_gunler = array(
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday"
);
$tr_gunler  = array(
    "Pazartesi",
    "Salı",
    "Çarşamba",
    "Perşembe",
    "Cuma",
    "Cumartesi",
    "Pazar"
);

/* SAYFALAMA */
if (isset($_GET["s"])) {
    $s = $_GET["s"];
} else {
    $s = 1;
}

function sayfalama()
{
    global $s, $sayfaSayisi;
    $sayfalamaArray = array(
        '/\?s=[0-9,]/',
        '/\&s=[0-9,]/'
    );
    echo '<div class="cezaSayfalama">';
    if ($s > 1) {
        echo '<a class="linksayi solbas" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=1" original-title="En Başa Dön"><i class="fa fa-step-backward" aria-hidden="true"></i></a>';
    } else {
        echo '<span class="delink linksayi solbas"><i class="fa fa-step-backward" aria-hidden="true" style="opacity:0"></i></span>';
    }
    if ($s <= $sayfaSayisi) {
        if ($s > 3) {
            echo '<a class="linksayi" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s - 3) . '">' . ($s - 3) . '</a>';
        } else {
            echo '<span class="delink linksayi" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s - 3) . '"> </span>';
        }
        if ($s > 2) {
            echo '<a class="linksayi" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s - 2) . '">' . ($s - 2) . '</a>';
        } else {
            echo '<span class="delink linksayi" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s - 2) . '"> </span>';
        }
        if ($s > 1) {
            echo '<a href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s - 1) . '" original-title="Önceki Sayfa"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
        } else {
            echo '<span class="delink" original-title="Önceki sayfa yok"><i class="fa fa-angle-left" aria-hidden="true"></i></span>';
        }
    }
    echo '<select onchange="location = this.value;">';
    if ($sayfaSayisi > 1) {
        for ($i = 1; $i <= $sayfaSayisi; $i++) {
            echo '<option value="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . $i . '"';
            if ($s == $i) {
                echo ' selected>' . $i;
            } else {
                echo '>' . $i;
            }
            echo '</option>' . $i . '</a>';
        }
    }
    echo '</select>';
    if ($s <= $sayfaSayisi) {
        if ($s < $sayfaSayisi) {
            echo '<a href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s + 1) . '" original-title="Sonraki Sayfa"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
        } else {
            echo '<span class="delink" original-title="Sonraki sayfa yok"><i class="fa fa-angle-right" aria-hidden="true"></i></span>';
        }
        if ($s < $sayfaSayisi - 1) {
            echo '<a class="linksayi linksayisag" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s + 2) . '">' . ($s + 2) . '</a>';
        } else {
            echo '<span class="delink linksayi linksayisag" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s + 2) . '"> </span>';
        }
        if ($s < $sayfaSayisi - 2) {
            echo '<a class="linksayi linksayisag" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s + 3) . '">' . ($s + 3) . '</a>';
        } else {
            echo '<span class="delink linksayi linksayisag" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . ($s + 3) . '"> </span>';
        }
    }
    
    if ($s < $sayfaSayisi) {
        echo '<a class="linksayi linksayisag solbas" href="' . preg_replace($sayfalamaArray, '', $_SERVER['REQUEST_URI']) . '&s=' . $sayfaSayisi . '" original-title="En Sona Git"><i class="fa fa-step-forward" aria-hidden="true"></i></a>';
    } else {
        echo '<span class="delink linksayi linksayisag"> </span>';
    }
    echo '</div>';
    
}
/* SAYFALAMA SON */

/* ARAMA */
if (isset($_GET['arama'])) {
    if (isset($_GET['aramaCesit'])) {
        switch ($_GET['aramaCesit']) {
            case 'isimArat':
                $islev = $_GET['isimArat'];
                $arama = " WHERE name LIKE '%" . $islev . "%' ";
                break;
            case 'steamArat':
                $islev = $_GET['steamArat'];
                $arama = " WHERE authid LIKE '" . $islev . "' ";
                break;
            case 'ipArat':
                $islev = $_GET['ipArat'];
                $arama = " WHERE ip LIKE '" . $islev . "' ";
                break;
            case 'sebepArat':
                $islev = $_GET['sebepArat'];
                $arama = " WHERE reason LIKE '%" . $islev . "%' ";
                break;
            case 'serverArat':
                $islev = $_GET['serverArat'];
                $arama = " WHERE sid LIKE '" . $islev . "' ";
                break;
            case 'sureArat':
                $islev  = $_GET['sureArat'];
                $islev2 = $_GET['sureArat2'];
                switch ($_GET['sureArat2']) {
                    case 'esit':
                        $islev2islem = "=";
                        break;
                    case 'buyuk':
                        $islev2islem = "<";
                        break;
                    case 'kucuk':
                        $islev2islem = ">";
                        break;
                    case 'buyukesit':
                        $islev2islem = "<=";
                        break;
                    case 'kucukesit':
                        $islev2islem = ">=";
                        break;
                }
                $arama = " WHERE length " . $islev2islem . " " . $islev . " ";
                break;
        }
    }
}
/* ARAMA SON */

/* SUNUCU LİSTESİ */
function serverListesi()
{
    global $banyedigiserver;
    $banserver = "Site üzerinden atıldı";
    $qServer   = mysql_query("SELECT sid,ip,port,svAdi FROM sb_servers WHERE sid LIKE '$banyedigiserver'");
    $svTag     = "<i class=\"fa fa-server console\" style=\"padding-right:4.5px\"></i>";
    while ($row = mysql_fetch_assoc($qServer)) {
        $yCek[]     = $row;
        $temizSvAdi = str_replace("TF2 Turkiye # ", "", $row['svAdi']);
        $banserver  = $svTag . $temizSvAdi;
    }
    return array(
        $banserver
    );
}
/* SUNUCU LİSTESİ SON */

function serverListesiArama()
{
    $qServer = mysql_query("SELECT sid,ip,port,svAdi FROM sb_servers");
    while ($row = mysql_fetch_array($qServer)) {
        if ($row['sid'] == 22) {
        } else {
            echo "<option value='" . $row['sid'] . "'>" . $row['svAdi'] . "</option>";
        }
    }
}

function sayfaSuresi()
{
    global $kayitSayisi, $sayfaSayisi;
    $zaman  = microtime();
    $zaman  = explode(" ", $zaman);
    $zaman  = $zaman[1] + $zaman[0];
    $baslat = $zaman;
    
    $zaman      = microtime();
    $zaman      = explode(" ", $zaman);
    $zaman      = $zaman[1] + $zaman[0];
    $bitis      = $zaman;
    $toplamsure = ($bitis - $baslat);
    printf('<div class="sayfalamaSutun" original-title="Sayfa yükleme süresi"><i class="fa fa-spinner" aria-hidden="true"></i> %f Saniye</div>', $toplamsure);
    
    if ($kayitSayisi > 999) {
        $sonkayitSayisi = number_format(str_replace(',', '', $kayitSayisi));
    } else {
        $sonkayitSayisi = $kayitSayisi;
    }
    
    echo '<div class="sayfalamaSutun" original-title="Toplam veritabanı bilgisi"><i class="fa fa-database" aria-hidden="true"></i> ' . $sonkayitSayisi . ' Sonuç - ' . $sayfaSayisi . ' Sayfa</div>';
}

function cezaSebebiListeleme()
{
    $qServer = mysql_query("SELECT id,sebep FROM sb_bansebebi");
    while ($row = mysql_fetch_array($qServer)) {
        echo '<option value="' . $row['id'] . '">' . $row['sebep'] . '</option>';
    }
}
