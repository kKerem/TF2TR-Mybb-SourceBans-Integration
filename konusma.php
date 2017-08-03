<?php
define(IN_MYBB, 1);
require "../../global.php";

require "../inc/config.php";

$sayfaadi = "Ceza Listesi (Beta)";
$Sayfa    = $sayfaadi . " :: " . $mybb->settings['homename'];
add_breadcrumb($sayfaadi, 'http://tf2turkiye.net/ceza');
add_breadcrumb("Konuşma Cezası Kayıtları", 'http://tf2turkiye.net/ceza/konusma');

echo '<link href="http://tf2turkiye.net/cache/themes/theme17/font/flag-icon.min.css" rel="stylesheet" type="text/css" />';
echo $headerinclude;
echo "<title>$Sayfa</title>";
echo $header;
echo $headernav;
eval("\$headernav = \"" . $templates->get("headernav") . "\";");
output_page($headernav);

date_default_timezone_set('Europe/Istanbul');

$q = mysql_query("SELECT * FROM sb_comms $arama");
mysql_query("SET NAMES 'utf8'");
$limit       = 30;
$kayitSayisi = mysql_num_rows($q);
$sayfaSayisi = ceil($kayitSayisi / $limit);
$baslangic   = ($s * $limit) - $limit;
?>

</navigation>

<div class="nav2govde">
	<navigation class="nav2">
		<fieldset class="breadcrumb">	<span class="crumbs" original-title="">
			<span class="crust">
				<a href="../konusma" class="crumb navAktifDegil" original-title="Konuşma Yasakları Listesi"><i class="fa fa-commenting" aria-hidden="true"></i>
				<span class="arrow" original-title="">
					<span class="navAktifDegilS" original-title="">&gt;</span>
				</span>
				</a>
			</span>
		</fieldset>
	</navigation>
</div>

	<table class="tborder cezaTablo">
		<tr>
			<th>Tür / Tarih</th>
			<th>Oyuncu</th>
			<th>Banlayan Yetkili</th>
			<th>Ceza Sebebi</th>
			<th>Kalan Süre</th>
		</tr>

<?php
$q = mysql_query("SELECT * FROM sb_comms $arama ORDER BY bid DESC LIMIT $baslangic,$limit");
mysql_query("SET NAMES 'utf8'");

