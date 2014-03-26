<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: GoogleClosure
 * Basic PHP wrapper for Google Closure Compiler
 */

namespace frontend\modules\storage\components;

use common\helpers\DateTime;
use common\helpers\ES;
use common\helpers\Html;
use yii\base\Exception;

class GoogleClosureException extends Exception
{
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code, null);
	}
}

class GoogleClosure
{
	//basic params
	const PARAM_INPUT = 'js';
	const PARAM_OUTPUT = 'js_output_file';
	const PARAM_CHARSET = 'charset';
	const PARAM_DEBUG = 'debug';
	const PARAM_COMPILATION_LEVEL = 'compilation_level';
	const PARAM_INPUT_LANGUAGE_SPECIFICATION = 'language_in';
	const PARAM_VALIDATE_SOURCE = 'third_party';
	const PARAM_VERSION = 'version';

	// compilation levels
	const COMPILATION_LEVEL_WHITESPACE_ONLY = 'WHITESPACE_ONLY';
	const COMPILATION_LEVEL_SIMPLE_OPTIMIZATION = 'SIMPLE_OPTIMIZATIONS';
	const COMPILATION_LEVEL_ADVANCED_OPTIMIZATION = 'ADVANCED_OPTIMIZATIONS';

	//javascript specifications
	const SPEC_DEFAULT_ECMASCRIPT3 = 'ECMASCRIPT3';
	const SPEC_ECMASCRIPT5 = 'ECMASCRIPT5';
	const SPEC_ECMASCRIPT5_STRICT = 'ECMASCRIPT5_STRICT';

	/**
	 * Path to compiler jar
	 * @var string
	 */
	private $executable = '/usr/share/java/bin/java -jar /var/apps/compiler.jar';

	/**
	 * Temporary path where will stored temporary files
	 * @var string
	 */
	private $temporaryPathName = '/dev/shm/';

	/**
	 * Temporary unique file name
	 * @var string
	 */
	private $temporaryFileName;

	/**
	 * Temporary complete file path
	 * @var string
	 */
	private $temporaryFilePath;

	/**
	 * Source code
	 * @var string
	 */
	private $sourceData;

	/**
	 * Result code
	 * @var string
	 */
	private $outputData;

	/**
	 * @var int
	 */
	private $sourceSize = 0;

	/**
	 * @var int
	 */
	private $outputSize = 0;

	/**
	 * Whether TRUE, in debug info will showed executing time
	 * @var bool
	 */
	private $enableProfiling = false;

	/**
	 * Will be TRUE after executing script
	 * @var bool
	 */
	private $executed = false;

	/**
	 * Build command
	 * @var array
	 */
	private $command = array();

	/**
	 * STDOUT data
	 * @var array
	 */
	private $runtimeOutput = array();

	/**
	 * Custom information
	 * @var array
	 */
	private $debugInformation = array(
		'warning'=>false,
		'error'=>false,
	);

	/**
	 * @param string $source Source code
	 */
	public function __construct($source) {
		$this->sourceData = $source;
		$this->temporaryFileName = uniqid("minified");
		$this->writeFile($this->temporaryPathName . $this->temporaryFileName, $this->sourceData, true);
		$this->setInput();

		return $this;
	}

	/**
	 * @param string $filePath
	 * @param string $data
	 * @param bool $temporary
	 * @throws GoogleClosureException
	 */
	private function writeFile($filePath, $data, $temporary = false) {
		$handler = fopen($filePath, 'w');

		if ( !$handler ) {
			throw new GoogleClosureException(
				"Error while creating temporary file. Permission denied or not valid set php open_basedir to temporary path {$this->temporaryPathName}. Trying save path: $filePath");
		}

		if ( fwrite($handler, $data) === false ) {
			throw new GoogleClosureException("Error while saving data to temporary file. Unexpected error");
		}

		fclose($handler);

		if ( $temporary ) {
			$this->temporaryFilePath = $filePath;
		}
	}

	private function setInput() {
		$this->setParam(self::PARAM_INPUT, $this->temporaryFilePath);
	}

