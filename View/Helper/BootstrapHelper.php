<?php

require_once(dirname(__FILE__) . '/../../Lib/BootstrapInfo.php');

class BootstrapHelper extends AppHelper {

	public $helpers = array("Html", "Session");

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->BootstrapInfo = new BootstrapInfo();
	}

	/**
	 * Displays an h1 tag wrapped in a div with the page-header class
	 *
	 * @param string $title
	 * @return string
	 */
	public function pageHeader($title){
		return $this->Html->tag(
			"div",
			"<h1>$title</h1>",
			array("class" => "page-header")
		);
	}

	/**
	 * Creates a Bootstrap label with $messsage and optionally the $type. Any
	 * options that could get passed to HtmlHelper::tag can be passed in the
	 * third param.
	 *
	 * @param string $message
	 * @param string $type
	 * @param array $options
	 * @access public
	 * @return string
	 */
	public function label($message = "", $style = "", $options = array()) {
		$klass = isset($options['class']) ? $options['class'] : '';
		$options['class'] = $this->BootstrapInfo->stylesFor('label', $style, $klass);
		return $this->Html->tag("span", $message, $options);
	}

	/**
	 * Creates a Bootstrap badge with $num and optional $style. Any options
	 * that could get passed to the HtmlHelper::tag can be passed in the 3rd
	 * param
	 *
	 * @param  integer $num
	 * @param  string  $style
	 * @param  array   $options
	 * @return string
	 */
	public function badge($num = 0, $style = "", $options = array()) {
		$klass = isset($options['class']) ? $options['class'] : '';
		$options['class'] = $this->BootstrapInfo->stylesFor('badge', $style, $klass);
		return $this->Html->tag("span", $num, $options);
	}

	/**
	 * progress
	 *
	 * @param  string $style
	 * @param  array  $options
	 * @return string
	 */
	public function progress($options = array()) {
		$width = 0;
		$options = $this->BootstrapInfo->progress($options);
		if (
			isset($options["width"]) &&
			!empty($options["width"]) &&
			is_int($options["width"])
		) {
			$width = $options["width"];
			unset($options['width']);
		}
		$bar = $this->Html->tag(
			"div",
			"",
			array("class" => "bar", "style" => "width: {$width}%;")
		);
		return $this->Html->tag("div", $bar, $options);
	}

	/**
	 * Takes the name of an icon and returns the i tag with the appropriately
	 * named class. The second param will switch between black and white
	 * icon sets.
	 *
	 * @param mixed $name
	 * @access public
	 * @return void
	 */
	public function icon($name, $color = null) {
		if ($color === "white") {
			$color = "icon-{$color}";
		}
		$klass = $this->BootstrapInfo->stylesFor('icon', $name, $color);
		return $this->Html->tag("i", false, array("class" => $klass));
	}

	/**
	 * Renders alert markup and takes a style and closable option
	 *
	 * @param mixed $content
	 * @param array $options
	 * @access public
	 * @return void
	 */
	public function alert($content, $options = array()) {
		$close = "";
		if (isset($options['closable']) && $options['closable']) {
			$close = '<a class="close" data-dismiss="alert">&times;</a>';
		}
		$style = isset($options["style"]) ? $options["style"] : 'warning';
		$klass = isset($options["class"]) ? $options["class"] : '';
		$klass = $this->BootstrapInfo->stylesFor('alert', $style, $klass);
		return $this->Html->tag(
			'div',
			"{$close}{$content}",
			array("class" => $klass)
		);
	}

	/**
	 * Captures the Session flash if it is set and renders it in the proper
	 * markup for the twitter bootstrap styles. The default key of "flash",
	 * gets translated to the warning styles. Other valid $keys are "info",
	 * "success", "error". The $key "auth" with use the error styles because
	 * that is when the auth form fails.
	 *
	 * @param string $key
	 * @param $options
	 * @access public
	 * @return string
	 */
	public function flash($key = "flash", $options = array()) {
		$content = $this->_flash_content($key);
		if (empty($content)) { return ''; }
		$close = false;
		if (isset($options['closable']) && $options['closable']) {
			$close = true;
		}
		return $this->alert($content, array("style" => $key, "closable" => $close));
	}

	/**
	 * By default it checks $this->flash() for 5 different keys of valid
	 * flash types and returns the string.
	 *
	 * @param array $options
	 * @access public
	 * @return string
	 */
	public function flashes($options = array()) {
		if (!isset($options["keys"]) || !$options["keys"]) {
			$options["keys"] = array("info", "success", "danger", "warning", "flash");
		}
		if (isset($options["auth"]) && $options["auth"]) {
			$options["keys"][] = "auth";
			unset($options["auth"]);
		}
		$keys = $options["keys"];
		unset($options["keys"]);
		$out = '';
		foreach($keys as $key) {
			$out .= $this->flash($key, $options);
		}
		return $out;
	}

	/**
	 * Returns the content from SessionHelper::flash() for the passed in
	 * $key.
	 *
	 * @param string $key
	 * @access public
	 * @return void
	 */
	public function _flash_content($key = "flash") {
		return $this->Session->flash($key, array("element" => null));
	}

	/**
	 * Displays the alert-message.block-messgae div's from the twitter
	 * bootstrap.
	 *
	 * @param string $message
	 * @param array $links
	 * @param array $options
	 * @access public
	 * @return string
	 */
	public function block($message = null, $options = array()) {
		$style = "";
		$valid = array("success", "info", "error");
		if (isset($options["style"]) && in_array($options["style"], $valid)) {
			$style = " alert-{$options["style"]}";
		}
		$class = "alert alert-block{$style}";
		$close = $heading = "";
		if (isset($options["closable"]) && $options["closable"]) {
			$close = '<a class="close" data-dismiss="alert">&times;</a>';
		}
		if (isset($options["heading"]) && !empty($options["heading"])) {
			$heading = $this->Html->tag(
				"h4",
				$options["heading"],
				array("class" => "alert-heading")
			);
		}
		return $this->Html->tag(
			"div",
			$close.$heading.$message,
			array("class" => $class)
		);
	}

}
