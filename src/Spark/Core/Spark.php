<?php
/**
 * Spark is a high-performance templating engine designed to work
 * with common WYSIWYG editors.
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace Spark\Core;

/**
 * The core of Spark
 */
class Spark
{
	/** The namespace for Spark to use (prefixes all tags) */
	private $_namespace;
	/** A list of all elements we parse */
	private $_registered_elements = array();
	/** A list of all current tokens */
	private $_tokens = array();
	/** A list of all linked tokens (e.g. embedded snippets) */
	private $_embedded_tokens = array();

	/**
	 * Initialise Spark
	 * 
	 * @param string $namespace The namespace for Spark to use (prefixes all tags)
	 */
	public function __construct($namespace = "Spark") {
		$this->_namespace = $namespace;

		$this->addTag("Version", function($html, $inner) {
			return "<p>Spark Version 0.2_dev</p>";
		});
	}

	/**
	 * Register a callback related to a tag.
	 * Everytime spark encountrers the tag it will call the callback function with the element's markup
	 * 
	 * @param string $tag      The Tag to detect
	 * @param mixed  $callback The Callback to call when the $tag is detected
	 */
	public function addTag($tag, $callback) {
		$this->_registered_elements[$tag] = $callback;
	}

	/**
	 * Render a page
	 * 
	 * @param string $html The HTML to render
	 */
	public function render($html) {
		// Reset tokens
		$this->_tokens = array();

		// Breakup the tags
		$lines = $this->breakup($html);

		// Tokenise all namespaced tags
		$html = $this->tokenise($lines);

		// Replace tags with data
		$html = $this->replace($html);

		// Post-process and output
		print $this->postProcess($html);
	}

	/**
	 * Breakup a page, ensures all tags are on a separate line.
	 * Then return an array of lines
	 * 
	 * @param string $html The HTML to break up
	 */
	private function breakup($html) {
		$html = str_replace("\n", "", $html);
		$html = str_replace(array("<", ">"), array("\n<", ">\n"), $html);
		return explode("\n", $html);
	}

	/**
	 * Tokenise a page
	 * 
	 * @param string $lines The HTML lines to tokenise
	 */
	private function tokenise($lines) {
		$html = "";

		$token = 0;
		$stack = array();
		foreach ($lines as $line) {
			// Do we have a valid tag?
			if (stripos($line, "<" . $this->_namespace) !== false) {
				// Register the token
				$this->_tokens[$token] = array($line);

				// Link the token to the previous item on the stack
				if (count($stack) > 0) {
					// List it as embedded, the parent is responsible for the output
					$ptr = end($stack);
					$this->_tokens[$ptr][] = "[[SRM]]";
					$this->_embedded_tokens[$token] = array($ptr, count($this->_tokens[$ptr]) - 1);
				} else {
					// Tokenise it
					$html .= "<SPARKTOKEN" . $token . ">\n";
				}

				// Add it to the stack
				$stack[] = $token;

				$token++;
			} elseif (stripos($line, "</" . $this->_namespace) !== false) {
				// Pop off the stack
				$t = array_pop($stack);
				$this->_tokens[$t][] =  $line;
			} elseif (!empty($stack)) {
				// Add the line to the stack
				$t = array_pop($stack);
				$this->_tokens[$t][] =  $line;
				$stack[] = $t;
			} else {
				$html .= $line . "\n";
			}
		}

		return $html;
	}

	/**
	 * Replace all tokens with real data
	 * 
	 * @param string $html The HTML to parse
	 */
	private function replace($html) {
		$tlen = strlen("<" . $this->_namespace);

		for ($token = count($this->_tokens) - 1; $token >= 0; $token--) {
			$data = $this->_tokens[$token];

			$tag = $data[0];
			$tag = substr($tag, $tlen);
			$tag = substr($tag, 0, -1);

			if (isset($this->_registered_elements[$tag])) {
				$func = $this->_registered_elements[$tag];

				// Grab markups
				$snippet_markup = trim(implode("\n", $data));
				$inner_markup   = trim(implode("\n", array_slice($data, 1, -1)));

				$markup = $func($snippet_markup, $inner_markup);
				$html = str_replace("<SPARKTOKEN" . $token . ">", $markup, $html);

				// Also handle links
				if (isset($this->_embedded_tokens[$token])) {
					$ptrs = $this->_embedded_tokens[$token];
					$this->_tokens[$ptrs[0]][$ptrs[1]] = $markup;
				}
			}
		}

		return $html;
	}

	/**
	 * Post-processor to cleanup a page before output
	 */
	protected function postProcess($html) {
		return trim($html);
	}
}