	/**
	 * @param string $paramName
	 * @param null|string $value
	 * @return GoogleClosure
	 */
	private function setParam($paramName, $value = null) {

		if ( $value === null ) {
			$this->command[] = '--' . $paramName;
		} else {
			$this->command[] = '--' . $paramName . '=' . $value;
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @param string $message
	 */
	private function addDebugInfo($name, $message) {
		$this->debugInformation[$name] = $message;
	}

	/**
	 * @param string $charset
	 * @return GoogleClosure
	 */
	public function setCharset($charset = 'UTF8') {
		$this->setParam(self::PARAM_CHARSET, $charset);

		return $this;
	}

	/**
	 * @param bool $enable
	 * @return GoogleClosure
	 */
	public function enableDebug($enable = false) {
		if ( $enable ) {
			$this->setParam(self::PARAM_DEBUG);
		}

		return $this;
	}

	/**
	 * @param string $level
	 * @return GoogleClosure
	 */
	public function setCompilationLevel($level = self::COMPILATION_LEVEL_SIMPLE_OPTIMIZATION) {
		$this->setParam(self::PARAM_COMPILATION_LEVEL, $level);

		return $this;
	}

	/**
	 * @param string $specification
	 * @return GoogleClosure
	 */
	public function setLanguageSpecification($specification = self::SPEC_DEFAULT_ECMASCRIPT3) {
		$this->setParam(self::PARAM_INPUT_LANGUAGE_SPECIFICATION, $specification);

		return $this;
	}

	/**
	 * @param bool $validate
	 * @return GoogleClosure
	 */
	public function enableValidatingSource($validate = false) {
		if ( $validate ) {
			$this->setParam(self::PARAM_VALIDATE_SOURCE);
		}

		return $this;
	}

	/**
	 * @param $outputFilePath
	 * @return GoogleClosure
	 */
	public function setOutput($outputFilePath) {
		$this->setParam(self::PARAM_OUTPUT, $outputFilePath);

		return $this;
	}

	/**
	 * @param bool $enable
	 * @return GoogleClosure
	 */
	public function enableProfiling($enable = false) {
		$this->enableProfiling = $enable;

		return $this;
	}

	public function getCopyrights() {
		$date = DateTime::getDateTime();
		return <<<COMMENT
/*
 * Compiled by MINIFIED.pw with Google Closure Compiler
 * Date: $date
 */

COMMENT;
	}

	public function getResult() {
		return $this->outputData;
	}

	public function getRuntimeResult() {
		return Html::encode(implode('', $this->runtimeOutput));
	}

	public function hasWarnings() {
		if($this->debugInformation['warning'] !== false)
			return true;

		return false;
	}

	public function hasErrors() {
		if($this->debugInformation['error'] !== false)
			return true;

		return false;
	}

	public function getWarnings() {
		return $this->debugInformation['warning'];
	}

	public function getErrors() {
		return $this->debugInformation['error'];
	}

	private function detectErrors() {
		$pattern = "/(\d+) error\(s\), (\d+) warning\(s\)/s";
		$matches = array();
		$runtime = implode("\n", $this->getRuntimeOutput());
		$runtime = str_replace($this->temporaryFilePath.':1: ','', $runtime);

		if(preg_match($pattern, $runtime, $matches)) {

			$runtime = str_replace($matches[1].' error(s), '.$matches[2].' warning(s)','', $runtime);

			if($matches[1] != 0) {
				$this->addDebugInfo('error', $runtime);
			}

			if($matches[2] != 0) {
				$this->addDebugInfo('warning', $runtime);
			}
		}


		return false;
	}

	public function getSourceSize() {
		return $this->sourceSize;
	}

	public function getOutputSize() {
		return $this->outputSize;
	}

	/**
	 * Calculate the difference in size between old data and new data
	 * @param bool $accurate
	 * @param bool $asString
	 * @return double|string
	 * @throws GoogleClosureException
	 */
	public function getDifference($accurate = false, $asString = true) {
		if(!$this->executed)
			throw new GoogleClosureException('You can\'t get difference information before executing');

		$sourceLength = mb_strlen($this->sourceData);
		$outputLength = mb_strlen($this->outputData);

		$this->sourceSize = $sourceLength;
		$this->outputSize = $outputLength;

		$difference = 0.00;

		if($sourceLength === 0 || $outputLength === 0)
			return $difference;

		if ( $sourceLength > $outputLength ) {
			$difference = ($outputLength / $sourceLength) * 100;
		} else if ( $outputLength > $sourceLength ) {
			$difference = ($sourceLength / $outputLength) * 100;
		}

		$difference = (100 - $difference);

		if(!$accurate && $asString) {
			return $difference.'%';
		} else if(!$accurate && !$asString) {
			return $difference;
		} else if($accurate && $asString) {
			return number_format((double) $difference, 2) .'%';
		} else {
			return number_format((double) $difference, 2);
		}
	}

	/**
	 * Gets version of closure compiler
	 * @return string
	 */
	public function getVersion() {
		$this->setParam(self::PARAM_VERSION);
		$command = $this->executable . ' ' . $this->getCommand();

		return exec($command);
	}

	/**
	 * @return string
	 */
	public function getCommand() {
		return $this->executable . ' ' . implode(' ', $this->command) . ' 2>&1';
	}

	/**
	 * @return array Runtime information from stdout
	 */
	public function getRuntimeOutput() {
		return $this->runtimeOutput;
	}

	/**
	 * Custom information
	 * @return array
	 */
	public function getDebugInfo() {
		return $this->debugInformation;
	}

	/**
	 * @param bool $deleteTemporaryFile
	 */
	public function execute($deleteTemporaryFile = true) {
		$this->executed = true;

		$command = $this->getCommand();
		$this->addDebugInfo('executed_command', $command);

		//start profiling
		if($this->enableProfiling){
			$profiling = microtime(true);
		}
			//executing
			$this->outputData = exec($command, $this->runtimeOutput);

		//finish profiling
		if($this->enableProfiling) {
			$this->addDebugInfo('profiling',microtime(true) - $profiling);
		}

		if ( $deleteTemporaryFile ) {
			$this->deleteTemporaryFile();
		}

		$this->detectErrors();

	}

	/**
	 * @return bool
	 * @throws GoogleClosureException
	 */
	public function deleteTemporaryFile() {
		if ( !$this->executed ) {
			throw new GoogleClosureException("Temporary file cannot be deleted before executing handler");
		}

		if ( file_exists($this->temporaryFilePath) && is_file($this->temporaryFilePath) ) {
			return unlink($this->temporaryFilePath);
		}

		return false;
	}

} 