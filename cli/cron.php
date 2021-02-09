<?php

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

/* Impedir a execução simultanea */


/* Update table optimizer_files */
$anteriorMaxId = (int)get_config('optimizer', 'maxid');
$atualMaxId = $DB->get_field_sql('SELECT max(id) from {files}');

if ($atualMaxId != $anteriorMaxId){
    set_config('maxid', $atualMaxId, 'optimizer');
    $DB->execute('
        insert ignore into {optimizer_files}
        select distinct
            contenthash,
            0
        from
            {files}
        where
            id between :min and :max and 
            component != "assignfeedback_editpdf" and
            mimetype in("video/mp4","application/pdf","image/png","image/jpeg")
	', [
		'min' => $anteriorMaxId,
		'max' => $atualMaxId
	]);
}

/* Optimize files */
