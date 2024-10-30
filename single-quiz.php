<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	// Get site information
	$url = get_bloginfo('url');
	$img = get_site_icon_url(512, plugins_url( 'iqz_logo.png', __FILE__ ), [], false, true);
	$blogName = get_bloginfo('name');
	
	// Get quiz information
	$title = get_the_title();
	$description = get_post_meta(get_the_ID(), 'iqz_description', true);
	$questions = get_post_meta(get_the_ID(), 'iqz_questions', true);
	$number = get_post_meta(get_the_ID(), 'iqz_number', true);
	$alphanumeric = get_post_meta(get_the_ID(), 'iqz_alphanumeric', true);
	$email = get_post_meta(get_the_ID(), 'iqz_email', true);
	$notify = get_post_meta(get_the_ID(), 'iqz_notify', true);
	
	// Add scripts and styles
	wp_register_script( 'iqz_scripts', plugins_url( 'scripts.js', __FILE__ ), [], false, true);
	$quizData = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce('infinity-quiz'),
		'identity' => $notify,
		'questions' => $questions,
		'number' => $number,
		'alphanumeric' => $alphanumeric,
		'to' => $email
	);
	wp_localize_script( 'iqz_scripts', 'quizData', $quizData );
	wp_enqueue_script( 'iqz_scripts' );
	
	wp_enqueue_style( 'iqz_style', plugins_url( 'style.css', __FILE__ ));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title; ?></title>
	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <nav id="mainNav">
		<div id="nav-head">
			<a href="<?php echo $url; ?>"><img src="<?php echo $img; ?>" alt="<?php echo $blogName; ?> Logo"></a>
			<h1><?php echo $title; ?></h1>
		</div>
		<span id="hide">▼</span>
		<div id="nav-form">
			<form id="details" onsubmit="return false;">
				<input type="text" id="name" placeholder="Name" required>
				<input type="email" id="email" placeholder="Email" required>
				<input type="submit" id="save" value="Submit" required>
			</form>
		</div>
    </nav>

    <div id="main">
		<p id="details-info" class="info">Please input your name and email before taking the test.</p>
		<p id="option-info" class="info">Please choose an option.</p>
		<p id="email-info" class="info">Your results have been sent to your email.</p>
        <h2><?php echo $description;?></h2>
        <div id="question">
            <h3 id="question-title">Question 1</h3>
            <p id="question-text">Loading...</p>

            <form id="options">
            </form>

            <div id="best">
                <div id="first" class="best">
                    <h4 id="first-title">Your most prominent spiritual gift is: </h4>
                    <p id="first-score" class="score">Score: X</p>
                </div>
                <div id="second" class="best">
                    <h4 id="second-title">Your second most prominent spiritual gift is: </h4>
                    <p id="second-score" class="score">Score: Y</p>
                </div>
                <div id="third" class="best">
                    <h4 id="third-title">Your third most prominent spiritual gift is: </h4>
                    <p id="third-score" class="score">Score: Z</p>
                </div>
                <ul id="all-scores" class="best"></ul>
            </div>
        </div>
    </div>
	
    <footer>
        <button id="previous">Previous</button><button id="next">Next</button>
    </footer>
	
	<?php wp_footer();?>
</body>
</html>