<?php
return [
    'cron' => true, // true, false
    'selfCron' => 1, // Send message after refresh page by users ( false or count of letters for one refresh )
    'tableCron' => 'cron', // Name of the cron table
    'image' => 'GD', // GD, Magic
    'password_min_length' => 4, // Min password length
    'visitor' => true, // save user information to the database?
    'token' => 'KjsafkjAdglLIG:g7p89:OHID@)p', // defense from CSRF attacks
	'mobile' => true, //use mobile application
];