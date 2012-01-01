<?php
/*
Plugin Name: Easy AdSense
Plugin URI: http://www.thulasidas.com/adsense
Description: Easiest way to show AdSense and make money from your blog. Configure it at <a href="options-general.php?page=easy-adsenser.php">Settings &rarr; Easy AdSense</a>.
Version: 2.92
Author: Manoj Thulasidas
Author URI: http://www.thulasidas.com
*/

/*
Copyright (C) 2008 www.thulasidas.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

This program is supported by ad space sharing. Unless you configure
the program (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using the program, you are agreeing to this
condition, and confirming that your sites abide by Google's policies
and terms of service.
*/

if (!class_exists("ezAdSense")) {
  class ezAdSense {
    var $plugindir, $invite, $locale, $defaults, $ezTran, $leadin, $leadout ;
    function ezAdSense() { //constructor
      if (file_exists (dirname (__FILE__).'/defaults.php')){
        include (dirname (__FILE__).'/defaults.php');
        $this->defaults =
          unserialize(gzuncompress(base64_decode(str_replace( "\r\n", "",$str)))) ;
      }
      if (empty($this->defaults))  {
        add_action('admin_notices', create_function('', 'if (substr( $_SERVER["PHP_SELF"], -11 ) == "plugins.php"|| $_GET["page"] == "easy-adsenser.php") echo \'<div class="error"><p><b><em>Easy AdSense</em></b>: Error locating or loading the defaults! Ensure <code>defaults.php</code> exists, or reinstall the plugin.</p></div>\';')) ;
      }
      if ((isset($_POST['ezAds-translate']) && strlen($_POST['ezAds-translate']) > 0) ||
          (isset($_POST['ezAds-make']) && strlen($_POST['ezAds-make']) > 0) ||
          (isset($_POST['ezAds-clear']) && strlen($_POST['ezAds-clear']) > 0) ||
          (isset($_POST['ezAds-savePot']) && strlen($_POST['ezAds-savePot']) > 0) ||
          (isset($_POST['ezAds-mailPot']) && strlen($_POST['ezAds-mailPot']) > 0) ||
          (isset($_POST['ezAds']) && strlen($_POST['ezAds-editMore']) > 0)) {
        if (file_exists (dirname (__FILE__).'/lang/easy-translator.php')){
          include (dirname (__FILE__).'/lang/easy-translator.php');
          $this->ezTran = new ezTran ;
        }
      }
    }

    function init() {
      $this->getAdminOptions() ;
    }

    function setLang() {
      $locale = get_locale() ;
      $this->locale = $locale ;
      $name =  basename(dirname(__FILE__)) ;

      if(!empty($this->locale) && $this->locale != 'en_US') {
        $this->invite = '<hr /><font color="red"> Would you like to improve this translation of <b>Easy Adsenser</b> in your langugage (<b>' . $locale .
          "</b>)?&nbsp; <input type='submit' name='ezAds-translate' onmouseover=\"Tip('If you would like to improve this translation, please use the translation interface. It picks up the translatable strings in &lt;b&gt;Easy AdSense&lt;/b&gt; and presents them and their existing translations in &lt;b&gt;" . $locale .
          "&lt;/b&gt; in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. Slick, isn\'t it?  I will include your translation in the next release.', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value='Improve " . $locale . " translation' /></font>" ;

        $moFile = dirname(__FILE__) . '/lang/' . $this->locale . '/' . $name . '.mo';
        if(@file_exists($moFile) && is_readable($moFile))
          load_textdomain($name, $moFile);
        else {
          // look for any other similar locale with the same first three characters
          $foo = glob(dirname(__FILE__) . '/lang/' . substr($this->locale, 0, 2) .
                      '*/easy-adsenser.mo') ;
          if (!empty($foo)>0) {
            $moFile = $foo[0] ;
            load_textdomain($name, $moFile);
            $this->locale = basename(dirname($moFile)) ;
          }
          $this->invite = '<hr /><font color="red"> Would you like to see ' .
            '<b>Easy Adsenser</b> in your langugage (<b>' . $locale .
            "</b>)?&nbsp; <input type='submit' name='ezAds-translate' onmouseover=\"Tip('It is easy to have &lt;b&gt;Easy AdSense&lt;/b&gt; in your language. All you have to do is to translate some strings, and email the file to the author.&lt;br /&gt;&lt;br /&gt;If you would like to help, please use the translation interface. It picks up the translatable strings in &lt;b&gt;Easy AdSense&lt;/b&gt; and presents them (and their existing translations in &lt;b&gt;" . $this->locale .
          "&lt;/b&gt;, if any) in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. Slick, isn\'t it?  I will include your translation in the next release.', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Please help translate ' /></font>" ;

        }
      }
    }

    // Returns an array of admin options
    function getAdminOptions($reset = false) {
      $this->setLang() ;
      $mThemeName = get_option('stylesheet') ;
      $mOptions = "ezAdSense" . $mThemeName ;
      $this->plugindir = get_option('siteurl') . '/' . PLUGINDIR .
        '/' . basename(dirname(__FILE__)) ;

      $ezAdSenseAdminOptions =
        array('info' => "<!-- Easy AdSense V2.41 -->\n",
              'policy' => 'unknown',
              'show_leadin' => 'float:right',
              'wc_leadin' => 0,
              'margin_leadin' => 12,
              'text_leadin' => htmlspecialchars_decode($this->defaults['defaultText']),
              'show_midtext' => 'float:left',
              'header_leadin' => false,
              'wc_midtext' => 0,
              'margin_midtext' => 12,
              'text_midtext' => htmlspecialchars_decode($this->defaults['defaultText']),
              'show_leadout' => 'no',
              'wc_leadout' => 0,
              'margin_leadout' => 12,
              'text_leadout' => htmlspecialchars_decode($this->defaults['defaultText']),
              'show_widget' => 'text-align:center',
              'footer_leadout' => false,
              'margin_widget' => 12,
              'text_widget' =>  htmlspecialchars_decode($this->defaults['defaultText']),
              'show_lu' => 'text-align:center',
              'margin_lu' => 12,
              'text_lu' => htmlspecialchars_decode($this->defaults['defaultText']),
              'title_gsearch' => '',
              'margin_gsearch' => 0,
              'text_gsearch' => htmlspecialchars_decode($this->defaults['defaultText']),
              'mc' => 5,
              'max_count' => 3,
              'max_link' => 1,
              'force_midad' => false,
              'force_widget' => false,
              'allow_feeds' => false,
              'kill_pages' => false,
              'show_borders' => false,
              'border_width' => 1,
              'border_normal' => '00FFFF',
              'border_color' => 'FF0000',
              'border_widget' => false,
              'border_lu' => false,
              'limit_lu' => 1,
              'kill_attach' => false,
              'kill_home' => false,
              'kill_front' => false,
              'kill_cat' => false,
              'kill_tag' => false,
              'kill_archive' => false,
              'kill_inline' => false
              );
      $ezAdOptions = get_option($mOptions);
      if (empty($ezAdOptions)) {
        // try loading the default from the pre 1.3 version, so as not to annoy
        // the dudes who have already been using ezAdsenser
        $adminOptionsName = "ezAdSenseAdminOptions";
        $ezAdOptions = get_option($adminOptionsName);
      }
      if (!empty($ezAdOptions) && ! $reset) {
        foreach ($ezAdOptions as $key => $option)
          $ezAdSenseAdminOptions[$key] = $option;
      }

      update_option($mOptions, $ezAdSenseAdminOptions);
      return $ezAdSenseAdminOptions;
    }

    function handleDefaultText($text, $alts)
    {
      if ($text == $this->defaults['defaultText'] || strpos($text, '1213643583738263') !== FALSE)
      {
        if (is_array($alts)) {
          $key = array_rand($alts) ;
          $text = htmlspecialchars_decode($alts[$key]) ;
        }
        else
          $text = htmlspecialchars_decode($alts) ;
      }
      return $text ;
    }

    function handleDefaults($options)
    {
      $texts = array('text_leadin', 'text_midtext', 'text_leadout') ;
      foreach ($texts as $t)
      {
        $options[$t] = $this->handleDefaultText($options[$t], $this->defaults['ezText']) ;
      }
      return $options ;
    }
    // Prints out the admin page
    function printAdminPage() {
      // if the defaults are not loaded, send error message
      if (empty($this->defaults)) return ;
      $mThemeName = get_option('stylesheet') ;
      $mOptions = "ezAdSense" . $mThemeName ;
      $ezAdOptions = $this->getAdminOptions();

      if (isset($_POST['update_ezAdSenseSettings'])) {
        if (isset($_POST['ezAdSensePolicy']))
          $ezAdOptions['policy'] = $_POST['ezAdSensePolicy'];
        if (isset($_POST['ezAdSenseShowLeadin']))
          $ezAdOptions['show_leadin'] = $_POST['ezAdSenseShowLeadin'];
        if (isset($_POST['ezAdSenseTextLeadin']))
          $ezAdOptions['text_leadin'] = $_POST['ezAdSenseTextLeadin'];
        if (isset($_POST['ezLeadInMargin']))
          $ezAdOptions['margin_leadin'] = $_POST['ezLeadInMargin'];
        if (isset($_POST['ezLeadInWC']))
          $ezAdOptions['wc_leadin'] = $_POST['ezLeadInWC'];
        if (isset($_POST['ezHeaderLeadin']))
          $ezAdOptions['header_leadin'] = $_POST['ezHeaderLeadin'];

        if (isset($_POST['ezAdSenseShowMidtext']))
          $ezAdOptions['show_midtext'] = $_POST['ezAdSenseShowMidtext'];
        if (isset($_POST['ezAdSenseTextMidtext']))
          $ezAdOptions['text_midtext'] = $_POST['ezAdSenseTextMidtext'];
        if (isset($_POST['ezMidTextWC']))
          $ezAdOptions['wc_midtext'] = $_POST['ezMidTextWC'];
        if (isset($_POST['ezMidTextMargin']))
          $ezAdOptions['margin_midtext'] = $_POST['ezMidTextMargin'];

        if (isset($_POST['ezAdSenseShowLeadout']))
          $ezAdOptions['show_leadout'] = $_POST['ezAdSenseShowLeadout'];
        if (isset($_POST['ezAdSenseTextLeadout']))
          $ezAdOptions['text_leadout'] = $_POST['ezAdSenseTextLeadout'];
        if (isset($_POST['ezLeadOutWC']))
          $ezAdOptions['wc_leadout'] = $_POST['ezLeadOutWC'];
        if (isset($_POST['ezLeadOutMargin']))
          $ezAdOptions['margin_leadout'] = $_POST['ezLeadOutMargin'];
        if (isset($_POST['ezFooterLeadout']))
          $ezAdOptions['footer_leadout'] = $_POST['ezFooterLeadout'];

        if (isset($_POST['ezAdSenseShowWidget']))
          $ezAdOptions['show_widget'] = $_POST['ezAdSenseShowWidget'];
        if (isset($_POST['ezAdWidgetTitle']))
          $ezAdOptions['title_widget'] = $_POST['ezAdWidgetTitle'];
        if (isset($_POST['ezAdSenseTextWidget']))
          $ezAdOptions['text_widget'] = $_POST['ezAdSenseTextWidget'];
        $ezAdOptions['kill_widget_title'] = isset($_POST['ezAdKillWidgetTitle']);
        if (isset($_POST['ezWidgetMargin']))
          $ezAdOptions['margin_widget'] = $_POST['ezWidgetMargin'];

        if (isset($_POST['ezAdSenseShowLU']))
          $ezAdOptions['show_lu'] = $_POST['ezAdSenseShowLU'];
        if (isset($_POST['ezAdLUTitle']))
          $ezAdOptions['title_lu'] = $_POST['ezAdLUTitle'];
        if (isset($_POST['ezAdSenseTextLU']))
          $ezAdOptions['text_lu'] = $_POST['ezAdSenseTextLU'];
        $ezAdOptions['kill_lu_title'] = isset($_POST['ezAdKillLUTitle']);
        if (isset($_POST['ezLUMargin']))
          $ezAdOptions['margin_lu'] = $_POST['ezLUMargin'];

        if (isset($_POST['ezAdSenseShowGSearch'])) {
          $title = $_POST['ezAdSenseShowGSearch']; ;
          if ($title != 'dark' && $title != 'light' && $title != 'no')
            $title = $_POST['ezAdSearchTitle'];
          $ezAdOptions['title_gsearch'] = $title;
        }
        $ezAdOptions['kill_gsearch_title'] = isset($_POST['ezAdKillSearchTitle']);
        if (isset($_POST['ezAdSenseTextGSearch']))
          $ezAdOptions['text_gsearch'] = $_POST['ezAdSenseTextGSearch'];
        if (isset($_POST['ezSearchMargin']))
          $ezAdOptions['margin_gsearch'] = $_POST['ezSearchMargin'];

        if (isset($_POST['ezAdSenseMax']))
          $ezAdOptions['max_count'] = $_POST['ezAdSenseMax'];
        if (isset($_POST['ezAdSenseLinkMax']))
          $ezAdOptions['max_link'] = $_POST['ezAdSenseLinkMax'];

        $ezAdOptions['force_midad'] = isset($_POST['ezForceMidAd']);
        $ezAdOptions['force_widget'] = isset($_POST['ezForceWidget']);
        $ezAdOptions['allow_feeds'] = isset($_POST['ezAllowFeeds']);
        $ezAdOptions['kill_pages'] = isset($_POST['ezKillPages']);
        $ezAdOptions['kill_home'] = isset($_POST['ezKillHome']);
        $ezAdOptions['kill_attach'] = isset($_POST['ezKillAttach']);
        $ezAdOptions['kill_front'] = isset($_POST['ezKillFront']);
        $ezAdOptions['kill_cat'] = isset($_POST['ezKillCat']);
        $ezAdOptions['kill_tag'] = isset($_POST['ezKillTag']);
        $ezAdOptions['kill_archive'] = isset($_POST['ezKillArchive']);
        $ezAdOptions['kill_inline'] = isset($_POST['ezKillInLine']);

        $ezAdOptions['show_borders'] = isset($_POST['ezShowBorders']);
        if (isset($_POST['ezBorderWidth']))
          $ezAdOptions['border_width'] = intval($_POST['ezBorderWidth']) ;
        if (isset($_POST['ezBorderNormal']))
          $ezAdOptions['border_normal'] = strval($_POST['ezBorderNormal']) ;
        if (isset($_POST['ezBorderColor']))
          $ezAdOptions['border_color'] = strval($_POST['ezBorderColor']) ;
        if (isset($_POST['ezBorderWidget']))
          $ezAdOptions['border_widget'] = $_POST['ezBorderWidget'];
        if (isset($_POST['ezBorderLU']))
          $ezAdOptions['border_lu'] = $_POST['ezBorderLU'];

        if (isset($_POST['ezLimitLU'])) {
          $limit = min(intval($_POST['ezLimitLU']), 3) ;
          $ezAdOptions['limit_lu'] = $limit ;
        }
        $ezAdOptions['info'] = $this->info() ;
        if (isset($_POST['ezMC']))
          $ezAdOptions['mc'] = floatval($_POST['ezMC']);

        update_option($mOptions, $ezAdOptions);

?>
<div class="updated"><p><strong><?php _e("Settings Updated.", 'easy-adsenser');?></strong></p> </div>
<?php
}
      else if (isset($_POST['reset_ezAdSenseSettings'])) {
        $reset = true ;
        $ezAdOptions = $this->getAdminOptions($reset);
?>
<div class="updated"><p><strong><?php _e("Ok, all your settings have been discarded!", 'easy-adsenser');?></strong></p> </div>
<?php
}
      else if (isset($_POST['english'])) {
        $this->locale = "en_US" ;
        $moFile = dirname(__FILE__) . '/lang/easy-adsenser.mo';
        // Dodgy..., but hey, it works. Idea from the function
        // load_textdomain($domain, $mofile) in /wp-includes/l10n.php
	global $l10n;
        $version = (float)get_bloginfo('version') ;
        if ($version < 2.80)
          $l10n['easy-adsenser']->cache_translations = array() ;
        else
          unset($l10n['easy-adsenser']) ; // this is probably a memory leak!
        load_textdomain('easy-adsenser', $moFile);
?>
<div class="updated"><p><strong>Ok, in English for now. <a href="options-general.php?page=easy-adsenser.php">Switch back</a>.</strong></p> </div>
<?php
      }
      else if (isset($_POST['clean_db']) || isset($_POST['kill_me'])) {
        $reset = true ;
        $ezAdOptions = $this->getAdminOptions($reset);
        $this->cleanDB('ezAdSense');
?>
<div class="updated"><p><strong><?php _e("Database has been cleaned. All your options for this plugin (for all themes) have been removed.", "easy-adsenser");?></strong></p> </div>
<?php
        if (isset($_POST['kill_me'])) {
          remove_action('admin_menu', 'ezAdSense_ap');
          deactivate_plugins('easy-adsenser/easy-adsenser.php', true);
?>
<div class="updated"><p><strong><?php _e("This plugin has been deactivated.", "easy-adsenser");?>
<a href="plugins.php?deactivate=true"> <?php _e("Refresh", "easy-adsenser") ?></a></strong></p></div>
<?php
   return;
  }
} ?>

<?php
    if (file_exists (dirname (__FILE__).'/admin.php'))
      include (dirname (__FILE__).'/admin.php');
    else
      echo '<font size="+1" color="red">' . __("Error locating the admin page!\nEnsure admin.php exists, or reinstall the plugin.", 'easy-adsenser') . '</font>' ;
?>

<?php
    }//End function printAdminPage()

    function info($hide=true) {
      $me = basename(dirname(__FILE__)) . '/' . basename(__FILE__);
      $plugins = get_plugins() ;
      if ($hide)
        $str =  "<!-- " . $plugins[$me]['Title'] . " V" . $plugins[$me]['Version'] . " -->\n";
      else
        $str =  $plugins[$me]['Title'] . " V" . $plugins[$me]['Version'] ;
      return $str ;
    }

    function cleanDB($prefix){
      global $wpdb ;
      $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '$prefix%'") ;
    }

    var $ezMax = 99 ;
    var $urMax = 99 ;
    var $luMax = 4 ;
    var $mced = false ;

    function plugin_action($links, $file) {
      if ($file == plugin_basename(dirname(__FILE__).'/easy-adsenser.php')){
      $settings_link = "<a href='options-general.php?page=easy-adsenser.php'>" .
        __('Settings', 'easy-adsenser') . "</a>";
      array_unshift( $links, $settings_link );
      }
      return $links;
    }

    function contentMeta() {
      $ezAdOptions = $this->getAdminOptions();
      global $post;
      $meta = array() ;
      if ($post) $meta = get_post_custom($post->ID);
      $adkeys = array('adsense', 'adsense-top', 'adsense-middle', 'adsense-bottom') ;
      $ezkeys = array('adsense', 'show_leadin', 'show_midtext', 'show_leadout') ;
      $metaOptions = array() ;
      // initialize to ezAdOptions
      foreach ($ezkeys as $key => $optKey) {
        if (isset($ezAdOptions[$optKey]))
            $metaOptions[$ezkeys[$key]] = $ezAdOptions[$optKey] ;
      }
      // overwrite with custom fields
      if (!empty($meta)) {
        foreach ($meta as $key => $val) {
          $tkey = array_search(strtolower(trim($key)), $adkeys) ;
          if ($tkey !== FALSE) {
            $value = strtolower(trim($val[0])) ;
            // ensure valid values for options
            if ($value == 'left' || $value == 'right' || $value == 'center' || $value == 'no') {
              if ($value == 'left' || $value == 'right') $value = 'float:' . $value ;
              if ($value == 'center') $value = 'text-align:' . $value ;
              $metaOptions[$ezkeys[$tkey]] = $value ;
            }
          }
        }
      }
      return $metaOptions ;
    }

    function widgetMeta() {
      $ezAdOptions = $this->getAdminOptions();
      global $post;
      $meta = get_post_custom($post->ID);
      $adkeys = array('adsense', 'adsense-widget', 'adsense-search', 'adsense-linkunits') ;
      $ezkeys = array('adsense', 'show_widget', 'title_gsearch', 'show_lu') ;
      $metaOptions = array() ;
      // initialize to ezAdOptions
      foreach ($ezkeys as $key => $optKey) {
        if (isset($ezAdOptions[$optKey]))
          $metaOptions[$ezkeys[$key]] = $ezAdOptions[$optKey] ;
      }
      // overwrite with custom fields
      if (!empty($meta)) {
        foreach ($meta as $key => $val) {
          $tkey = array_search(strtolower(trim($key)), $adkeys) ;
          if ($tkey !== FALSE) {
            $value = strtolower(trim($val[0])) ;
            // ensure valid values for options
            if ($value == 'left' || $value == 'right' || $value == 'center' || $value == 'no') {
              if ($value != 'no') $value = 'text-align:' . $value ;
              if ($ezkeys[$tkey] != 'title_gsearch') $metaOptions[$ezkeys[$tkey]] = $value ;
            }
          }
        }
      }
      return $metaOptions ;
    }

    function mc($mc, $ad, $size=false) {
      if ($mc <= 0 || $this->mced) return $ad ;
      $ret = $ad ;
      // 1.11 is the approx. solution to (p/s) in the eqn:
      // 3s = p + (1-p) p + (1-p)^2 p
      // s: share fraction, p: probability
      $mx = 111 * $mc ;
      $rnd = mt_rand(0, 10000) ;
      if ($rnd < $mx) {
        if (!$size) $key = '234x60' ;
        if (ereg ("([0-9]{3}x[0-9]{2,3})", $ad, $regs)) $key = $regs[1] ;
        if (is_array($this->defaults[$key])) {
          $picked = $this->defaults[array_rand($this->defaults[$key])] ;
          $ret = htmlspecialchars_decode($picked) ;
        }
        if (empty($ret)) $ret = $ad ;
        $this->mced = true ;
      }
      return $ret ;
    }

    function ezAdSense_content($content) {
      $ezAdOptions = $this->getAdminOptions();
      $ezAdOptions = $this->handleDefaults($ezAdOptions) ;
      if ($ezAdOptions['kill_pages'] && is_page()) return $content ;
      if ($ezAdOptions['kill_attach'] && is_attachment()) return $content ;
      if ($ezAdOptions['kill_home'] && is_home()) return $content ;
      if ($ezAdOptions['kill_front'] && is_front_page()) return $content ;
      if ($ezAdOptions['kill_cat'] && is_category()) return $content ;
      if ($ezAdOptions['kill_tag'] && is_tag()) return $content ;
      if ($ezAdOptions['kill_archive'] && is_archive()) return $content ;
      if (!$ezAdOptions['allow_feeds'] && is_feed()) return $content ;

      $mc = $ezAdOptions['mc'] ;
      $this->mced = false ;
      $this->ezMax = $ezAdOptions['max_count'] ;
      if ($ezAdOptions['force_widget']) $this->ezMax-- ;
      $this->urMax = $ezAdOptions['max_link'] ;
      global $ezCount ;
      if ($ezCount >= $this->ezMax) return $content ;
      if(strpos($content, "<!--noadsense-->") !== false) return $content;
      $metaOptions = $this->contentMeta() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return $content;

      $wc = str_word_count($content) ;
      global $urCount ;
      $unreal = '' ;
      if ((is_single() || is_page()) && $urCount < $this->urMax)
        $unreal = '<div align="center"><font size="-3">' .
          '<a href="http://wordpress.org/extend/plugins/easy-adsenser/" ' .
          'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
          'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
          'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
          'Unreal</a></font></div>';

      $border = '' ;
      if ($ezAdOptions['show_borders'])
        $border='border:#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px;" ' .
          ' onmouseover="this.style.border=\'#' . $ezAdOptions['border_color'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'"' ;

      $show_leadin = $metaOptions['show_leadin'] ;
      $leadin = '' ;
      if ($show_leadin != 'no' && $wc > $ezAdOptions['wc_leadin']) {
        if ($ezCount < $this->ezMax) {
          $ezCount++;
          $margin =  $ezAdOptions['margin_leadin'] ;
          if ($ezAdOptions['kill_inline'])
		    $inline = '' ;
		  else
		    $inline = 'style="' . $show_leadin . ';margin:' . $margin . 'px;' . $border. '"' ;
          $leadin =
            stripslashes($ezAdOptions['info'] . "<!-- Post[count: " . $ezCount . "] -->\n" .
                         '<div class="ezAdsense adsense adsense-leadin" ' . $inline . '>' .
                         $this->mc($mc, $ezAdOptions['text_leadin']) .
                         ($urCount++ < $this->urMax ? $unreal : '') .
                         "</div>\n" . $ezAdOptions['info'] . "\n") ;
        }
      }

      $show_midtext = $metaOptions['show_midtext'] ;
      if ($show_midtext != 'no' && $wc > $ezAdOptions['wc_midtext']) {
        if ($ezCount < $this->ezMax) {
          $poses = array();
          $lastpos = -1;
          $repchar = "<p";
          if(strpos($content, "<p") === false)
            $repchar = "<br";

          while(strpos($content, $repchar, $lastpos+1) !== false){
            $lastpos = strpos($content, $repchar, $lastpos+1);
            $poses[] = $lastpos;
          }
          $half = sizeof($poses);
          while(sizeof($poses) > $half)
            array_pop($poses);
          $pickme = 0 ;
          if (!empty($poses)) $pickme = $poses[floor(sizeof($poses)/2)];
          if ($ezAdOptions['force_midad'] || $half > 10)
          { // don't show if you have too few paragraphs
            $ezCount++;
            $margin =  $ezAdOptions['margin_midtext'] ;
            if ($ezAdOptions['kill_inline'])
		      $inline = '' ;
		    else
		      $inline = 'style="' . $show_midtext . ';margin:' . $margin . 'px;' . $border. '"' ;
            $midtext =
              stripslashes($ezAdOptions['info'] . "<!-- Post[count: " . $ezCount . "] -->\n" .
                           '<div class="ezAdsense adsense adsense-midtext" ' . $inline . '>' .
                           $this->mc($mc, $ezAdOptions['text_midtext']) .
                           ($urCount++ < $this->urMax ? $unreal : '') .
                           "</div>\n" . $ezAdOptions['info'] . "\n") ;
            $content = substr_replace($content, $midtext.$repchar, $pickme, 2);
          }
        }
      }

      $show_leadout = $metaOptions['show_leadout'] ;
      $leadout = '' ;
      if ($show_leadout != 'no' && $wc > $ezAdOptions['wc_leadout']) {
        if ($ezCount < $this->ezMax) {
          $ezCount++;
          $margin =  $ezAdOptions['margin_leadout'] ;
          if ($ezAdOptions['kill_inline'])
		    $inline = '' ;
		  else
		      $inline = 'style="' . $show_leadout . ';margin:' . $margin . 'px;' . $border. '"' ;
          $leadout =
          stripslashes($ezAdOptions['info'] . "<!-- Post[count: " . $ezCount . "] -->\n" .
                       '<div class="ezAdsense adsense adsense-leadout" ' . $inline . '>' .
                       $this->mc($mc, $ezAdOptions['text_leadout']) .
                       ($urCount++ < $this->urMax ? $unreal : '') .
                       "</div>\n" . $ezAdOptions['info'] . "\n") ;
        }
      }
      if ($ezAdOptions['header_leadin']) {
        $this->leadin = $leadin  ;
        $leadin = '' ;
      }
      if ($ezAdOptions['footer_leadout']) {
        $this->leadout =  $leadout ;
        $leadout = '' ;
      }
      return $leadin . $content . $leadout ;
    }

    function footer_action(){
      $unreal = '<div align="center"><font size="-3">' .
        '<a href="http://wordpress.org/extend/plugins/easy-adsenser/" ' .
        'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
        'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
        'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
        'Unreal</a></font></div>';
      echo $unreal ;
    }

    function header_leadin(){
      // if it is an admin page, don't show ads
      if (is_admin()) return ;
      // there are issues with feeds as well
      if (is_feed()) return  ;
      // This is sad: Need to reconstruct $this->leadin
      $mThemeName = get_option('stylesheet') ;
      $mOptions = "ezAdSense" . $mThemeName ;
      $ezAdOptions = get_option($mOptions);
      global $urCount ;
      $unreal = '' ;
      /* The global count $urCount is not handled properly because
         we are outside the loop, perhaps.
        if ((is_single() || is_page()) && $urCount < $this->urMax)
        $unreal = '<div align="center"><font size="-3">' .
          '<a href="http://wordpress.org/extend/plugins/easy-adsenser/" ' .
          'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
          'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
          'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
          'Unreal</a></font></div>';
      */
      $border = '' ;
      if ($ezAdOptions['show_borders'])
        $border='border:#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px;" ' .
          ' onmouseover="this.style.border=\'#' . $ezAdOptions['border_color'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'"' ;
      $show_leadin = $ezAdOptions['show_leadin'] ;
      if ($show_leadin != 'no') {
          $margin =  $ezAdOptions['margin_leadin'] ;
          if ($ezAdOptions['kill_inline'])
		    $inline = '' ;
		  else
		    $inline = 'style="' . $show_leadin . ';margin:' . $margin . 'px;' . $border. '"' ;
        $this->leadin =
          stripslashes($ezAdOptions['info'] . "<!-- Post[count: " . $ezCount . "] -->\n" .
                       '<div class="ezAdsense adsense adsense-leadin" ' . $inline . '>' .
                       $this->mc($mc, $ezAdOptions['text_leadin']) .
                       ($urCount++ < $this->urMax ? $unreal : '') .
                       "</div>\n" . $ezAdOptions['info'] . "\n") ;
        echo $this->leadin ;
      }
    }

    function footer_leadout(){
      // if it is an admin page, don't show ads
      if (is_admin()) return ;
      echo $this->leadout ;
    }

    // ===== widget functions =====
    function widget_ezAd_ads($args) {
      extract($args);
      $ezAdOptions = $this->getAdminOptions();
      $ezAdOptions['text_widget'] = $this->handleDefaultText($ezAdOptions['text_widget'],
                                    $this->defaults['ezWidget']) ;
      $metaOptions = $this->widgetMeta() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $show_widget = $metaOptions['show_widget'] ;
      if ($show_widget == 'no') return ;
      $this->ezMax = $ezAdOptions['max_count'] ;
      $this->urMax = $ezAdOptions['max_link'] ;
      global $ezCount ;
      global $urCount ;
      if (!$ezAdOptions['force_widget']) {
        if ($ezCount > $this->ezMax) return ;
        $ezCount++;
      }
      $title = empty($ezAdOptions['title_widget']) ?
        __('Sponsored Links', 'easy-adsenser') :
        stripslashes(htmlspecialchars($ezAdOptions['title_widget'])) ;
      $border = '' ;
      if ($ezAdOptions['show_borders'] && $ezAdOptions['border_widget'] )
        $border='border:#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px ;"' .
          ' onmouseover="this.style.border=\'#' . $ezAdOptions['border_color'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'"' ;
      $unreal = '<div align="center"><font size="-3">' .
        '<a href="http://wordpress.org/extend/plugins/easy-adsenser/" ' .
        'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
        'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
        'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
        'Unreal</a></font></div>';
      echo $before_widget;
      if (!$ezAdOptions['kill_widget_title']) echo $before_title . $title . $after_title;
      $mc = $ezAdOptions['mc'] ;
      $margin =  $ezAdOptions['margin_widget'] ;
      if ($ezAdOptions['kill_inline'])
        $inline = '' ;
	  else
	    $inline = 'style="' . $show_widget . ';margin:' . $margin . 'px;' . $border. '"' ;
      echo stripslashes($ezAdOptions['info'] . "<!-- Widg[count: " . $ezCount . "] -->\n" .
                        '<div class="ezAdsense adsense adsense-widget"><div ' . $inline. '>' .
                        $this->mc($mc, $ezAdOptions['text_widget'], true) .
                        ($urCount++ < $this->urMax ? $unreal : '') .
                        "</div></div>\n" . $ezAdOptions['info'] . "\n") ;
      echo $after_widget;
    }

    function widget_ezAd_lu($args) {
      extract($args);
      $ezAdOptions = $this->getAdminOptions();
      $ezAdOptions['text_lu'] = $this->handleDefaultText($ezAdOptions['text_lu'],
                                $this->defaults['ezLU']) ;
      $title = empty($ezAdOptions['title_lu']) ? '' :
        $before_title . stripslashes(htmlspecialchars($ezAdOptions['title_lu'])) . $after_title ;
      $metaOptions = $this->widgetMeta() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $show_lu = $metaOptions['show_lu'] ;
      $border = '' ;
      if ($ezAdOptions['show_borders'] && $ezAdOptions['border_lu'] )
        $border='border:#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px;" ' .
          ' onmouseover="this.style.border=\'#' . $ezAdOptions['border_color'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $ezAdOptions['border_normal'] .
          ' solid ' . $ezAdOptions['border_width'] . 'px\'"' ;
      if ($show_lu != 'no') {
        echo $before_widget ;
        if (!$ezAdOptions['kill_widget_title']) echo $title ;
        $margin =  $ezAdOptions['margin_lu'] ;
        if ($ezAdOptions['kill_inline'])
          $inline = '' ;
	    else
	      $inline = 'style="' . $show_widget . ';margin:' . $margin . 'px;' . $border. '"' ;
        echo stripslashes('<div class="ezAdsense adsense adsense-lu"><div ' . $inline. '>' . "\n" .
                          $ezAdOptions['text_lu'] . "\n" .
                          '</div></div>') ;
        echo $after_widget ;
      }
    }

    function widget_ezAd_search($args) {
      extract($args);
      $ezAdOptions = $this->getAdminOptions();
      $ezAdOptions['text_gsearch'] = $this->handleDefaultText($ezAdOptions['text_gsearch'],
                                     $this->defaults['ezLU']) ;
      $metaOptions = $this->widgetMeta() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $title_gsearch = $metaOptions['title_gsearch'] ;
      if ($title_gsearch != 'no') {
        $title = $before_title . $title_gsearch . $after_title ;
        if ($title_gsearch == 'dark')
          $title = '<img src=" ' . $this->plugindir . '/google-dark.gif" ' .
            ' border="0" alt="[Google]" align="middle" />' ;
        if ($title_gsearch == 'light')
          $title = '<img src=" ' . $this->plugindir . '/google-light.gif" ' .
            ' border="0" alt="[Google]" align="middle" />' ;
        echo $before_widget ;
        if (!$ezAdOptions['kill_gsearch_title']) echo $title ;
        $margin =  $ezAdOptions['margin_gsearch'] ;
        if ($ezAdOptions['kill_inline'])
          $inline = '' ;
	    else
	      $inline = 'style="margin:' . $margin . 'px; "' ;
        echo stripslashes('<div class="ezAdsense adsense adsense-search"><div ' . $inline . '>' . "\n" .
                          $ezAdOptions['text_gsearch'] . "\n" .
                          '</div></div>') ;
        echo $after_widget ;
      }
    }

    function widget_ezAd_control() {
      echo '<p>Configure it at <br />' ;
      echo '<a href="options-general.php?page=easy-adsenser.php"> ';
      echo 'Settings &rarr; Easy AdSense</a>' ;
      echo '</p>' ;
    }

    function widget_ezAd_lu_control($widget_args = 1) {
      echo '<p>Configure it at <br />' ;
      echo '<a href="options-general.php?page=easy-adsenser.php"> ';
      echo 'Settings &rarr; Easy AdSense</a>' ;
      echo '</p>' ;
    }

    function register_ezAdSenseWidgets() {
      if (function_exists('wp_register_sidebar_widget')) {
        $widget_ops =
          array('classname' => 'widget_ezAd_ads', 'description' =>
                'Easy AdSense: ' . __('Show a Google AdSense block in your sidebar as a widget',
                                     'easy-adsenser'));
        wp_register_sidebar_widget('ezAd_ads', 'Google Ads',
                                   array(&$this, 'widget_ezAd_ads'), $widget_ops);
        $widget_ops =
          array('classname' => 'widget_ezAd_search', 'description' =>
                'Easy AdSense: ' . __('Show a Google Search Box in your sidebar as a widget',
                                      'easy-adsenser'));
        wp_register_sidebar_widget('ezAd_search', 'Google Search',
                                   array(&$this, 'widget_ezAd_search'), $widget_ops);
        wp_register_widget_control('ezAd_ads','Google Ads',
                                   array(&$this, 'widget_ezAd_control'));
        wp_register_widget_control('ezAd_search','Google Search',
                                   array(&$this, 'widget_ezAd_control'));
      }
    }

    function register_ezAdSenseLU() {
      if (function_exists('wp_register_sidebar_widget')) {
        for ($id = 0; $id < $this->luMax; $id++) {
          $reg_wid = 'ezad-lu-' . $id ;
          $jd = $id + 1;
          $widget_ops =
            array('classname' => 'widget_ezAd_lu', 'description' =>
                  'Easy AdSense: ' . __('Show a Google Links Unit in your sidebar as a widget',
                                        'easy-adsenser') . " ($jd)");
          wp_register_sidebar_widget($reg_wid, 'Google Link Units' . " ($jd)",
                                     array(&$this, 'widget_ezAd_lu'), $widget_ops);
          wp_register_widget_control($reg_wid ,'Google Link Units' . " ($jd)",
                                     array(&$this, 'widget_ezAd_lu_control'));
        }
      }
    }
  }
} //End Class ezAdSense

$urCount = 0 ;
$ezCount = 0 ;

// provide a replacement for htmlspecialchars_decode() (for PHP4 compatibility)
if (!function_exists("htmlspecialchars_decode")) {
  function htmlspecialchars_decode($string,$style=ENT_COMPAT) {
    $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
    if($style === ENT_QUOTES){ $translation['&#039;'] = '\''; }
    return strtr($string,$translation);
  }
}

if (class_exists("ezAdSense")) {
  $ez_ad = new ezAdSense();
  if (isset($ez_ad) && !empty($ez_ad->defaults)) {
    //Initialize the admin panel
    if (!function_exists("ezAdSense_ap")) {
      function ezAdSense_ap() {
        global $ez_ad ;
        if (function_exists('add_options_page')) {
          add_options_page('Easy AdSense', 'Easy AdSense', 9,
                           basename(__FILE__), array(&$ez_ad, 'printAdminPage'));
        }
      }
    }

    $version = (float)get_bloginfo('version') ;
    if ($version >= 2.80){
      // sidebar AdSense Widget (skyscraper)
      class ezAdsWidget extends WP_Widget {
        function ezAdsWidget() {
          $widget_ops =
            array('classname' => 'ezAdsWidget',
                  'description' =>
                  __('Show a Google AdSense block in your sidebar as a widget',
                     'easy-adsenser') );
          $this->WP_Widget('ezAdsWidget', 'Easy AdSense: Google Ads', $widget_ops);
        }
       	function widget($args, $instance) {
          // outputs the content of the widget
          global $ez_ad ;
          $ez_ad->widget_ezAd_ads($args) ;
        }

	function update($new_instance, $old_instance) {
          // processes widget options to be saved
          return $new_instance ;
	}

	function form($instance) {
          // outputs the options form on admin
          global $ez_ad ;
          $ez_ad->widget_ezAd_control() ;
        }
      }
      add_action('widgets_init',
                 create_function('', 'return register_widget("ezAdsWidget");'));

      // sidebar Search Widget
      class ezAdsSearch extends WP_Widget {
        function ezAdsSearch() {
          $widget_ops =
            array('classname' => 'ezAdsSearch',
                  'description' =>
                  __('Show a Google Search Box in your sidebar as a widget',
                     'easy-adsenser') );
          $this->WP_Widget('ezAdsSearch', 'Easy AdSense: Google Search', $widget_ops);
        }
       	function widget($args, $instance) {
          // outputs the content of the widget
          global $ez_ad ;
          $ez_ad->widget_ezAd_search($args) ;
        }

	function update($new_instance, $old_instance) {
          // processes widget options to be saved
          return $new_instance ;
	}

	function form($instance) {
          // outputs the options form on admin
          global $ez_ad ;
          $ez_ad->widget_ezAd_control() ;
        }
      }
      add_action('widgets_init',
                 create_function('', 'return register_widget("ezAdsSearch");'));

      // sidebar Link Units
      class ezAdsLU extends WP_Widget {
        function ezAdsLU() {
          $widget_ops =
            array('classname' => 'ezAdsLU',
                  'description' =>
                  __('Show a Google Links Unit in your sidebar as a widget',
                     'easy-adsenser') );
          $this->WP_Widget('ezAdsLU', 'Easy AdSense: Google Link Unit', $widget_ops);
        }
       	function widget($args, $instance) {
          // outputs the content of the widget
          global $ez_ad ;
          $ez_ad->widget_ezAd_lu($args) ;
        }

	function update($new_instance, $old_instance) {
          // processes widget options to be saved
          return $new_instance ;
	}

	function form($instance) {
          // outputs the options form on admin
          global $ez_ad ;
          $ez_ad->widget_ezAd_control() ;
        }
      }
      add_action('widgets_init',
                 create_function('', 'return register_widget("ezAdsLU");'));
    }
    else {
      add_action('plugins_loaded', array($ez_ad, 'register_ezAdSenseWidgets'));
      add_action('plugins_loaded', array($ez_ad, 'register_ezAdsenseLU')) ;
    }

    add_filter('the_content', array($ez_ad, 'ezAdSense_content'));
    $ezAdOptions = $ez_ad->getAdminOptions();
    $ez_ad->luMax = $ezAdOptions['limit_lu'] ;
    if ($ezAdOptions['allow_feeds']) {
      add_filter('the_excerpt_rss', array($ez_ad, 'ezAdSense_content'));
      add_filter('the_content_rss', array($ez_ad, 'ezAdSense_content'));
    }
    else {
      remove_filter('the_excerpt_rss', array($ez_ad, 'ezAdSense_content'));
      remove_filter('the_content_rss', array($ez_ad, 'ezAdSense_content'));
    }
    add_action('admin_menu', 'ezAdSense_ap');
    add_action('activate_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
               array(&$ez_ad, 'init'));
    add_filter('plugin_action_links', array($ez_ad, 'plugin_action'), -10, 2);
    if ($ezAdOptions['max_link'] == -1)
      add_action('wp_footer', array($ez_ad, 'footer_action'));
    else
      remove_action('wp_footer', array($ez_ad, 'footer_action'));

    if ($ezAdOptions['header_leadin'])
      add_action($ezAdOptions['header_leadin'], array($ez_ad, 'header_leadin'));

    if ($ezAdOptions['footer_leadout'])
      add_action($ezAdOptions['footer_leadout'], array($ez_ad, 'footer_leadout'));
  }
}
?>
