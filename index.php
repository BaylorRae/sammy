<?php
include 'sammy.php';

get('/', function() {
	return 'Hello World!';
});

get('/users', function($sammy) {
  
  // example.com/users.json
  $sammy->format('json', function() {
      return json_encode(array('users' => array(
          array(
            'username' => 'bob',
            'api_key' => '5829-akeK-Ai49'
            ),
          array(
            'username' => 'sam',
            'api_key' => '4938-Akeb-Le32'
            )
        )));
  });
  
  if( !$sammy->format )
    return 'Hello';
});

get('/hello/(.*?)', function($sammy) {
	return 'Hello '.$sammy->segment(2);
});

$sammy->run();