date_default_timezone_set('Europe/Istanbul');
while ($y = mysql_fetch_array($q)) {
    /* STEAMID32 TO 64 SON */
    $steamid    = $y['authid'];
    $id         = explode(":", $steamid);
    $authserver = $id[1];
    $steamid64  = $id[2];
    $steamid64  = $steamid64 * 2;
    $steamid64  = bcadd($steamid64, 61197960265728);
    if ($authserver == 1) {
        $steamid64 = $steamid64 + 1;
    }
    ;
    $steamid64 = "765$steamid64";
    /* STEAMID32 TO 64 SON */
    
    /* STEAMID32 TO STEAMID3 */
    $data['steamid'] = $y['authid'];
    $steam2id        = $data['steamid'];
    $steam3parts     = explode(':', $steam2id);
    $steamid3        = '[U:1:' . ($steam3parts[2] * 2 + $steam3parts[1]) . ']';
    /* STEAMID32 TO STEAMID3 SON */
    
    $cezaTarihi = $y['created'];
    $tarihorj   = date('d.m.Y - H:i', $cezaTarihi);
    $tarih      = date('d F Y - H:i', $cezaTarihi);
    $tarih      = str_replace($ing_aylar, $tr_aylar, $tarih);
    $tarih      = str_replace($ing_gunler, $tr_gunler, $tarih);
    
    if ($cezaTarihi >= strtotime("today"))
        $tarih = "Bugün, " . date('H:i', $cezaTarihi);
    else if ($cezaTarihi >= strtotime("yesterday"))
        $tarih = "Dün, " . date('H:i', $cezaTarihi);
    
    if (empty($y['name'])) {
        $banlanan = "<i>isim mevcut değil</i>";
    } else {
        $banlanan = $y['name'];
    }
    /* Banlayan Yetkili AID'sini ALIP İSME ÇEVİRME */
    $banlayanYetkili = $y['aid'];
    $qYetkili        = mysql_query("SELECT aid,user,srv_group FROM sb_admins WHERE aid LIKE '$banlayanYetkili'");
    if ($qYetkiliSonuc = mysql_num_rows($qYetkili)) {
        while ($row = mysql_fetch_assoc($qYetkili)) {
            $yCek[] = $row;
            switch ($row['srv_group']) {
                case 'ServerSahibi':
                    $rutbe = "<i class='fa fa-bolt sunucuSahibi' original-title='Sunucu Sahibi'></i>";
                    break;
                case 'Yönetici':
                    $rutbe = "<i class='fa fa-gavel yonetici' original-title='Yönetici'></i>";
                    break;
                case 'Admin':
                    $rutbe = "<i class='fa fa-balance-scale admin' original-title='Admin'></i>";
                    break;
                case 'Ultra Admin':
                    $rutbe = "<i class='fa fa-balance-scale admin' original-title='Admin'></i>";
                    break;
                default:
                    if ($row['aid'] != 0 && empty($row['srv_group'])) {
                        $rutbe = "<i class='fa icon-user-delete silinmis' original-title='Eski Yetkili'></i>";
                    } else {
                        $rutbe = "<i class='fa fa-server console' original-title='Sunucu'></i>";
                    }
                    break;
            }
        }
        foreach ($yCek as $yetkili) {
            $yetkili = $rutbe . $yetkili[user];
        }
    } else {
        $yetkili = "<span original-title='İsim Silinmiş'><i class='fa icon-user-delete silinmis'></i> Eski Yetkili</span>";
    }
    /* SON Banlayan Yetkili AID'sini ALIP İSME ÇEVİRME */
    
    $sebeparray = array(
        "[2 SAAT]",
        "[4 SAAT]",
        "[1 GÜN]",
        "[3 GÜN]"
    );
    $sebep      = str_replace($sebeparray, "", $y['reason']);
    if (empty($sebep)) {
        $sebep = "Sebep girilmemiş.";
    }
    
    $dkcevir       = $y['length'];
    $tarihorjbitis = date('d.m.Y - H:i', $cezaTarihi + $dkcevir);
    $cezasuresi    = $y['length'];
    if (!empty($y['length'])) {
        switch ($y['RemoveType']) {
            case 'E':
                $kalansure  = " class='doldu'><span><i class='demo-icon icon-check'></i>Süresi Doldu</span>";
                $cezadurumu = "<i class=\"fa fa-clock-o\"></i>Süresi doldu";
                break;
            case 'U':
                $kalansure  = " class='kaldirildi'><span><i class=\"fa fa-undo\"></i> Kaldırıldı</span>";
                $cezadurumu = "<i class=\"fa fa-undo\"></i>Ceza Kaldırıldı";
                break;
            default:
                $cezatarih = date('d F Y H:i', $cezaTarihi);
                $kalanson  = date('d F Y H:i:s', $cezaTarihi + $dkcevir);
                
                $now         = new DateTime();
                $future_date = new DateTime($kalanson);
                $interval    = $future_date->diff($now);
                $kalanson    = $interval->format("%m ay, %a gün, %h saat, %i dk");
                
                $sifir      = array(
                    "0 yıl, ",
                    "0 ay, ",
                    "0 gün, ",
                    "0 saat, "
                );
                $kalansure  = ' class="bandevam"><span><i class="demo-icon icon-back-in-time"></i>' . str_replace($sifir, "", $kalanson . '</span>');
                $cezadurumu = "<i class=\"fa fa-circle-o-notch\"></i>Devam Ediyor";
                break;
        }
    } else {
        if (!$y['RemoveType'] == "E") {
            $kalansure = " class='kalici'><span><i class='fa fa-times'></i> Kalıcı Ceza</span>";
        } else {
            $kalansure = " class='kaldirildi'><span><i class=\"fa fa-undo\"></i> Kaldırıldı</span>";
        }
        $tarihorjbitis = "Bulunmuyor";
        $cezadurumu    = "<i class=\"fa fa-ban\"></i>Kalıcı ceza";
    }
    
    if ($y['ip'] != "") {
        if ($mybb->usergroup['showforumteam'] == 1) {
            $kip = $y['ip'];
        } else {
            $kip = "Gizli";
        }
        $bolgeip = "<span class=\"flag-icon flag-icon-" . $y['country'] . "\"></span><span original-title=\"IP'ler oyuncuların gizliliği için gösterilmez.\">" . $kip . "</span>";
    } else {
        $bolgeip = "Bilgi girilmemiş.";
    }
    
    $banyedigiserver = $y['sid'];
    
    $serverListesiFunct = serverListesi();
    
    
    
    /* Ban Kaldıran Yetkili AID'sini ALIP İSME ÇEVİRME */
    $kaldiranYetkili = $y['RemovedBy'];
    if ($kaldiranYetkili != "") {
        $qYetkili = mysql_query("SELECT aid,user,srv_group FROM sb_admins WHERE aid LIKE '$kaldiranYetkili'");
        while ($row = mysql_fetch_assoc($qYetkili)) {
            $yCek[] = $row;
            switch ($row[srv_group]) {
                case 'ServerSahibi':
                    $rutbe = "<i class='fa fa-bolt sunucuSahibi' original-title='Sunucu Sahibi'></i>";
                    break;
                case 'Yönetici':
                    $rutbe = "<i class='fa fa-gavel yonetici' original-title='Yönetici'></i>";
                    break;
                case 'Admin':
                    $rutbe = "<i class='fa fa-balance-scale admin' original-title='Admin'></i>";
                    break;
                case 'Ultra Admin':
                    $rutbe = "<i class='fa fa-balance-scale admin' original-title='Admin'></i>";
                    break;
                default:
                    $rutbe = "<i class='fa fa-server console' original-title='TF2 Turkiye' style='padding-right:4px'></i>";
                    break;
            }
        }
        foreach ($yCek as $cezakaldiranyetkili) {
            if ($cezakaldiranyetkili[user] == "CONSOLE") {
                $cezakaldiran = NULL;
            } else {
                $kaldirilmasebebi = $y['ureason'];
                if (empty($kaldirilmasebebi)) {
                    $kaldirilmasebebi = "Sebep girilmemiş.";
                }
                $cezakaldiran = "<tr><td>Cezayı Kaldıran</td><td>" . $rutbe . $cezakaldiranyetkili[user] . "</td></tr>
						<tr><td>Kaldırılma Sebebi</td><td>" . $kaldirilmasebebi . "</td>
						</tr>";
            }
        }
    } else {
        $cezakaldiran = NULL;
    }
    
    /* SON Banlayan Yetkili AID'sini ALIP İSME ÇEVİRME */
    
    switch ($y['type']) {
        case '1':
            $tur = '<i class="fa fa-microphone-slash" aria-hidden="true" original-title="Mikrofon Yasağı"></i>';
            break;
        case '2':
            $tur = '<i class="fa fa-commenting" aria-hidden="true" original-title="Yazı yazma Yasağı"></i>';
            break;
    }
    
    echo "
		<tr>
		<td>" . $tur . $tarih . "</td>
	  <td>" . $banlanan . "
	  <td>" . $yetkili . "</td>
	  <td>" . mb_strimwidth($sebep, 0, 35, "...") . "</td>
	  <td" . $kalansure . "</td>
		</tr>
		<!-- SLIDE PANEL -->
  <tr class=\"acilirpanel\">
    <td colspan=\"5\">
      <div>";
    if ($mybb->user['uid']) {
    }
    echo "
			<ul class='yetkiliIsta'>
				<h1>Araştırma Araçları</h1>
				<li><a href='#' class='aratsteam' original-title='Bu hesapta yenen ceza kayıtlarını gösterir.'><i class='fa fa-steam'></i> Profile Göre Arat</a></li>
				<li><a href='#' class='aratisim' original-title='Bu nickte olanların kayıtlarını gösterir.'><i class='fa fa-address-card'></i> İsime Göre Arat</a></li>
				" . $yoneticiiparat . "
			</ul>
        <table>
					<tr>
						<th colspan=\"2\">Steam Bilgileri</th>
					</tr>
					<tr>
						<td>Bölge / IP</td>
						<td>" . $bolgeip . "</td>
					</tr>
					<tr>
						<td>Steam ID3</td>
						<td>" . $steamid3 . "</td>
					</tr>
					<tr>
						<td>Steam ID32</td>
						<td>" . $y['authid'] . "</td>
					</tr>
					<tr>
						<td>Steam ID64</td>
						<td>" . $steamid64 . "</td>
					</tr>
					<tr>
						<td>Profil Adresi</td>
						<td><a href=\"http://steamcommunity.com/profiles/" . $steamid64 . "\" target=\"_blank\" class=\"detaybilgilink\"><i class=\"fa fa-steam\"></i> Steam Profili</a></td>
					</tr>
				</table>
				<table>
					<tr>
						<th colspan=\"2\">Ceza Bilgileri</th>
					</tr>
					<tr>
						<td>Başlangıç Tarihi</td>
						<td>" . $tarihorj . $row['sid'] . "</td>
					</tr>
					<tr>
						<td>Ceza Bitiş Tarihi</td>
						<td>" . $tarihorjbitis . "</td>
					</tr>
					<tr>
						<td>Ceza Durumu</td>
						<td>" . $cezadurumu . "</td>
					</tr>
					<tr>
						<td>Ceza Yediği Sunucu</td>
						<td style=\"padding: 0 10px !important;\">" . $serverListesiFunct[0] . "</td>
					</tr>
					" . $cezakaldiran . "
					<tr>
						<td>SteamRep Profili</td>
						<td><a href=\"http://steamrep.com/profiles/" . $steamid64 . "\" target=\"_blank\" class=\"detaybilgilink detaybilgilinksteamrep\"><i class=\"fa fa-check-square fa-fw\"></i> Rep.tf Profili</a></td>
					</tr>
				</table>
				" . $yetkiAraclari . $yoneticiAraclari . "
      </div>
    </td>
  </tr>
  <!-- SLIDE PANEL END -->

	";
    
}

