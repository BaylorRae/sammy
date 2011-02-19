<?php
include 'sammy.php';

get('/', function() {
?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <title>HTML5 Document</title>
  <meta charset="utf-8" />

  </head>
  <body>

    <div id="results"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
    <script>

      $('#results').load('/ajax');

    </script>

  </body>
  </html>
<?php
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
  
  $sammy->format('json', function($sammy) {
    return json_encode(array('name', $sammy->segment(2)));
  });
  
  if( !$sammy->format )
  	return 'Hello '.$sammy->segment(2);
});

ajax('/ajax', function() {
  return 'ha, you found me!';
});

$sammy->run();