<?php
namespace system\model;

class RecordMode {
	const MODE_NOBODY = 0;
	const MODE_SU = 1;
	const MODE_SU_OWNER = 2;
	const MODE_SU_OWNER_ADMINS = 3;
	const MODE_REGISTERED = 4;
	const MODE_ANYONE = 5;
}
