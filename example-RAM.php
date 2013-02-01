<?php

/*\
 * PHP Console class - Example RAM
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

Console::begin($argv, array('plain' => true));

/*
 * WORK PROCESS ===============================================
 */

function getSystemMemInfo()
{
	$data = explode("\n", file_get_contents("/proc/meminfo"));
	$meminfo = array();
	foreach ($data as $line) {
		if($line != '') {
			list($key, $val) = explode(':', $line);
			$meminfo[$key] = trim($val);
		}
	}
	return $meminfo;
}

$mem = getSystemMemInfo();
$memTotal = ((int)str_replace(' kB', '', $mem['MemTotal'])) / 1000;
$memFree = ((int)str_replace(' kB', '', $mem['MemFree'])) / 1000;
$memUsed = (int)($memTotal - $memFree);

Console::graph(array(
	'label' => 'Computer ressources used (RAM):',
	'xLabels' => array(date('H:i:s')),
	'yLabels' => '{{ value }} Mo',
	'yMax' => $memTotal,
	'colors' => array(
		array(ConsoleColors::blue, ConsoleBackgrounds::blue)
	),
	'data' => array(
		'RAM used' => array($memUsed)
	)
));

Console::line();
Console::line('  Actual used: '.$memUsed.' Mo', ConsoleColors::cyan, ConsoleStyles::bold);
Console::line();
Console::line();

/*
 * ============================================================
 */
