<?php

/*\
 * PHP Console class - Example Widgets
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

Console::begin($argv, array(
	'plain' => true
), array(
	'Example Widgets',
	'My awesome widgets'
));

/*
 * WORK PROCESS ===============================================
 */

Console::line();
Console::line('  Progress bar widget :');
Console::line();
for($i = 0; $i <= 100; $i++) {
	Console::progress($i);
	time_nanosleep(0, 25000000);
}
Console::line();

Console::line();
Console::line('  Table widget :');
Console::line();

Console::table(array(
	array('field 1', 'field 2', 'field 3', 'field 4', 'field 5'),
	array('value 1.1', 'value 1.2', 'value 1.3', 'value 1.4', 'value 1.5'),
	array('value 2.1', 'value 2.2', 'value 2.3', 'value 2.4', 'value 2.5'),
	array('value 3.1', 'value 3.2', 'value 3.3', 'value 3.4', 'value 3.5'),
	array('value 4.1', 'value 4.2', 'value 4.3', 'value 4.4', 'value 4.5'),
	array('value 5.1', 'value 5.2', 'value 5.3', 'value 5.4', 'value 5.5'),
	array('value 6.1', 'value 6.2', 'value 6.3', 'value 6.4', 'value 6.5'),
	'-',
	array('value 7.1', 'value 7.2', 'value 7.3', 'value 7.4', 'value 7.5')
));

Console::line();

Console::line();
Console::line('  Drawing :');
Console::line();

Console::draw(array(
	'0    0  00000',
	' 0  0   0   0',
	'  00    00000',
	' 0  0   0   0',
	'0    0  0   0'
), ConsoleBackgrounds::green, ConsolePositions::right);

Console::line();

$text = Console::input('  Input text here: ', ConsoleColors::green);
Console::line('  You\'ve writed "'.$text.'"');

Console::badge('Badge Widget', ConsolePositions::right, ConsoleColors::none, ConsoleStyles::none, ConsoleBackgrounds::none, ConsoleBackgrounds::cyan);

/*
 * ============================================================
 */

//Console::easterEggLogo();

Console::end();