?>
</table>

<?php
sayfaSuresi();
?>

<div class="gelismisArama">
	<h1><i class="fa fa-arrow-down" aria-hidden="true"></i> Gelimiş Arama <i class="fa fa-arrow-down" aria-hidden="true"></i></h1>
	<form method="get" action="">
		<label for="isimarat" class="aramaform-label">
			<h2>Oyuncu Ismi</h2>
			<input type="radio" id="isimarat" name="aramaCesit" value="isimArat"/>
			<input type="text" id="isimArat" name="isimArat" />
		</label>

		<label for="steamarat" class="aramaform-label">
			<h2>SteamID</h2>
			<input type="radio" id="steamarat" name="aramaCesit" value="steamArat"/>
			<input type="text" id="Steam" name="steamArat" placeholder="Örnek: STEAM_0:0:52765951" />
		</label>

		<label for="iparat" class="aramaform-label">
			<h2>IP Adresi</h2>
			<input type="radio" id="ipmarat" name="aramaCesit" value="ipArat"/>
			<input type="text" id="Ip" name="ipArat" />
		</label>

		<label for="sebeparat" class="aramaform-label">
			<h2>Ceza Sebebi</h2>
			<input type="radio" id="sebepmarat" name="aramaCesit" value="sebepArat"/>
			<input type="text" id="Sebep" name="sebepArat" 	/>
		</label>

		<label for="serverarat" class="aramaform-label">
			<h2>Ceza Yediği Yer</h2>
			<input type="radio" id="serverarat" name="aramaCesit" value="serverArat"/>
			<select id="Server" name="serverArat">
				<?php
