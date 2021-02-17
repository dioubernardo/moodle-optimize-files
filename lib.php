<?php

function local_optimizer_after_file_created($record){
    error_log("created: ".var_export($record, true));
}

function local_optimizer_after_file_deleted($record){
    error_log("deleted: ".var_export($record, true));
}
