<?php
	// directories
	define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
	define('DATA_DIR', DIRECTORY_SEPARATOR .'data'. DIRECTORY_SEPARATOR);
	define('INCLUDES_DIR', ROOT_DIR .'includes'. DIRECTORY_SEPARATOR);
	define('PUBLIC_DIR', ROOT_DIR .'public'. DIRECTORY_SEPARATOR);
	define('PUBLIC_DATA_DIR', PUBLIC_DIR .'data'. DIRECTORY_SEPARATOR);
	define('RELEASE_DIR', ROOT_DIR .'release'. DIRECTORY_SEPARATOR);
	
	// uris
	define('DEV_PUBLIC_URI', 'http://localhost/');
	define('DOCKER_PUBLIC_URI', (getenv("PUBLIC_URI") ? getenv("PUBLIC_URI") : 'http://localhost:8080/'));
	
	// database
	define('DB_HOST', (getenv("DB_HOST")?getenv("DB_HOST"):''));
	define('DB_USER', (getenv("DB_USER")?getenv("DB_USER"):''));
	define('DB_PASSWORD', (getenv("DB_PASSWORD")?getenv("DB_PASSWORD"):''));
	define('DB_NAME', (getenv("DB_NAME")?getenv("DB_NAME"):''));
	define('DB_CHARSET', 'utf8mb4');
	
	// includes
	require_once(INCLUDES_DIR .'database.mysql.pdo.php');
	require_once(INCLUDES_DIR .'helpers.php');
	
	// sensitive config
	require_once(ROOT_DIR .'config.sensitive.php');
	
	// globals
	global $character_names;
	$character_names = [
		"Isaac",
		"Magdalene",
		"Cain",
		"Judas",
		"???",
		"Eve",
		"Samson",
		"Azazel",
		"Lazarus",
		"Eden",
		"The Lost",
		"Lilith",
		"Keeper",
		"Apollyon",
		"The Forgotten",
		"Bethany",
		"Jacob and Esau",
		"Tainted Isaac",
		"Tainted Magdalene",
		"Tainted Cain",
		"Tainted Judas",
		"Tainted ???",
		"Tainted Eve",
		"Tainted Samson",
		"Tainted Azazel",
		"Tainted Lazarus",
		"Tainted Eden",
		"Tainted Lost",
		"Tainted Lilith",
		"Tainted Keeper",
		"Tainted Apollyon",
		"Tainted Forgotten",
		"Tainted Bethany",
		"Tainted Jacob",
	];
	
	global $boss_names;
	$boss_names = [
		"Mother",
		"??? (Boss)",
		"The Beast",
		"Dogma",
		"Delirium",
		"Ultra Greed",
		"Ultra Greedier",
		"Hush",
		"Mega Satan",
		"Isaac",
		"Satan",
		"The Lamb",
		"Isaac (Boss)",
		"Blue Baby",
		"Boss Rush",
	];
	
	global $challenge_names;
	$challenge_names = [
		"Pitch Black",
		"High Brow",
		"Head Trauma",
		"Darkness Falls",
		"The Tank",
		"Solar System",
		"Suicide King",
		"Cat Got Your Tongue",
		"Demo Man",
		"Cursed!",
		"Glass Cannon",
		"When Life Gives You Lemons",
		"Beans!",
		"It's in the Cards",
		"Slow Roll",
		"Computer Savvy",
		"Waka Waka",
		"The Host",
		"The Family Man",
		"Purist",
		"XXXXXXXXL",
		"SPEED!",
		"Blue Bomber",
		"PAY TO PLAY",
		"Have a Heart",
		"I RULE!",
		"BRAINS!",
		"PRIDE DAY!",
		"Onan's Streak",
		"The Guardian",
		"Backasswards",
		"Aprils Fool",
		"Pokey Mans",
		"Ultra Hard",
		"Pong",
		"Scat Man",
		"Bloody Mary",
		"Baptism by Fire",
		"Isaac's Awakening",
		"Seeing Double",
		"Pica Run",
		"Hot Potato",
		"Cantripped!",
		"Red Redemption",
		"DELETE THIS",
	];