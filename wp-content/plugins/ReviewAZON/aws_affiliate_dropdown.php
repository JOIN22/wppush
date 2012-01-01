<?php
#########################################################################
#                                                                       #
#                   ReviewAZON 1.x Wordpress Plugin                     #
#                        www.reviewazon.com                             #
#                                                                       #
#########################################################################
# COPYRIGHT NOTICE                                                      #
# Copyright 2009 Niche Web Strategies LLC.  All Rights Reserved.        #
#                                                                       #
# This script may be only used and modified in accordance to the        #
# license agreement attached (eula.txt) except where expressly          #
# noted within commented areas of the code body. This copyright notice  #
# and the comments above and below must remain intact at all times.     #
# By using this code you agree to indemnify Niche Web Strategies LLC,   #
# its corporate agents and affiliates from any liability that might     #
# arise from its use.                                                   #
#                                                                       #
# Selling the code for this program without prior written consent is    #
# expressly forbidden and in violation of Domestic and International    #
# copyright laws.                                                       #
#########################################################################

$aws_affiliate_program = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");

if($aws_affiliate_program == "com")
{
?>
<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');
	

	switch(jQuery(this).val())
	{
	case "All":
		break;
	case "Apparel":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest Arrivals</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On Sale</option>');
		break;
	case "Automotive":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Baby":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "Beauty":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest Arrivals</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On Sale</option>');
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "DigitalMusic":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="songtitlerank">Most popular</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="uploaddaterank">Date added</option>');
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-video-release-date">Release date: newer to older</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="releasedate">Release date: newer to older</option>');		
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		break;
	case "GourmetFood":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "Grocery":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "HealthPersonalCare":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured Items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "HomeGarden":
		break;
	case "Industrial":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Jewelry":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');
		break;
	case "KindleStore":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-edition-sales-velocity">Quickest to slowest selling products</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		break;
	case "Kitchen":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Magazines":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="subslot-salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Merchants":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "Miscellaneous":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "MP3Downloads":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-releasedate">Release Date</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		break;
	case "Music":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="psrank">Bestseller ranking </option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="artistrank">Artist name: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="orig-rel-date">Original release date of the item listed from newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="release-date">Sorts by the latest release date from newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="releasedate">Release date: most recent to oldest</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-releasedate">Release date: oldest to most recent</option>');
		break;
	case "MusicalInstruments":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured Items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "MusicTracks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "OfficeProducts":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured Items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "OutdoorLiving":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "PCHardware":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured Items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "PetSupplies":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="+pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Photo":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Shoes":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest arrivals</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		break;
	case "SilverMerchants":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured Items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "SportingGoods":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="launch-date">Newest arrivals</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="sale-flag">On sale</option>');		
		break;
	case "Tools":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Toys":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-age-min">Age: high to low</option>');
		break;
	case "UnboxVideo":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-video-release-date">Release date: newer to older</option>');
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-video-release-date">Release date: newer to older</option>');
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-video-release-date">Release date: newer to older</option>');
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "Watches":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		break;
	case "Wireless":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "WirelessAccessories":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="psrank">Bestseller ranking projected sales</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");

</script>
<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="All" <?php if(get_option("search_category") == "All") { echo 'selected'; }?>>All</option>		
		<option value="Apparel" <?php if(get_option("search_category") == "Apparel") { echo 'selected'; }?>>Apparel</option>
		<option value="Automotive" <?php if(get_option("search_category") == "Automotive") { echo 'selected'; }?>>Automotive</option>
		<option value="Baby" <?php if(get_option("search_category") == "Baby") { echo 'selected'; }?>>Baby</option>		
		<option value="Beauty" <?php if(get_option("search_category") == "Beauty") { echo 'selected'; }?>>Beauty</option>
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DigitalMusic" <?php if(get_option("search_category") == "DigitalMusic") { echo 'selected'; }?>>Digital Music</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="GourmetFood" <?php if(get_option("search_category") == "GourmetFood") { echo 'selected'; }?>>Gourmet Food</option>
		<option value="Grocery" <?php if(get_option("search_category") == "Grocery") { echo 'selected'; }?>>Grocery</option>
		<option value="HealthPersonalCare" <?php if(get_option("search_category") == "HealthPersonalCare") { echo 'selected'; }?>>Health Personal Care</option>
		<option value="HomeGarden" <?php if(get_option("search_category") == "HomeGarden") { echo 'selected'; }?>>Home Garden</option>
		<option value="Industrial" <?php if(get_option("search_category") == "Industrial") { echo 'selected'; }?>>Industrial</option>
		<option value="Jewelry" <?php if(get_option("search_category") == "Jewelry") { echo 'selected'; }?>>Jewelry</option>
		<option value="KindleStore" <?php if(get_option("search_category") == "KindleStore") { echo 'selected'; }?>>KindleStore</option>
		<option value="Kitchen" <?php if(get_option("search_category") == "Kitchen") { echo 'selected'; }?>>Kitchen</option>
		<option value="Magazines" <?php if(get_option("search_category") == "Magazines") { echo 'selected'; }?>>Magazines</option>
		<option value="Merchants" <?php if(get_option("search_category") == "Merchants") { echo 'selected'; }?>>Merchants</option>
		<option value="Miscellaneous" <?php if(get_option("search_category") == "Miscellaneous") { echo 'selected'; }?>>Miscellaneous</option>
		<option value="MP3Downloads" <?php if(get_option("search_category") == "MP3Downloads") { echo 'selected'; }?>>MP3 Downloads</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="MusicalInstruments" <?php if(get_option("search_category") == "MusicalInstruments") { echo 'selected'; }?>>Musical Instruments</option>
		<option value="MusicTracks" <?php if(get_option("search_category") == "MusicTracks") { echo 'selected'; }?>>Music Tracks</option>
		<option value="OfficeProducts" <?php if(get_option("search_category") == "OfficeProducts") { echo 'selected'; }?>>Office Products</option>
		<option value="OutdoorLiving" <?php if(get_option("search_category") == "OutdoorLiving") { echo 'selected'; }?>>Outdoor Living</option>
		<option value="PCHardware" <?php if(get_option("search_category") == "PCHardware") { echo 'selected'; }?>>PC Hardware</option>
		<option value="PetSupplies" <?php if(get_option("search_category") == "PetSupplies") { echo 'selected'; }?>>Pet Supplies</option>
		<option value="Photo" <?php if(get_option("search_category") == "Photo") { echo 'selected'; }?>>Photo</option>
		<option value="Shoes" <?php if(get_option("search_category") == "Shoes") { echo 'selected'; }?>>Shoes</option>
		<option value="SilverMerchants" <?php if(get_option("search_category") == "SilverMerchants") { echo 'selected'; }?>>Silver Merchants</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SportingGoods" <?php if(get_option("search_category") == "SportingGoods") { echo 'selected'; }?>>Sporting Goods</option>
		<option value="Tools" <?php if(get_option("search_category") == "Tools") { echo 'selected'; }?>>Tools</option>
		<option value="Toys" <?php if(get_option("search_category") == "Toys") { echo 'selected'; }?>>Toys</option>
		<option value="UnboxVideo" <?php if(get_option("search_category") == "UnboxVideo") { echo 'selected'; }?>>Unbox Video</option>
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
		<option value="Watches" <?php if(get_option("search_category") == "Watches") { echo 'selected'; }?>>Watches</option>
		<option value="Wireless" <?php if(get_option("search_category") == "Wireless") { echo 'selected'; }?>>Wireless</option>
		<option value="WirelessAccessories" <?php if(get_option("search_category") == "WirelessAccessories") { echo 'selected'; }?>>Wireless Accessories</option>
	</select>
	<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php
}

if($aws_affiliate_program == "ca")
{

?>
<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');

	switch(jQuery(this).val())
	{
	case "Blended":
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="orig-rel-date">Alphabetical: A to Z</option>');	
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "ForeignBooks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "Music":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="orig-rel-date">Original release date of the item listed from newer to older</option>');
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-daterank">Rel Date: Old to New</option>');
		break;
	case "SoftwareVideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-daterank">Rel Date: Old to New</option>');
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");

</script>
<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="Blended" <?php if(get_option("search_category") == "Blended") { echo 'selected'; }?>>All</option>		
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="ForeignBooks" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Foreign Books</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SoftwareVideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Software Video Games</option>
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
	</select>
	<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php 
}

if($aws_affiliate_program == "de")
{
?>

<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');

	switch(jQuery(this).val())
	{
	case "All":
		break;
	case "Apparel":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Automotive":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Baby":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Beauty":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "ForeignBooks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "HealthPersonalCare":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "HomeImprovement":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');	
		break;
	case "Kitchen":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');	
		break;
	case "Magazines":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "MusicTracks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "OutdoorLiving":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "PCHardware":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Photo":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Shoes":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest arrivals</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "SoftwareVideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "SportingGoods":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverseprice">Price: high to low</option>');
		break;
	case "Tools":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Toys":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pmrank">Featured items</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Watches":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");

</script>
<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="Blended" <?php if(get_option("search_category") == "Blended") { echo 'selected'; }?>>All</option>		
		<option value="Apparel" <?php if(get_option("search_category") == "Apparel") { echo 'selected'; }?>>Apparel</option>
		<option value="Automotive" <?php if(get_option("search_category") == "Automotive") { echo 'selected'; }?>>Automotive</option>
		<option value="Baby" <?php if(get_option("search_category") == "Baby") { echo 'selected'; }?>>Baby</option>		
		<option value="Beauty" <?php if(get_option("search_category") == "Beauty") { echo 'selected'; }?>>Beauty</option>
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="ForeignBooks" <?php if(get_option("search_category") == "ForeignBooks") { echo 'selected'; }?>>Foreign Books</option>
		<option value="HealthPersonalCare" <?php if(get_option("search_category") == "HealthPersonalCare") { echo 'selected'; }?>>Health Personal Care</option>
		<option value="HomeGarden" <?php if(get_option("search_category") == "HomeGarden") { echo 'selected'; }?>>Home Garden</option>
		<option value="Kitchen" <?php if(get_option("search_category") == "Kitchen") { echo 'selected'; }?>>Kitchen</option>
		<option value="Magazines" <?php if(get_option("search_category") == "Magazines") { echo 'selected'; }?>>Magazines</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="MusicTracks" <?php if(get_option("search_category") == "MusicTracks") { echo 'selected'; }?>>Music Tracks</option>
		<option value="OutdoorLiving" <?php if(get_option("search_category") == "OutdoorLiving") { echo 'selected'; }?>>Outdoor Living</option>
		<option value="PCHardware" <?php if(get_option("search_category") == "PCHardware") { echo 'selected'; }?>>PC Hardware</option>
		<option value="Photo" <?php if(get_option("search_category") == "Photo") { echo 'selected'; }?>>Photo</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SoftwareVideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Software Video Games</option>
		<option value="SportingGoods" <?php if(get_option("search_category") == "SportingGoods") { echo 'selected'; }?>>Sporting Goods</option>
		<option value="Tools" <?php if(get_option("search_category") == "Tools") { echo 'selected'; }?>>Tools</option>
		<option value="Toys" <?php if(get_option("search_category") == "Toys") { echo 'selected'; }?>>Toys</option>
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
		<option value="Watches" <?php if(get_option("search_category") == "Watches") { echo 'selected'; }?>>Watches</option>
	</select>
		<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php 
}
if($aws_affiliate_program == "fr")
{
?>
<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');

	switch(jQuery(this).val())
	{
	case "Blended":
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="amzrank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="availability">Most to least available</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "ForeignBooks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Kitchen":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Music":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "MusicTracks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "SoftwareVideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-date">Rel Date: Old to New</option>');
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="amzrank">Most to least available</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="availability">Most to least available</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="date">Rel Date: new to old</option>');	
		break;
	case "Watches":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");

</script>
<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="Blended" <?php if(get_option("search_category") == "Blended") { echo 'selected'; }?>>All</option>		
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="ForeignBooks" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Foreign Books</option>
		<option value="Kitchen" <?php if(get_option("search_category") == "Kitchen") { echo 'selected'; }?>>Kitchen</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="MusicTracks" <?php if(get_option("search_category") == "MusicTracks") { echo 'selected'; }?>>Music Tracks</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SoftwareVideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Software Video Games</option>		
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
		<option value="Watches" <?php if(get_option("search_category") == "Watches") { echo 'selected'; }?>>Watches</option>
</select>
	<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php 
}
if($aws_affiliate_program == "jp")
{
?>
<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');

	switch(jQuery(this).val())
	{
	case "Blended":
		break;
	case "Apparel":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Baby":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="psrank">Bestseller ranking</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		break;
	case "Beauty":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Highest to lowest ratings in customer reviews</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "ForeignBooks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="daterank">Publication date: newer to older</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Grocery":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Highest to lowest ratings in customer reviews</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Hobbies":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-release-date">Release date: newer to older</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="release-date">Release date: older to newer</option>');		
		break;
	case "Kitchen":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;		
	case "Music":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Bestselling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "MusicTracks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "SportingGoods":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Toys":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Watches":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");
</script>

<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="Blended" <?php if(get_option("search_category") == "Blended") { echo 'selected'; }?>>All</option>		
		<option value="Apparel" <?php if(get_option("search_category") == "Apparel") { echo 'selected'; }?>>Apparel</option>
		<option value="Baby" <?php if(get_option("search_category") == "Baby") { echo 'selected'; }?>>Baby</option>		
		<option value="Beauty" <?php if(get_option("search_category") == "Beauty") { echo 'selected'; }?>>Beauty</option>
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="ForeignBooks" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Foreign Books</option>
		<option value="Grocery" <?php if(get_option("search_category") == "Grocery") { echo 'selected'; }?>>Grocery</option>
		<option value="Hobbies" <?php if(get_option("search_category") == "Hobbies") { echo 'selected'; }?>>Hobbies</option>
		<option value="Kitchen" <?php if(get_option("search_category") == "Kitchen") { echo 'selected'; }?>>Kitchen</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="MusicTracks" <?php if(get_option("search_category") == "MusicTracks") { echo 'selected'; }?>>Music Tracks</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SportingGoods" <?php if(get_option("search_category") == "SportingGoods") { echo 'selected'; }?>>Sporting Goods</option>
		<option value="Toys" <?php if(get_option("search_category") == "Toys") { echo 'selected'; }?>>Toys</option>
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
		<option value="Watches" <?php if(get_option("search_category") == "Watches") { echo 'selected'; }?>>Watches</option>
</select>
	<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php 
}
if($aws_affiliate_program == "co.uk")
{
?>
<script>
jQuery('#SearchIndexValue').change(function(){
	jQuery('#ReviewAZON_Sort_Value').empty();
	jQuery('#ReviewAZON_Sort_Value').append('<option value="default">Default Sorting</option>');
	


	switch(jQuery(this).val())
	{
	case "All":
		break;
	case "Apparel":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Baby":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Beauty":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "Books":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="pricerank">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Classical":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "DVD":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "Electronics":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');		
		break;
	case "Jewelry":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "HealthPersonalCare":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');	
		break;
	case "HomeImprovement":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');	
		break;
	case "Kitchen":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');	
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "MP3Downloads":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Music":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');		
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "MusicTracks":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "OfficeProducts":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		break;
	case "OutdoorLiving":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Shoes":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-launch-date">Newest arrivals</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="relevancerank">Relevance</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		break;
	case "Software":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "SoftwareVideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="inverse-pricerank">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Toys":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-mfg-age-min">Age: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="mfg-age-min">Age: low to high</option>');
		break;
	case "VHS":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Video":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "VideoGames":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="reviewrank">Average customer review: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	case "Watches":
		jQuery('#ReviewAZON_Sort_Value').append('<option value="salesrank">Best Selling</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="price">Price: low to high</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-price">Price: high to low</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="titlerank">Alphabetical: A to Z</option>');
		jQuery('#ReviewAZON_Sort_Value').append('<option value="-titlerank">Alphabetical: Z to A</option>');
		break;
	default:
		break;
	}
});

jQuery('#SearchIndexValue').val("<?php echo get_option("search_category")?>");
jQuery('#SearchIndexValue').trigger("change");
jQuery('#ReviewAZON_Sort_Value').val("<?php echo get_option("search_sort")?>");

</script>
<select id="SearchIndexValue" name="SearchIndexValue" style="height:25px;border:solid 1px gainsboro;">
		<option value="Blended" <?php if(get_option("search_category") == "Blended") { echo 'selected'; }?>>All</option>		
		<option value="Apparel" <?php if(get_option("search_category") == "Apparel") { echo 'selected'; }?>>Apparel</option>
		<option value="Baby" <?php if(get_option("search_category") == "Baby") { echo 'selected'; }?>>Baby</option>		
		<option value="Beauty" <?php if(get_option("search_category") == "Beauty") { echo 'selected'; }?>>Beauty</option>
		<option value="Books" <?php if(get_option("search_category") == "Books") { echo 'selected'; }?>>Books</option>
		<option value="Classical" <?php if(get_option("search_category") == "Classical") { echo 'selected'; }?>>Classical</option>
		<option value="DVD" <?php if(get_option("search_category") == "DVD") { echo 'selected'; }?>>DVD</option>
		<option value="Electronics" <?php if(get_option("search_category") == "Electronics") { echo 'selected'; }?>>Electronics</option>
		<option value="HealthPersonalCare" <?php if(get_option("search_category") == "HealthPersonalCare") { echo 'selected'; }?>>Health Personal Care</option>
		<option value="HomeGarden" <?php if(get_option("search_category") == "HomeGarden") { echo 'selected'; }?>>Home Garden</option>
		<option value="Jewelry" <?php if(get_option("search_category") == "Jewelry") { echo 'selected'; }?>>Jewelry</option>
		<option value="Kitchen" <?php if(get_option("search_category") == "Kitchen") { echo 'selected'; }?>>Kitchen</option>
		<option value="MP3Downloads" <?php if(get_option("search_category") == "MP3Downloads") { echo 'selected'; }?>>MP3 Downloads</option>
		<option value="Music" <?php if(get_option("search_category") == "Music") { echo 'selected'; }?>>Music</option>
		<option value="MusicTracks" <?php if(get_option("search_category") == "MusicTracks") { echo 'selected'; }?>>Music Tracks</option>
		<option value="OfficeProducts" <?php if(get_option("search_category") == "OfficeProducts") { echo 'selected'; }?>>OfficeProducts</option>
		<option value="OutdoorLiving" <?php if(get_option("search_category") == "OutdoorLiving") { echo 'selected'; }?>>Outdoor Living</option>
		<option value="Shoes" <?php if(get_option("search_category") == "Shoes") { echo 'selected'; }?>>Shoes</option>
		<option value="Software" <?php if(get_option("search_category") == "Software") { echo 'selected'; }?>>Software</option>
		<option value="SoftwareVideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Software Video Games</option>	
		<option value="Toys" <?php if(get_option("search_category") == "Toys") { echo 'selected'; }?>>Toys</option>
		<option value="VHS" <?php if(get_option("search_category") == "VHS") { echo 'selected'; }?>>VHS</option>
		<option value="Video" <?php if(get_option("search_category") == "Video") { echo 'selected'; }?>>Video</option>
		<option value="VideoGames" <?php if(get_option("search_category") == "VideoGames") { echo 'selected'; }?>>Video Games</option>
		<option value="Watches" <?php if(get_option("search_category") == "Watches") { echo 'selected'; }?>>Watches</option>
</select>
	<select id="ReviewAZON_Sort_Value" name="ReviewAZON_Sort_Value" style="width:175px;height:25px;border:solid 1px gainsboro;">
	</select>
<?php 
}
?>