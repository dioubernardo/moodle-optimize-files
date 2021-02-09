<?php

$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => 'optimizer_handler',
        'includefile' => '/local/optimizer/lib.php',
		'priority'    =>  999, 
        'internal'    => false
    )
);
