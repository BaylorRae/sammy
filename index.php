<?php
include 'sammy.php';

get('/', function() {
	return 'Hello World!';
});

get('/bye', function() {
	return 'Goodbye World!';
});

$sammy->run();