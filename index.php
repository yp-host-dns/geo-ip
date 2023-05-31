<?php
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	
	function domain_2_ip($domain = ""){
		if (filter_var($domain, FILTER_VALIDATE_IP)) {
			return $domain;
		}else{
			$ip = gethostbyname($domain);
			if (!filter_var($ip, FILTER_VALIDATE_IP)) {
				return false;
			}else{
				return $ip;
			}
		}
	}

	$whitelist = array(
		'127.0.0.1',
		'::1'
	);
	$data_error = false;
	
	$is_post = false;
	$is_domain = false;
	
	if( isset($_POST['host']) && $_POST['host'] != "")
	{
		$ip = domain_2_ip($_POST['host']);
		
		if($ip==false){
			$ip = $_POST['host'];
			$data_error = true;
		}else{
			if($ip!=$_POST['host']){
				$domain = $_POST['host'];
				$is_domain = true;
			}
		}
		
		$is_post = true;
	}else{
		if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
			$ip = $_SERVER['REMOTE_ADDR'];
		}else{
			$ip = '';
		}
	}

	if($data_error==false){		
		$data = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip) );
		$ip = $data['geoplugin_request'];	
	}

	$google_maps_api_key = "AIzaSyBGBCK9FH4o_0trP8dBHNS9P0bZ5xP2rCQ";
	$zoom_level = 15;

?>