serverListesiArama();
?>
				<option value="0">Site Üzerinden</option>
			</select>
		</label>

		<label for="surearat" class="aramaform-label">
			<h2>Ceza Sebebi</h2>
			<input type="radio" id="surearat" name="aramaCesit" value="sureArat"/>
			<select id="Sure" name="sureArat2">
				<option value="esit" title="Eşit ise">=</option>
				<option value="buyuk" title="Büyük ise"><</option>
				<option value="kucuk" title="Küçük ise">></option>
				<option value="buyukesit" title="Eşit ve Büyük ise"><=</option>
				<option value="kucukesit" title="Eşit ve Küçük ise">>=</option>
			</select>
			<select id="Sure" name="sureArat">
				<option value="0" selected>Kalıcı</option>
				<option value="60">1 Saat</option>
				<option value="120">2 Saat</option>
				<option value="240">4 Saat</option>
				<option value="1440">1 Gün</option>
				<option value="4320">3 Gün</option>
				<option value="10080">1 Hafta</option>
				<option value="40320">1 Ay</option>
			</select>
		</label>

		<button type="submit" name="arama" value="Gonder"><i class="fa fa-search" aria-hidden="true"></i>  Arama Yap</button>
		<div class="gelismisAramaBilgi" original-title="Yani aynı anda hem oyuncu ismi hem SteamID aratamazsınız.">
			<i class="demo-icon icon-info-circled"></i> Her işlemde tek satırda yer alan değerler aratılabilir.
		</div>
	</form>
</div>
<?php
sayfalama();
echo $footer;
echo '<script type="text/javascript" src="http://tf2turkiye.net/ceza/inc/genel.js"></script>';
?>
