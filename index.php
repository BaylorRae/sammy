<?php
include 'sammy.php';

get('/', function() {
	return 'Hello World!';
});

get('/hello/(.*?)', function($sammy) {
	return 'Hello '.$sammy->segment(2);
});

$sammy->run();