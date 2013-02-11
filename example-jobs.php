<?php

/*\
 * PHP Console class - Example Jobs
 * This file is part of the PHP Console library.
 *
 * (c) 2013 Xavier Boubert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * CONFIGURATION ==============================================
 *
 * -nocolors	= Remove coloration
 * -nobash		= Remove coloration + Text structure
 *
 * ============================================================
 *
\*/

require_once 'class.console.php';

Console::beginJobs($argv, array(
	'Example Jobs',
	'My awesome jobs'
), array(
	't1' => 'task number 1...',
	't2' => 'task number 2...',
	't3' => 'manual task number 3...',
	't4' => 'task number 4...'
));

/*
 * WORK PROCESS ===============================================
 */

Console::jobProgress('t1', 0);

// Progress Job 1 to 99%
for($i = 0; $i < 100; $i++) {
	Console::jobProgress('t1', $i);
	time_nanosleep(0, 20000000);
}

// Success Job 1
if(true) {
	Console::jobSuccess('t1');
}
else {
	Console::jobFail('t1');
}

Console::jobProgress('t2', 0);

for($i = 0; $i < 100; $i++) {
	Console::jobProgress('t2', $i);
	time_nanosleep(0, 20000000);
}

if(false) {
	Console::jobSuccess('t2');
}
else {
	Console::jobFail('t2');
}

Console::jobProgress('t3', 0);
Console::jobUserAction('t3', 'Done? (press ENTER when done)');
Console::jobSuccess('t3');

Console::jobProgress('t4', 0);

for($i = 0; $i < 100; $i++) {
	Console::jobProgress('t4', $i);
	time_nanosleep(0, 20000000);
}

Console::jobWarning('t4');

/*
 * ============================================================
 */

Console::jobsBadge();

//Console::easterEggLogo();

Console::end();
