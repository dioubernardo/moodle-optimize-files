<?php

function optimizer_cron() {
	global $DB;

	error_log("rodando o cron");

	$ultimoMaxId = (int)get_config('optimizer_maxid');
	$atualMaxId = 999;



//	set_config('optimizer_maxid', $atualMaxId);
}
