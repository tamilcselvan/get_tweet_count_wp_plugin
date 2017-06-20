<?php
/*
 * Plugin Name: Acs Twitter API
 * Plugin URI: http://agilecyber.co.uk
 * Description: The Twitter Api to get tweet count
 * Version: 1.0
 * Author: Tamil Selvan
 * Author URI: http://tamilcselvan.com
 * License: GPL2
 */
defined ( 'ABSPATH' ) or die ( 'No script kiddies please!' );

require plugin_dir_path( __FILE__ ) . 'includes/acs-twitter-shortcode.php';
if (! function_exists ( 'acs_debug' )) {
	function acs_debug($mixed, $debug = false) {
		if( $debug ) {
			$ip = array ();
			if (! empty ( $ip ) && in_array($_SERVER['REMOTE_ADDR'], $ip)) {
				if (is_array ( $mixed ) || is_object ( $mixed )) {
					echo '<pre>', print_r ( $mixed, true ), '</pre>';
				} else {
					var_dump ( $mixed );
				}
			}
		}
	}
}

/* Create a Admin Menu for the plugin */
function acs_twitter_api_menu() {
	add_menu_page ( 'ACS Twitter API', 'ACS Twitter API', 'manage_options', 'acs-plugin', 'acs_plugin_init' );
}

add_action ( 'admin_menu', 'acs_twitter_api_menu' );
function acs_plugin_init() {
	?>
<style type="text/css">
.acs label {
	display: block;
}

.acs input[type="text"] {
	width: 80%;
}
</style>
<div class="wrap acs">
	<h2>Twitter API Details</h2>
        <?php
	$acs_twitter_access_token = $acs_twitter_access_secret = $acs_twitter_consumer_key = $acs_twitter_consumer_secret = "";
	if (isset ( $_POST ['acs_twitter_api'] ) && $_POST ['acs_twitter_api'] == 'Y') {
		$acs_twitter_access_token = $_POST ['acs_twitter_access_token'];
		$acs_twitter_access_secret = $_POST ['acs_twitter_access_secret'];
		$acs_twitter_consumer_key = $_POST ['acs_twitter_consumer_key'];
		$acs_twitter_consumer_secret = $_POST ['acs_twitter_consumer_secret'];
		$acs_twitter_username = $_POST ['acs_twitter_username'];
		
		$twitter_settings = array (
				'oauth_access_token' => $acs_twitter_access_token,
				'oauth_access_token_secret' => $acs_twitter_access_secret,
				'consumer_key' => $acs_twitter_consumer_key,
				'consumer_secret' => $acs_twitter_consumer_secret 
		);
		
		update_option ( 'acs_plugin_twitter_api', $twitter_settings );
		update_option ( 'acs_plugin_twitter_username', $acs_twitter_username );
		?>
            <div class="updated">
		<p>
			<strong><?php _e('Options saved.' ); ?></strong>
		</p>
	</div>
            <?php
	}
	$acs_twitter_options = get_option ( 'acs_plugin_twitter_api' );
	// var_dump($acs_twitter_options);
	if (is_array ( $acs_twitter_options )) {
		$acs_twitter_access_token = '';//$acs_twitter_options ['oauth_access_token'];
		$acs_twitter_access_secret = '';//$acs_twitter_options ['oauth_access_token_secret'];
		$acs_twitter_consumer_key = $acs_twitter_options ['consumer_key'];
		$acs_twitter_consumer_secret = $acs_twitter_options ['consumer_secret'];
	}
	$acs_twitter_username = get_option ( 'acs_plugin_twitter_username' );
	
	?>
    <form name="acs_twitter_api_details" method="post" action="">
		<input type="hidden" name="acs_twitter_api" value="Y">
		<p>
			<label>CONSUMER KEY</label> <input type="text"
				name="acs_twitter_consumer_key" required
				value="<?php echo $acs_twitter_consumer_key; ?>">
		</p>
		<p>
			<label>CONSUMER SECRET</label> <input type="text"
				name="acs_twitter_consumer_secret" required
				value="<?php echo $acs_twitter_consumer_secret; ?>">
		</p>

		<p>
			<label>TWITTER USERNAME</label> <input type="text"
				name="acs_twitter_username" required
				value="<?php echo $acs_twitter_username; ?>">
		</p>


		<p class="submit">
			<input type="submit" name="Submit" value="Update Options" />
		</p>
	</form>
	<h2>Tweet count: <?php echo (int) get_option('acs_twitter_tweet_count'); ?></h2>
</div>
<?php
}

/* Update the tweet count into the database */
function get_twitter_tweet_count_from_api() {
	//ini_set("display_errors", "On");
	//delete_transient ( 'acs_tweet_count_cron' );
	if (false === get_transient ( 'acs_tweet_count_cron' )) {
		set_transient ( 'acs_tweet_count_cron', true, 60 * 60 * 2 );
		
		$acs_twitter_options = get_option ( 'acs_plugin_twitter_api' );
		$acs_twitter_options ['oauth_access_token'] = '';
		$acs_twitter_options ['oauth_access_token_secret'] = '';
		$acs_plugin_twitter_username = get_option ( 'acs_plugin_twitter_username' );
		if (is_array ( $acs_twitter_options ) && ! empty ( $acs_plugin_twitter_username )) {
			
			$a = getTwitterTweetCountJSON ( $acs_twitter_options, $acs_plugin_twitter_username );
			
			$b = json_decode ( $a, true );
			
			if (json_last_error_msg () !== FALSE && is_array ( $b )) {
				if (isset ( $b [0] ["statuses_count"] )) {
					update_option ( 'acs_twitter_tweet_count', ( int ) $b [0] ["statuses_count"] );
				}
			}
		}
	}
}

/* Get the twitter tweet count from API in JSON format */
function getTwitterTweetCountJSON($acs_twitter_options, $acs_plugin_twitter_username) {
	
	
	
	require plugin_dir_path( __FILE__ ) . 'includes/TwitterAPIExchange.php';
	
	$url = 'https://api.twitter.com/1.1/users/lookup.json';
	$requestMethod = 'GET';
	$getfield = '?screen_name=' . esc_attr ( $acs_plugin_twitter_username );
	try {
		$twitter = new TwitterAPIExchange ( $acs_twitter_options );
		$a = $twitter->setGetfield ( $getfield )->buildOauth ( $url, $requestMethod )->performRequest ();
		return $a;
	} catch ( Exception $e ) {
		echo $e->getMessage();
	}
	return array ();
}

add_action ( 'init', 'get_twitter_tweet_count_from_api' );
?>