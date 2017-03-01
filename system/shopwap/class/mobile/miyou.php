<?php
$op = empty($_GP['op']) ? 'display': $_GP['op'];

if($op == 'display'){
	include themePage('miyou');
}else if($op='invite'){
	include themePage('invite_miyou');
}
