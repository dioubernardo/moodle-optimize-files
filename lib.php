<?php
/**
 * See:
 *  https://docs.moodle.org/dev/Callbacks
 *  https://docs.moodle.org/dev/File_API
 * */

function local_optimizer_after_file_created($record){
    global $DB;
    if (
        $record->component != 'assignfeedback_editpdf' and 
        $record->component != 'core' and
        $record->component != 'core_admin' and
        !($record->component == 'user' and $record->filearea == 'icon') and
        !($record->component == 'user' and $record->filearea == 'draft') and
        (
            $record->mimetype == 'video/mp4' or
            $record->mimetype == 'application/pdf' or
            $record->mimetype == 'image/png' or
            $record->mimetype == 'image/jpeg'
        )
    ){
        $DB->execute('insert ignore into {optimizer_files} values (:contenthash, 0)', [
            'contenthash' => $record->contenthash
        ]);
    }
}

function local_optimizer_after_file_deleted($record){
    global $DB;

    $params = [
        'contenthash' => $record->contenthash
    ];
    if (!$DB->record_exists_sql('select 1 from mdl_files where contenthash=:contenthash', $params)){
        $DB->execute('delete from {optimizer_files} where contenthash=:contenthash', $params);
    }
}
