<?php

/*\
 * PHP Console class - Example Graphs
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

Console::graph(array(
	'label' => 'Proportion of men, women and kids on each floor:',
	'xLabels' => array('1st floor', '2nd floor', '3rd floor', '4rd floor', '5th floor', '6th floor'),
	'yLabels' => '{{ value }}%',
	'colors' => array(
		array(ConsoleColors::blue, ConsoleBackgrounds::blue),
		array(ConsoleColors::purple, ConsoleBackgrounds::purple),
		array(ConsoleColors::yellow, ConsoleBackgrounds::yellow)
	),
	'data' => array(
		'men' => array(100, 100, 100, 100, 100, 100, 100),
		'women' => array(50, 50, 50, 50, 50, 50, 50),
		'kids' => array(75, 75, 75, 75, 75, 75, 75)
	)
));

Console::graph(array(
	'label' => 'Apache errors log:',
	'xLabels' => array('25 min ago', '20 min ago', '15 min ago', '10 min ago', '5 min ago'),
	'colors' => array(
		array(ConsoleColors::green, ConsoleBackgrounds::green),
		array(ConsoleColors::red, ConsoleBackgrounds::red)
	),
	'data' => array(
		'good' => array(3200, 2300, 1600, 800, 600),
		'error' => array(2000, 1500, 1000, 600, 400),
	)
));

Console::graph(array(
	'label' => 'Computer ressources used (RAM):',
	'xLabels' => array('25 min ago', '20 min ago', '15 min ago', '10 min ago', '5 min ago'),
	'colors' => array(
		array(ConsoleColors::blue, ConsoleBackgrounds::blue)
	),
	'data' => array(
		'RAM used' => array(2048, 1024, 1600, 800, 600)
	)
));

Console::line();

/*
 * ============================================================
 */
