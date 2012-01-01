<?php
				require_once("wp-load.php");
				global $wpdb;
				if(isset($_GET["asin"]) && isset($_GET["link"])&& isset($_GET["trackingid"]))
				{
					$awsCountryCode = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
					$keyid = KEYID;
					$assoctag = $_GET["trackingid"];
					$title = str_replace("-"," ",$_GET["title"]);
					$time = current_time('mysql');
					$query = "INSERT INTO reviewazon_reports (ASIN, Tracking_ID, Title, Country, IP_Address, Click_Date) VALUES ('{$wpdb->escape($_GET["asin"])}', '{$wpdb->escape($_GET["trackingid"])}', '{$wpdb->escape($title)}', '','{$wpdb->escape($_SERVER["REMOTE_ADDR"])}','{$time}')";
					
					$wpdb->query($query);						
					
					switch($_GET["link"])
					{
						case "techdetails":
							$link = "http://www.amazon.{$awsCountryCode}/dp/tech-data/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "baby":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/baby/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "wedding":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/wedding/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "wishlist":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/wishlist/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "taf":
							$link = "http://www.amazon.{$awsCountryCode}/gp/pdp/taf/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "custreview":
							$link = "http://www.amazon.{$awsCountryCode}/review/product/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "offers":
							$link = "http://www.amazon.{$awsCountryCode}/gp/offer-listing/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "product":
							$link = "http://www.amazon.{$awsCountryCode}/dp/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3D{$_GET["asin"]}";
							break;
					}
					header( "Location: {$link}");
				
				}
				?>