<html>
	<head>
		<title>IP Location Finder | IPLocation.Ga</title>
		<meta name="description" content="IP Location Finder - Find a geolocation of an IP address including latitude, longitude, city, region and country.">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" href="img/favicon32.png">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=<?php echo $google_maps_api_key; ?>"></script>
		<script type="text/javascript">			
			function initialize() {
				<?php if(!$data_error){	?>
				var latitude = <?php echo $data['geoplugin_latitude']; ?>;
				var longitude = <?php echo $data['geoplugin_longitude']; ?>;
				var myLatlng = new google.maps.LatLng(latitude,longitude);
				var myOptions = {
					zoom: <?php echo $zoom_level; ?>,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}

				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				var marker = new google.maps.Marker({
					position: myLatlng,
					map: map,
					title:"IP Location!"
				});
				<?php } ?>
			}
		</script>		
		<style>
			#IP{	
				font-size: 30px;
			}
			#IP span{
				color:#002bff;
			}
			.textlinks{
				margin-top:10px;
				margin-bottom:10px;
			}
			form.lookup input[type=text] {
			  padding: 10px;
			  font-size: 17px;
			  border: 1px solid grey;
			  float: left;
			  width: 80%;
			  background: #f1f1f1;
			}
			form.lookup button {
			  float: left;
			  width: 20%;
			  padding: 10px;
			  background: #2196F3;
			  color: white;
			  font-size: 17px;
			  border: 1px solid grey;
			  border-left: none;
			  cursor: pointer;
			}
			form.lookup button:hover {
			  background: #0b7dda;
			}
			form.lookup::after {
			  content: "";
			  clear: both;
			  display: table;
			}
			.vpn-aff-link{
				margin-bottom: 10px;
			}
			.vpn-aff-link button{
				width:100%;
			}
		</style>
	</head>
	<body onload="initialize()">

		<div id="main" class="container">

			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="header text-center">
							<a href="/"><h1 class="title">IP Location Finder</h1></a>
							<p class="description">Find a geolocation of an IP address including latitude, longitude, city, region and country.</p>
						</div>		
					</div>	
				</div>	

				<div class="row">
					<div class="col-sm-12">
					<?php
					if(!$data_error){
						if($is_domain){
					?>
						<h1 class="text-center" id="IP">"<?php echo $domain; ?>" has IP address: <span><?php echo $data['geoplugin_request']; ?><span></h1>
					<?php
						}else{
							if($is_post){ ?>
								<h1 class="text-center" id="IP">Geolocation for IP Address: <span><?php echo $ip; ?><span></h1>
							<?php }else{ ?>
								<h1 class="text-center" id="IP">Your IP Address: <span><?php echo $ip; ?><span></h1>
					<?php
							}
						}					
					}
					?>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<form id="lookup-form" class="lookup" action="" method="post">
						  <input type="text" placeholder="IP address or Domain name" name="host">
						  <button type="submit" onClick="location.href=''">CHECK</button>
						</form>
					</div>
				</div>
				
			  <div class="row">
				<div class="col-sm-8">
				<?php
					if($data_error){
				?>

					<div class="alert alert-danger">
					  <strong>Error:</strong> <?php echo $ip; ?> does not exist.
					</div>
				<?php
					}
					else{
				?>

				  <table class="table table-striped">
					<tbody>
					
					  <tr>
						<td>Country Name</td>
						<td><?php echo $data['geoplugin_countryName']; ?></td>
					  </tr>
					  
					  <tr>
						<td>Country Code</td>
						<td><?php echo $data['geoplugin_countryCode']; ?></td>
					  </tr>

					  <tr>
						<td>Region</td>
						<td><?php echo $data['geoplugin_regionName']; ?></td>
					  </tr>

					<tr>
						<td>Region Code</td>
						<td><?php echo $data['geoplugin_regionCode']; ?></td>
					  </tr>

					  <tr>
						<td>City</td>
						<td><?php echo $data['geoplugin_city'];; ?></td>
					  </tr>

					  <tr>
						<td>Continent</td>
						<td><?php echo $data['geoplugin_continentName']; ?></td>
					  </tr>
					  
					  <tr>
						<td>Continent Code</td>
						<td><?php echo $data['geoplugin_continentCode']; ?></td>
					  </tr>

					  <tr>
						<td>Time Zone</td>
						<td><?php echo $data['geoplugin_timezone']; ?></td>
					  </tr>

					  <tr>
						<td>Latitude</td>
						<td><?php echo $data['geoplugin_latitude']; ?></td>
					  </tr>

					  <tr>
						<td>Longitude</td>
						<td><?php echo $data['geoplugin_longitude']; ?></td>
					  </tr>
					
					  <tr>
						<td>Currency Code</td>
						<td><?php echo $data['geoplugin_currencyCode']; ?></td>
					  </tr>

					  <tr>
						<td>Currency Symbol</td>
						<td><?php echo $data['geoplugin_currencySymbol']; ?></td>
					  </tr>
					  
					</tbody>
				  </table>

				<?php } ?>

				</div>

				<div class="col-sm-4">
					<center>
						<a href="https://affiliate.hide-my-ip.com//proxy.php?id=3960_0_1_11" target="_blank"><img style="border:0px" src="https://affiliate.hide-my-ip.com//media/banners/336-black.png" width="336" height="280" alt=""></a>
					</center>
				</div>

			  </div>
			  
			<?php
				if(!$data_error){
			?>
			  <div class="row">
				<div class="col-sm-12"><div id="map_canvas" style="height:500px;width:100%;"></div></div>
			  </div>
			<?php
				}
			?>
			
			<div class="row">
				
				<div class="col-sm-12 textlinks text-center">
					<a href="https://affiliate.hide-my-ip.com//proxy.php?id=3960_0_1_16" target="_blank"><img style="border:0px" src="https://affiliate.hide-my-ip.com//media/banners/728light.png" width="728" height="90" alt=""></a>
				</div>
				
				<div class="col-sm-12">
					<hr>
					<h2>Your Information</h2>
					<div class="panel panel-default">
						<div class="panel-body">					
							<fieldset>
								<legend>Host Name & User Agent</legend>
								<p><label>Your Host Name:</label> <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
								<p><label>Your User Agent:</label> <?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
							</fieldset>
						</div>
					</div>
					
					<p>Surf anonymously, prevent hackers from acquiring your IP address, send anonymous email, and encrypt your Internet connection. Protect your online privacy by changing your IP.</p>
					<p>We've reviewed and recommend the following best services to make your IP information private:</p>
				</div>
				
				<div class="col-sm-4">
					<div class="vpn-aff-link"><a href="https://affiliate.hide-my-ip.com//proxy.php?id=3960" target="_blank"><button type="button" class="btn btn-default">Hide My IP</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/adguard-premium-lifetime-subscription-4?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">Adguard Premium</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/hidemyass-vpn-2-year-subscription-3?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">Hide My Ass</button></a></div>
				</div>
				
				<div class="col-sm-4">
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/cyberghost-vpn-lifetime-subscription?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">CyberGhost VPN</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/vpn-unlimited-lifetime-subscription?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">VPN Unlimited</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/tigervpn-lifetime-subscription-3?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">TigerVPN</button></a></div>
				</div>
				
				<div class="col-sm-4">
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/windscribe-vpn?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">Windscribe VPN</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/fastestvpn-lifetime-subscription?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">FastestVPN</button></a></div>
					<div class="vpn-aff-link"><a href="https://stacksocial.com/sales/vpnsecure-lifetime-3?aid=a-v18hvwhm" target="_blank"><button type="button" class="btn btn-default">VPNSecure</button></a></div>
				</div>
				
			</div>
			
			<div class="row textlinks">
				<div class="col-sm-12 text-center">
					<a href="https://affiliate.hide-my-ip.com//proxy.php?id=3960_0_1_15" target="_blank"><img style="border:0px" src="https://affiliate.hide-my-ip.com//media/banners/728black.png" width="728" height="90" alt=""></a>
				</div>
			  </div>
			
				<div class="row">
					<div class="col-sm-12">
						<hr>
						<p class="copyright text-center">
							Copyright &copy <?php echo date('Y'); ?> by <a href="http://www.shopifytips.com" target="_blank">Shopify Tips</a>. All Rights Reserved.<br>
							Developed and Designed by <a href="http://www.anhkiet.info"  target="_blank">Huynh Mai Anh Kiet</a>.
						</p>
					</div>
				</div>

			</div>

		</div>				

	</body>

</html>

