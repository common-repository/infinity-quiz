<?php
/*
Plugin Name: Infinity Quiz
Description: Easily create dynamic quizes with customised responses
Version: 1.0.4
Author: Akash Saggar
Author URI: https://www.linkedin.com/in/akash-s-5b459b121
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Flush rewrite rules on plugin activation
function iqz_flushRewrites() {
	iqz_registerCustomPost();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'iqz_flushRewrites' );

// Register custom post type
function iqz_registerCustomPost() {
	$singular = 'Quiz';
	$plural = 'Quizzes';
	$slug = str_replace( ' ', '_', strtolower( $singular ) );
	$labels = array(
		'name' 					=> $plural,
		'singular_name' 		=> $singular,
		'add_new' 				=> 'Add New',
		'add_new_item'		  	=> 'Add New ' . $singular,
		'edit'					=> 'Edit',
		'edit_item'				=> 'Edit ' . $singular,
		'new_item'				=> 'New ' . $singular,
		'view' 					=> 'View ' . $singular,
		'view_item' 			=> 'View ' . $singular,
		'search_term'   		=> 'Search ' . $plural,
		'parent' 				=> 'Parent ' . $singular,
		'not_found'				=> 'No ' . $plural .' found',
		'not_found_in_trash'	=> 'No ' . $plural .' in Trash'
		);
	$args = array(	
		'labels'				=> $labels,
		'public'              	=> true,
		'publicly_queryable'	=> true,
		'exclude_from_search'	=> false,
		'show_in_nav_menus'		=> true,
		'show_ui'             	=> true,
		'show_in_menu'        	=> true,
		'show_in_admin_bar'   	=> true,
		'menu_position'       	=> 10,
		'menu_icon'           	=> 'dashicons-list-view',
		'can_export'          	=> true,
		'delete_with_user'    	=> false,
		'hierarchical'        	=> false,
		'has_archive'         	=> true,
		'query_var'           	=> true,
		'capability_type'     	=> 'post',
		'map_meta_cap'        	=> true,
		// 'capabilities' 		=> array(),
		'rewrite'             	=> array(
			'slug' => $slug,
			'with_front' => true,
			'pages' => true,
			'feeds' => true,
		),
		'supports'				=> array(
			'title',
			'author',
		)
	);
	register_post_type( $slug, $args );
}
add_action( 'init', 'iqz_registerCustomPost' );

// Add custom fields to quiz posts
function iqz_addFields() {	
    add_meta_box(
      'iqz_meta',
      'Quiz Details',
      'iqz_displayMeta',
      'quiz',
      'normal',
      'high'
    );
	add_meta_box(
      'iqz_shortcode',
      'Quiz Shortcode',
      'iqz_displayShortcode',
      'quiz',
      'normal',
      'high'
    );
}
add_action( 'add_meta_boxes', 'iqz_addFields' );

// Display shortcode on quiz posts
function iqz_displayShortcode( $post ) {
	?><p>Once this quiz is published, you can use this shortcode to add it to any page.</p>
	<code>[infinity-quiz quiz="<?php echo $post -> post_name; ?>"]</code><?php
}

// Display custom meta box on quiz posts
function iqz_displayMeta( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'iqz_nonce' );
	$iqz_storedMeta = get_post_meta( $post ->ID );
	?>
	
	<div class="iqz_field">
		<label for="iqz_description">Please provide a short description of your quiz</label>
		<input name="iqz_description" id="iqz_description" placeholder="Find out which Disney character you are with our awesome quiz!" value="<?php echo esc_attr( $iqz_storedMeta['iqz_description'][0] ); ?>">
	</div>
	
	<div class="iqz_field">
		<label for="iqz_questions">Please input your questions.</label>
		<textarea id="iqz_questions" name="iqz_questions" rows="7" placeholder="Enter your questions and categories, comma separated. Have at least 3 questions.&#10;Do you like flying carpets?,Aladdin&#10;Are you a fan of green?,Jasmine&#10;Do you like the ocean?,Ariel"><?php echo esc_textarea( $iqz_storedMeta['iqz_questions'][0] ); ?></textarea>
	</div>
	
	<div class="iqz_field">
		<label for="iqz_number">How many possible answers should there be? Choose between 2-4.</label>
		<input type="number" required min="2" max="4" id="iqz_number" name="iqz_number" placeholder="2, 3, or 4" value="<?php echo (isset($iqz_storedMeta['iqz_number'][0])) ? esc_attr( $iqz_storedMeta['iqz_number'][0] ) : "3";?>">
	</div>
	
	<div class="iqz_field iqz_radioField">
		<label>Should answers be in words or numbers?.</label>
		
		<div class="iqz_field"><input type="radio" name="iqz_alphanumeric" id="numeric" value="numeric" 
		<?php if (!($iqz_storedMeta['iqz_alphanumeric'][0] === "alphabetical") || $iqz_storedMeta['iqz_alphanumeric'][0] === "numeric") echo 'checked'; ?>>
		<label class="radioLabel" for="numeric">Numeric</label></div>
		
		<div class="iqz_field"><input type="radio" name="iqz_alphanumeric" id="alphabetical" value="alphabetical" 
		<?php if ($iqz_storedMeta['iqz_alphanumeric'][0] === "alphabetical") echo 'checked';?>>
		<label class="radioLabel" for="alphabetical">Alphabetical</label></div>
	</div>
	
	<div class="iqz_field">
		<label for="iqz_email">If you want all responses to be sent to you, please input emails below.</label>
		<input type="email" id="iqz_email" name="iqz_email" placeholder="john@example.com,second@email.com" multiple value="<?php echo esc_attr( $iqz_storedMeta['iqz_email'][0] ); ?>">
	</div>
	
	<div class="iqz_field">
		<label for="iqz_notify">Do you want to collect user names/emails? They will also be their emailed quiz results if yes.</label>
		<input type="checkbox" id="iqz_notify" name="iqz_notify" value="checked" <?php echo esc_attr( $iqz_storedMeta['iqz_notify'][0] );?>>
	</div>
	<?php
}

// Save meta data when changed
function iqz_saveMeta( $post_id ) {
	// Checks save status
    $autosave = wp_is_post_autosave( $post_id );
    $revision = wp_is_post_revision( $post_id );
    $valid_nonce = ( isset( $_POST[ 'iqz_nonce' ] ) && wp_verify_nonce( $_POST[ 'iqz_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
	$user = current_user_can( 'contributor' );
    
	// Exits script depending on save status
    if ( $autosave || $revision || !$valid_nonce || !user ) return;
    
	// Save changes to the database
	if ( isset( $_POST[ 'iqz_description' ] ) ) update_post_meta( $post_id, 'iqz_description', sanitize_text_field( $_POST[ 'iqz_description' ] ) );
	if ( isset( $_POST[ 'iqz_questions' ] ) ) update_post_meta( $post_id, 'iqz_questions', sanitize_textarea_field( $_POST[ 'iqz_questions' ] ) );
	if ( isset( $_POST[ 'iqz_email' ] ) ) update_post_meta( $post_id, 'iqz_email', implode( ",", array_map('sanitize_email', explode(",", sanitize_text_field($_POST[ 'iqz_email' ])))) );
	if ( isset( $_POST[ 'iqz_number' ] ) ) update_post_meta( $post_id, 'iqz_number', intval( sanitize_text_field( $_POST[ 'iqz_number' ] ) ) );
	if ( isset( $_POST[ 'iqz_alphanumeric' ] ) ) update_post_meta( $post_id, 'iqz_alphanumeric', sanitize_text_field( $_POST[ 'iqz_alphanumeric' ] ) );
	
	if ( isset( $_POST[ 'iqz_notify' ] ) ) update_post_meta( $post_id, 'iqz_notify', sanitize_text_field( $_POST[ 'iqz_notify' ] ) );
	else update_post_meta( $post_id, 'iqz_notify', "false" );
}
add_action( 'save_post', 'iqz_saveMeta' );

// Enque scripts/styles for admin page
function iqz_admin_enqueue_scripts() {
	//These varibales allow us to target the post type and the post edit screen.
	global $pagenow, $typenow;
	if ( ($pagenow == 'post.php' || $pagenow == 'post-new.php') && $typenow == 'quiz' ) {
		wp_enqueue_style( 'iqz_admin-styles', plugins_url( 'admin-styles.css', __FILE__ ) );
	}
}
//This hook ensures our scripts and styles are only loaded in the admin.
add_action( 'admin_enqueue_scripts', 'iqz_admin_enqueue_scripts' );

// Shortcode
function iqz_shortcode ( $atts, $content = null ) {
	return '<iframe frameborder="0" style="width:100%;height:85vh;" src="'.get_bloginfo('url').'/quiz/'.$atts['quiz'].'/"></iframe>';
}
add_shortcode ( 'infinity-quiz', 'iqz_shortcode');

// Custom post template
function iqz_template( $original_template ) {
	// Only display this template for quizzes
	if ( get_query_var( 'post_type' ) !== 'quiz' || !is_singular('quiz') ) return $original_template;

	return plugin_dir_path( __FILE__ ) . 'single-quiz.php';
}
add_action( 'template_include', 'iqz_template' );

// Function to send email to user with quiz results
function iqz_emailResults() {
	if ($_SERVER['REQUEST_METHOD'] === "POST") {
		if (!check_ajax_referer('infinity-quiz', 'security', false)) wp_die("There was a security error when emailing your results.", 401);

		// Get input data
		$name = sanitize_text_field($_POST["name"]);
		$email = sanitize_email($_POST["email"]);
		$to = array_map( 'sanitize_email', explode(",", sanitize_text_field($_POST["to"])) );
		$values = explode(",", $_POST["values"]); // Values and titles are sanitized further below
		$titles = explode(",", $_POST["titles"]);
		$test = sanitize_text_field($_POST["test"]);

		// Construct email
		$subject = $test.": ".$name;
		$message = "<h3>Here are your test results, ".$name.".</h3><ul>";
		for ($i = 0; $i < count($titles); $i++) $message .= "<li><b>".sanitize_text_field($titles[$i])."</b>: ".sanitize_text_field($values[$i])."</li>";
		$message .= "</ul><br><a href='mailto:".$email."'>".$email."</a>";
		$headers = "Content-type: text/html";

		$adminMail = true;
		$userMail = true;
		// Send emails
		if($to != "") $adminMail = wp_mail($to, $subject, $message, $headers);
		if($email != "") $userMail = wp_mail($email, $subject, $message, $headers);
		
		// Send response
		if (!$userMail || !$adminMail) wp_die("There was an error sending your results. You can try again by going to the previous question, then next again.", 400);
		else wp_die("Your results have been sent to your email.", 202);
	}
}
add_action( 'wp_ajax_nopriv_iqz_emailResults', 'iqz_emailResults' );
add_action( 'wp_ajax_iqz_emailResults', 'iqz_emailResults' );
?>