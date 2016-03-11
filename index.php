<?php

	function getFileList($dir) {
    // array to hold return value
    $retval = array();

    // add trailing slash if missing
    if(substr($dir, -1) != "/") $dir .= "/";

    // open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
      // skip hidden files
      if($entry[0] == ".") continue;
      if(is_dir("$dir$entry")) {
        $retval[] = array(
          "name" => "$dir$entry/",
          "type" => filetype("$dir$entry"),
          "size" => 0,
          "lastmod" => filemtime("$dir$entry")
        );
      } elseif(is_readable("$dir$entry")) {
        $retval[] = array(
          "name" => "$dir$entry",
          "type" => mime_content_type("$dir$entry"),
          "size" => filesize("$dir$entry"),
          "lastmod" => filemtime("$dir$entry")
        );
      }
    }
    $d->close();

    return $retval;
  }

// paske atencji
if(isset($_POST['atencja'])) {
	$atencja = trim($_POST['atencja']);

	$file = 'paske_atencji.txt';
	$dane = $atencja;
	$fp = fopen($file, 'w');
	flock($fp, 2);
	fwrite($fp, $dane);
	flock($fp, 3);
	fclose($fp);
}

$attenform = file_get_contents("paske_atencji.txt");

// wyrenderuj mi tu paske
$attenform_html = '<form method="post" action="" id="attenform" style="display: none; visibility: hidden;">
<input name="atencja" style="width: 100%;" value="'.$attenform.'" type="text">
</form>';
$attenform_html .= '<div style="height: 1.5em; width: 100%; text-align: center;" onclick=\'document.getElementById("atencja").style.visibility = "hidden";document.getElementById("atencja").style.display = "none";document.getElementById("attenform").style.visibility = "visible";document.getElementById("attenform").style.display = "block";\' id="atencja">'.htmlspecialchars($attenform).'</div>';

$muzyczki = getFileList('audio');
$tla = getFileList('back');
?>

<!DOCTYPE html>
<html lang="pl">

	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />

		<title>Tomasz Terka - Wielkie odliczanie trwa</title>

		<style>

		@import url(https://fonts.googleapis.com/css?family=Oswald);

		body {
			font-family: 'Oswald', sans-serif;
			color: #fff;
			text-shadow: 1px 2px 2px #000;
			margin: 0;
		}

		div.overlay {
			position: fixed;
			top: 50%;
			left: 50%;
			min-width: 100%;
			min-height: 100%;
			width: auto;
			height: auto;
			transform: translateX(-50%) translateY(-50%);
			background-color: rgba(0, 0, 0, 0.7);
		}

		video {
			position: fixed;
			top: 50%;
			left: 50%;
			min-width: 100%;
			min-height: 100%;
			width: auto;
			height: auto;
			z-index: -100;
			transform: translateX(-50%) translateY(-50%);
			background-size: cover;
			transition: 1s opacity;
		}

		div.content {
			position: absolute;
			top: 30%;
			width: 100%;
			text-align: center;
		}

		h1 {
			font-size: 64px;
		}

		p {
			font-size: 24px;
		}

		p.countdown {
			font-size: 32px;
		}

		p.muted {
			font-size: 12px;
		}

		div.muzyka {
			position: absolute;
			width: 210px;
			top: 0;
			left: 0;
			background-color: rgba(0, 0, 0, 0.6);
		}

		div.tlo {
			position: absolute;
			width: 210px;
			top: 0;
			right: 0;
			background-color: rgba(0, 0, 0, 0.6);
		}

		div.muzyka > p,
		div.tlo > p {
			text-align: center;
		}

		ul {
			list-style-type: decimal;
		}

		a {
			color: #fff;
			text-decoration: none;
		}

		a:hover,
		a.active {
			color: #55ff85;
		}

		</style>
	</head>

	<body>
		<audio id="muzyka" src="<?php echo '/' . $muzyczki[0]['name']; ?>" loop autoplay></audio>

		<video autoplay id="tlo" src="<?php echo '/' . $tla[0]['name']; ?>" loop muted></video>

		<div class="overlay"></div>

		<div class="muzyka">
			<p>Wybierz utwór</p>
			<ul>
				<?php
					for($i=0; $i<sizeof($muzyczki); $i++) {
						echo '<li><a class="zmien-muzyke' . ($i == 0 ? ' active' : '') . '" plik="/' . $muzyczki[$i]['name'] . '" href="#">' . str_replace('.mp3', '', str_replace('audio/', '', $muzyczki[$i]['name'])) . '</a></li>';
					}
				?>
			</ul>
		</div>

		<div class="tlo">
			<p>Wybierz tło</p>
			<ul>
				<?php
					for($i=0; $i<sizeof($muzyczki); $i++) {
						echo '<li><a class="zmien-tlo' . ($i == 0 ? ' active' : '') . '" plik="/' . $tla[$i]['name'] . '" href="#">' . str_replace('.mp4', '', str_replace('back/', '', $tla[$i]['name'])) . '</a></li>';
					}
				?>
			</ul>
		</div>

		<div class="content">
			<?php echo $attenform_html; ?>

			<h1>Tomasz Terka</h1>

			<p>Do czarodzieja pozostało:</p>
			<p class="countdown"><span id="days"></span> dni, <span id="hours"></span>:<span id="minutes"></span>:<span id="seconds"></span></p>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script>
		var czarodziej = new Date(1464912000000); //czarodziej terki

		function getTimeRemaining(endtime){
		  var t = Date.parse(endtime) - Date.parse(new Date());
		  var seconds = Math.floor( (t/1000) % 60 );
		  var minutes = Math.floor( (t/1000/60) % 60 );
		  var hours = Math.floor( (t/(1000*60*60)) % 24 );
		  var days = Math.floor( t/(1000*60*60*24) );
		  return {
		    'total': t,
		    'days': days,
		    'hours': hours,
		    'minutes': minutes,
		    'seconds': seconds
		  };
		}

		setInterval(function() {
			var left = getTimeRemaining(czarodziej);

			$('#days').text(left.days);
			$('#hours').text((left.hours < 10 ? '0' : '') + left.hours);
			$('#minutes').text((left.minutes < 10 ? '0' : '') + left.minutes);
			$('#seconds').text((left.seconds < 10 ? '0' : '') + left.seconds);
		});

		$('.zmien-muzyke').click(function() {
			var target = $(this);

			var muzyczka = target.attr('plik');
			$('#muzyka').attr({
				src: muzyczka
			});

			$('.zmien-muzyke').removeClass('active');
			target.addClass('active');
		});

		$('.zmien-tlo').click(function() {
			var target = $(this);

			var tlo = target.attr('plik');
			$('#tlo').attr({
				src: tlo
			});

			$('.zmien-tlo').removeClass('active');
			target.addClass('active');
		});
		</script>
	</body>

</html>
