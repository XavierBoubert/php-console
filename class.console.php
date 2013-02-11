<?php

/*\
 * PHP Console class
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

class ConsoleColors {
    const none			= "\033[0m";
	const black			= "\033[0;30m";
	const red			= "\033[0;31m";
	const green			= "\033[0;32m";
	const yellow		= "\033[0;33m";
	const blue			= "\033[0;34m";
	const purple		= "\033[0;35m";
	const cyan			= "\033[0;36m";
	const white			= "\033[0;37m";
}

class ConsoleStyles {
    const none			= 0;
    const bold			= 1;
    const underline		= 4;
}

class ConsoleBackgrounds {
    const none			= "\033[0m";
    const black			= "\033[40m";
	const red			= "\033[41m";
	const green			= "\033[42m";
	const yellow		= "\033[43m";
	const blue			= "\033[44m";
	const purple		= "\033[45m";
	const cyan			= "\033[46m";
	const white			= "\033[47m";
}

class ConsolePositions {
    const top			= 1;
    const right			= 2;
	const bottom		= 4;
	const left			= 8;
}

class Console {

	/*
	 * CONFIGURATION
	 */

	const returnChara = "\n";
	const defaultBorderColor = ConsoleBackgrounds::blue;

	private static $config = array(
		'isColorsEnabled'	=> true,
		'isBashEnabled'		=> true,
		'maxCharas'			=> 79,
		'plain'				=> false
	);

	public static function config($key = false, $value = null) {
		if(!$key) {
			return self::$config;
		}
		else if(is_null($value)) {
			if(isset(self::$config[$key])) {
				return self::$config[$key];
			}
			else {
				return '';
			}
		}

		self::$config[$key] = $value;
	}

	public static function arguments($argv) {
		for($i = 1; $i < sizeof($argv); $i++) {
			if($argv[$i] == '-nobash' || $argv[$i] == '-nocolors') {
				self::config('isColorsEnabled', false);
			}
			if($argv[$i] == '-nobash') {
				self::config('isBashEnabled', false);
			}
			if($argv[$i] == '-plain') {
				self::config('plain', true);
			}
		}
	}

	/*
	 * OUTPUT
	 */

	public static function begin($argv, $config, $title = false) {
		foreach ($config as $key => $value) {
			self::config($key, $value);
		}

		self::arguments($argv);

		if(self::config('plain')) {
			self::clear();
		}

		if($title) {
			self::title($title);
		}
	}

	public static function end() {
		self::line();
		self::line(self::drawBar());

		echo self::returnChara.self::returnChara;
	}

	public static function clear() {
		passthru('clear');
	}

	public static function getLine($text = '', $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		if(!is_array($text)) {
			$text = array($text);
		}
		$text = implode(self::returnChara, $text);

		return self::styleText(self::returnChara.$text, $color, $style, $background);
	}

	public static function line($text = '', $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		echo self::getLine($text, $color, $style, $background);
	}

	public static function write($text, $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		if(!is_array($text)) {
			$text = array($text);
		}
		$text = implode(' ', $text);

		echo self::styleText(' '.$text, $color, $style, $background);
	}

	/*
	 * TEXT UTILS
	 */

	public static function styleText($text, $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		if(!self::config('isColorsEnabled')) {
			return $text;
		}

		if($style != ConsoleStyles::none) {
			$color = str_replace('[0;', '['.$style.';', $color);
		}

		if($color == ConsoleColors::none & $background != ConsoleBackgrounds::none) {
			$color = $background;
			$background = '';
		}

		return $background.$color.$text.ConsoleColors::none;
	}

	public static function savePosition() {
		if(self::config('isBashEnabled')) {
			echo "\033[s";
		}
	}

	public static function loadPosition() {
		if(self::config('isBashEnabled')) {
			echo "\033[u";
		}
	}

	public static function positionText($text, $y, $x, $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		self::savePosition();
		if(self::config('isBashEnabled')) {
			echo "\033[".$y.";".$x."f";
		}
		echo self::styleText($text, $color, $style, $background);
		self::loadPosition();
	}

	public static function getMaxChars($array) {
		$maxCharas = 0;

		if(!is_array($array)) {
			$array = array($array);
		}
		foreach ($array as $line) {
			if(strlen($line) > $maxCharas) {
				$maxCharas = strlen($line);
			}
		}

		return $maxCharas;
	}

	public static function draw($drawing, $background = ConsoleBackgrounds::white, $position = ConsolePositions::right) {
		$maxCharas = self::config('maxCharas');

		for($i = 0; $i < sizeof($drawing); $i++) {
			$line = $drawing[$i];
			$nbSpaces = $maxCharas - strlen($line);

			if(self::config('isColorsEnabled')) {
				$line = str_replace('0', $background.' '.ConsoleBackgrounds::none, $line);
			}

			self::line(str_repeat(' ', $nbSpaces).$line);
		}

		self::line();
	}

	/*
	 * DEFINED DRAWS
	 */

	public static function drawBar($color = self::defaultBorderColor, $size = false, $chara = '=') {
		$size = $size ? $size : self::config('maxCharas');

		$bar = self::styleText(str_repeat(' ', $size), ConsoleColors::none, ConsoleStyles::none, $color);
		if(!self::config('isColorsEnabled')) {
			$bar = '+'.str_repeat($chara, $size - 2).'+';
		}
		return $bar;
	}

	public static function drawBorder($color = self::defaultBorderColor, $chara = '|') {
		$border = self::styleText(str_repeat(' ', 1), ConsoleColors::none, ConsoleStyles::none, $color);
		if(!self::config('isColorsEnabled')) {
			$border = $chara;
		}
		return $border;
	}

	public static function drawEmptyLineBorder($color = self::defaultBorderColor, $size = false) {
		$size = $size ? $size : self::config('maxCharas');
		$border = self::drawBorder($color);
		$line = $border.str_repeat(' ', $size - 2).$border;

		return $line;
	}

	public static function easterEggLogo() {
		self::draw(array(
			'  0       0  ',
			'   0     0   ',
			'  000000000  ',
			' 00  000  00 ',
			'0000000000000',
			'0 000000000 0',
			'0 0       0 0',
			'  000   000  '
		), ConsoleBackgrounds::yellow, ConsolePositions::right);

		self::line();
	}

	/*
	 * WIDGETS
	 */

	public static function progress($percent, $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none) {
		self::savePosition();

		$maxCharas = self::config('maxCharas') - (2 + 2 + 6);

		$barSize = round($percent * $maxCharas / 100);
		$barBorder = self::drawBorder();
		$bar = '';
		if(self::config('isBashEnabled')) {
			if(self::config('isColorsEnabled')) {
				$bar = '  '.$barBorder.ConsoleBackgrounds::cyan.str_repeat(' ', $barSize).ConsoleBackgrounds::none.str_repeat(' ', $maxCharas - $barSize).$barBorder;
			}
			else {
				$bar = '  ['.str_repeat('=', $barSize).str_repeat(' ', $maxCharas - $barSize).']';
			}
		}

		$percent .= '%';
		$percent = str_repeat(' ', 4 - strlen($percent)).$percent;

		self::line($bar.'  '.$percent, $color, $style, $background);

		if($percent < 100) {
			self::loadPosition();
		}
	}

	public static function title($title) {
		$maxCharas = self::config('maxCharas');
		$bar = self::drawBar();
		$border = self::drawBorder();
		$lineBreak = self::drawEmptyLineBorder(self::defaultBorderColor);

		self::line($bar);
		self::line($lineBreak);

		$lines = array();
		if(!is_array($title)) {
			$title = array($title);
		}
		for($i = 0; $i < sizeof($title); $i++) {
			$line = $title[$i];
			$nbSpaces = ($maxCharas - strlen($line)) - 4;

			if($i == 0) {
				$line = self::styleText($line, ConsoleColors::red, ConsoleStyles::bold);
			}
			else {
				$line = self::styleText($line, ConsoleColors::white);
			}

			$lines []= $border.' ' .$line.str_repeat(' ', $nbSpaces).' '.$border;
		}
		self::line($lines);

		self::line($lineBreak);
		self::line($bar);
		self::line();
	}

	public static function badge($lines, $position = ConsolePositions::right, $color = ConsoleColors::none, $style = ConsoleStyles::none, $background = ConsoleBackgrounds::none, $borderColor = self::defaultBorderColor) {
		self::savePosition();

		$maxCharas = self::config('maxCharas');

		if(!is_array($lines)) {
			$lines = array($lines);
		}
		$lineMaxCharas = self::getMaxChars($lines) + 4;

		$bar = self::drawBar($borderColor, $lineMaxCharas + 2);
		$border = self::drawBorder($borderColor);
		$lineBreak = self::drawEmptyLineBorder($borderColor, $lineMaxCharas + 2);

		$x = $maxCharas - ($lineMaxCharas + 2);
		if($position == ConsolePositions::left) {
			$x = 1;
		}

		$badgeLines = array(
			$bar,
			$lineBreak
		);
		for($i = 0; $i < sizeof($lines); $i++) {
			$spaces = str_repeat(' ', ($lineMaxCharas - 4) - strlen($lines[$i]));

			$badgeLines []= $border.'  '.self::styleText($lines[$i], $color, $style, $background).$spaces.'  '.$border;
		}
		$badgeLines []= $lineBreak;
		$badgeLines []= $bar;

		if(self::config('isBashEnabled')) {

			for($i = sizeof($badgeLines) - 1; $i >= 0; $i--) {

				$y = 1;
				for($idLine = $i; $idLine < sizeof($badgeLines); $idLine++) {
					self::positionText($badgeLines[$idLine], $y, $x);
					$y++;
				}

				time_nanosleep(0, 80000000);
			}

			self::loadPosition();
		}
		else {
			for($i = 0; $i < sizeof($badgeLines); $i++) {
				self::line(str_repeat(' ', $x).$badgeLines[$i]);
			}
			self::line();
		}
	}

	public static function badgeSuccess($text) {
		self::badge($text, ConsolePositions::right, ConsoleColors::green, ConsoleStyles::bold, ConsoleBackgrounds::none, ConsoleBackgrounds::green);
	}

	public static function badgeError($text) {
		self::badge($text, ConsolePositions::right, ConsoleColors::red, ConsoleStyles::bold, ConsoleBackgrounds::none, ConsoleBackgrounds::red);
	}

	public static function badgeWarning($text) {
		self::badge($text, ConsolePositions::right, ConsoleColors::yellow, ConsoleStyles::bold, ConsoleBackgrounds::none, ConsoleBackgrounds::yellow);
	}

	public static function table($array, $titles = true) {
		$maxCharas = self::config('maxCharas');

		$columnsSizes = array();
		foreach ($array as $values) {
			if(is_array($values)) {
				for ($i = 0; $i < sizeof($values); $i++) {
					if(!isset($columnsSizes[$i])) {
						$columnsSizes[$i] = 0;
					}
					if($columnsSizes[$i] < strlen($values[$i])) {
						$columnsSizes[$i] = strlen($values[$i]);
					}
				}
			}
		}
		$maxWidth = 1;
		foreach($columnsSizes as $size) {
			$maxWidth += $size + 3;
		}

		$defaultBar = '  '.self::drawBar(self::defaultBorderColor, $maxWidth);
		$defaultBorder = self::drawBorder();
		$spacerBar = '  '.self::drawBar(self::defaultBorderColor, $maxWidth, '-');
		$titleBar = '  '.self::drawBar(ConsoleBackgrounds::red, $maxWidth);
		$titleBorder = self::drawBorder(ConsoleBackgrounds::red);

		$lineIndex = 0;
		foreach($array as $values) {
			if(!is_array($values)) {
				self::line($spacerBar);
			}
			else {
				$bar = $defaultBar;
				$border = $defaultBorder;
				if($titles && $lineIndex == 0) {
					$bar = $titleBar;
					$border = $titleBorder;
				}
				if($lineIndex == 0) {
					self::line($bar);
				}

				$line = '';
				for($i = 0; $i < sizeof($values); $i++) {
					if($i == 0) {
						$line .= '  '.$border;
					}
					$columnSize = $columnsSizes[$i];
					$nbSpaces = $columnsSizes[$i] - strlen($values[$i]);
					$label = $values[$i].($nbSpaces > 0 ? str_repeat(' ', $nbSpaces) : '');
					$line .= ' '.$label.' '.$border;
				}

				self::line($line);

				if($titles && $lineIndex == 0) {
					self::line($bar);
				}
			}
			$lineIndex++;
		}
		self::line($bar);
	}

	public static function graph($config) {
		if(!is_array($config) || !isset($config['data'])) {
			return;
		}

		$colors = array(
			array(ConsoleColors::green, ConsoleBackgrounds::green),
			array(ConsoleColors::red, ConsoleBackgrounds::red),
			array(ConsoleColors::yellow, ConsoleBackgrounds::yellow),
			array(ConsoleColors::blue, ConsoleBackgrounds::blue),
			array(ConsoleColors::purple, ConsoleBackgrounds::purple),
			array(ConsoleColors::cyan, ConsoleBackgrounds::cyan),
			array(ConsoleColors::black, ConsoleBackgrounds::black),
			array(ConsoleColors::white, ConsoleBackgrounds::white)
		);

		if(isset($config['colors'])) {
			for($i = 0; $i < sizeof($config['colors']); $i++) {
				$colors[$i] = $config['colors'][$i];
			}
		}

		$yLabels = isset($config['yLabels']) ? $config['yLabels'] : '{{ value }}';

		self::line();

		$label = isset($config['label']) ? $config['label'] : '';
		if($label != '') {
			self::line('  '.$config['label'], ConsoleColors::cyan, ConsoleStyles::bold);
			self::line();
		}

		$legend = '';
		$colorIndex = 0;
		$minValue = 0;
		$maxValue = 0;
		$nbEntities = sizeof($config['data']);
		$entityWidth = floor(10 / $nbEntities);
		$entitySpacer = 10 - ($entityWidth * $nbEntities);
		$border = self::drawBorder(ConsoleBackgrounds::white);
		$nbValues = 0;

		foreach($config['data'] as $entityName => $values) {
			$legend .= '       '.self::drawBorder($colors[$colorIndex][1]).' '.Console::styleText($entityName, $colors[$colorIndex][0], ConsoleStyles::bold);

			$colorIndex++;
			$colorIndex = $colorIndex == count($colors) ? 0 : $colorIndex;

			$nbValues = max($nbValues, sizeof($values));

			for($i = 0; $i < sizeof($values); $i++) {
				$minValue = min($minValue, $values[$i]);
				$maxValue = max($maxValue, $values[$i]);
			}
		}

		$maxValue = isset($config['yMax']) ? $config['yMax'] : $maxValue;
		$minValue = isset($config['yMin']) ? $config['yMin'] : $minValue;

		$yLabelsWidth = strlen(str_replace('{{ value }}', $maxValue, $yLabels));

		$maxWidth = $yLabelsWidth + 2 + 2 + ($nbValues * 12);

		$levelsValues = array();
		for($i = 0; $i <= 10; $i++) {
			$levelsValues []= round((($maxValue - $minValue) / 10) * $i);
		}

		$lines = array();
		for($i = 0; $i <= 10; $i++) {
			$testValue = $levelsValues[$i];
			$label = $i % 2 ? '' : $testValue;
			$label = $label !== '' ? str_replace('{{ value }}', $label, $yLabels) : '';
			$spaces = $yLabelsWidth - strlen($label) > 0 ? str_repeat(' ', $yLabelsWidth - strlen($label)) : '';
			$leftPart = '  '.$spaces.$label.' '.$border;

			if($i == 0) {
				$lines [] = $leftPart.self::drawBar(ConsoleBackgrounds::white, $maxWidth - ($yLabelsWidth + 3));
			}
			else {
				$columns = '';

				for($j = 0; $j < $nbValues; $j++) {
					$colorIndex = 0;
					$gaugesLine = '';
					foreach($config['data'] as $entityName => $values) {

						$value = $j < sizeof($values) ? $values[$j] : 0;

						if($value > 0 && $testValue <= $value) {
							$gaugesLine .= self::drawBar($colors[$colorIndex][1], $entityWidth);
						}
						else {
							$gaugesLine .= str_repeat(' ', $entityWidth);
						}

						$colorIndex++;
						$colorIndex = $colorIndex == count($colors) ? 0 : $colorIndex;
					}

					$columns .= '  '.$gaugesLine.str_repeat(' ', $entitySpacer);
				}

				$lines []= $leftPart.$columns;
			}
		}
		$lines = array_reverse($lines);
		self::line($lines);

		if(isset($config['xLabels']) && is_array($config['xLabels'])) {
			$line = str_repeat(' ', $yLabelsWidth + 4);

			foreach($config['xLabels'] as $label) {
				$labelLen = strlen($label);
				if($labelLen > 10) {
					$label = substr($label, 0, 10);
				}
				else if($labelLen < 10) {
					$label .= str_repeat(' ', 10 - $labelLen);
				}

				$line .= '  '.$label;
			}

			self::line($line);
		}

		self::line();

		self::line($legend);

		self::line();
	}

	/*
	 * JOBS
	 */

	private static $jobsSuccess;
	private static $actualJobs;

	private static function writeJobs($animType = '', $id = '', $step = 0) {

		self::loadPosition();

		$maxCharas = self::config('maxCharas');
		$rewrite = false;
		$hasUserAction = false;

		foreach (self::$actualJobs as $jobId => $job) {
			$spaces = str_repeat(' ', $maxCharas - (strlen($job['label']) + 2));
			$line = '  '.$job['label'].$spaces;

			if($jobId == $id && $animType == 'success' && $step <= 4) {
				$line = substr($line, 0, $maxCharas - 4);

				if(self::config('isColorsEnabled')) {

					$line .= str_repeat(' ', 4 - $step).ConsoleBackgrounds::green.str_repeat(' ', $step).ConsoleBackgrounds::none;

					$rewrite = true;
				}
				else {
					$line .= '[OK]';
				}
			}
			else if($jobId == $id && ($animType == 'fail' || $animType == 'warning') && $step <= 4) {
				$line = substr($line, 0, $maxCharas - 4);

				if(self::config('isColorsEnabled')) {

					$color = ConsoleBackgrounds::red;
					if($job['errorType'] == 'warning') {
						$color = ConsoleBackgrounds::yellow;
					}

					$line .= str_repeat(' ', 4 - $step).$color.str_repeat(' ', $step).ConsoleBackgrounds::none;

					$rewrite = true;
				}
				else {
					if($job['errorType'] == 'warning') {
						$line .= ' [W]';
					}
					else {
						$line .= '[KO]';
					}
				}
			}
			else if($job['userAction']) {
				$actionTextLen = strlen($job['userAction']) + 1;
				$actionText = ConsoleColors::red.$job['userAction'].ConsoleColors::none;

				$line = '  '.$job['label'];
				$lineLen = strlen($line) + $actionTextLen;
				if($lineLen > $maxCharas) {
					$line = substr('  '.$job['label'], 0, $maxCharas - $actionTextLen);
				}
				else if($lineLen < $maxCharas) {
					$line .= str_repeat(' ', $maxCharas - $lineLen);
				}
				$line .= ' '.$actionText;

				self::$actualJobs[$jobId]['userAction'] = false;
				$hasUserAction = true;
			}
			else if($job['activated'] && $job['percent'] > 0) {

				if($job['percent'] == 100) {
					if($job['success']) {
						if(self::config('isColorsEnabled')) {
							$percent = ConsoleBackgrounds::green.'    '.ConsoleBackgrounds::none;
							$line = self::styleText(substr($line, 0, $maxCharas - 4), ConsoleColors::green).$percent;
						}
						else {
							$line = substr($line, 0, $maxCharas - 4).'[OK]';
						}
					}
					else {
						if(self::config('isColorsEnabled')) {
							$color = ConsoleColors::red;
							$backgroundColor = ConsoleBackgrounds::red;
							$symbol = 'X';
							if($job['errorType'] == 'warning') {
								$color = ConsoleColors::yellow;
								$backgroundColor = ConsoleBackgrounds::yellow;
								$symbol = 'V';
							}

							$percent = $backgroundColor.'    '.ConsoleBackgrounds::none;
							$line = self::styleText(substr($line, 0, $maxCharas - 4), $color).$percent;
						}
						else {
							if($job['errorType'] == 'warning') {
								$line = substr($line, 0, $maxCharas - 4).' [W]';
							}
							else {
								$line = substr($line, 0, $maxCharas - 4).'[KO]';
							}
						}
					}
				}
				else {
					$percent = $job['percent'].'%';
					$line = substr($line, 0, $maxCharas - strlen($percent)).$percent;
				}
			}
			else if(!$job['activated'] && self::config('isColorsEnabled')) {
				$line = self::styleText($line, ConsoleColors::black, ConsoleStyles::bold);
			}

			self::line($line);
			if(self::config('isBashEnabled')) {
				self::line();
			}
		}

		if(!self::config('isBashEnabled')) {
			self::line();
		}

		if($hasUserAction) {
			readline('');
		}

		if($rewrite) {
			$step++;
			time_nanosleep(0, 50000000);
			self::writeJobs($animType, $id, $step);
		}
	}

	public static function initJobs($jobs) {
		self::line();
		self::savePosition();

		self::$actualJobs = array();
		foreach ($jobs as $jobId => $jobLabel) {
			self::$actualJobs[$jobId] = array(
				'label' => $jobLabel,
				'percent' => 0,
				'activated' => false,
				'success' => false,
				'userAction' => false,
				'errorType' => ''
			);
		}

		self::$jobsSuccess = array(
			'errors' => 0,
			'warnings' => 0
		);

		self::writeJobs();
	}

	public static function setJob($id, $values) {
		foreach (self::$actualJobs as $jobId => $job) {
			if($jobId == $id) {
				foreach($values as $valueKey => $valueValue) {
					self::$actualJobs[$jobId][$valueKey] = $valueValue;
				}

				break;
			}
		}
	}

	public static function jobProgress($id, $percent) {
		self::setJob($id, array(
			'percent' => $percent,
			'activated' => true
		));

		self::writeJobs();
	}

	public static function jobUserAction($id, $actionText) {
		self::setJob($id, array(
			'userAction' => $actionText
		));

		self::writeJobs();
	}

	public static function jobSuccess($id) {
		self::setJob($id, array(
			'percent' => 100,
			'success' => true
		));

		self::writeJobs('success', $id);
	}

	public static function jobFail($id) {
		self::setJob($id, array(
			'percent' => 100,
			'success' => false
		));

		self::$jobsSuccess['errors']++;

		self::writeJobs('fail', $id);
	}

	public static function jobWarning($id) {
		self::setJob($id, array(
			'percent' => 100,
			'success' => false,
			'errorType' => 'warning'
		));

		self::$jobsSuccess['warnings']++;

		self::writeJobs('warning', $id);
	}

	public static function jobsBadge() {
		$success = self::$jobsSuccess;

		if($success['errors'] == 0 && $success['warnings'] == 0) {
			self::badgeSuccess('WORK DONE !');
		}
		else if($success['warnings'] == 0) {
			self::badgeError('WORK DONE WITH '.$success['errors'].' ERROR'.($success['errors'] > 1 ? 'S' : '').' !');
		}
		else if($success['errors'] == 0) {
			self::badgeWarning('WORK DONE WITH '.$success['warnings'].' WARNING'.($success['warnings'] > 1 ? 'S' : ''));
		}
		else {
			self::badgeError(array(
				'WORK DONE',
				'WITH '.$success['errors'].' ERROR'.($success['errors'] > 1 ? 'S' : '').' !',
				'WITH '.$success['warnings'].' WARNING'.($success['warnings'] > 1 ? 'S' : '')
			));
		}
	}

	// alias
	public static function beginJobs($argv, $title, $jobs) {
		self::begin($argv, array(
			'plain' => true
		), $title);
		self::initJobs($jobs);
	}

}