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
	/** The namespace callback for Spark to use (called for any tag within a namespace) */
	private $_namespace_callback;
	/** A list of all elements we parse */
	private $_registered_elements = array();
	/** A list of all current tokens */
	private $_tokens = array();
	/** A list of all linked tokens (e.g. embedded snippets) */
	private $_embedded_tokens = array();
	/** The last output we processed */
	private $_output;
	/** The last set of errors we encountered */
	private $_errors;

	/**
	 * Initialise Spark
	 * 
	 * @param string $namespace The namespace for Spark to use (prefixes all tags)
	 * @param method $callback  A method to be called for any tags within the namespace (optional, negates Spark::addTag())
	 */
	public function __construct($namespace = "Spark", $callback = null) {
		$this->_namespace = $namespace;
		$this->_namespace_callback = $callback;
		$this->_output = "";

		// Add a demo tag
		$this->addTag("Version", function($html, $inner) {
			return "<p>Spark Version 0.3_dev</p>";
		});
	}

	/**
	 * Set the namespace
	 */
	public function setNamespace($namespace) {
		$this->_namespace = $namespace;
	}

	/**
	 * Returns the namespace
	 */
	public function getNamespace() {
		return $this->_namespace;
	}

	/**
	 * Set the namespace callback
	 */
	public function setNamespaceCallback($callback) {
		$this->_namespace_callback = $callback;
	}

	/**
	 * Returns the namespace callback
	 */
	public function getNamespaceCallback() {
		return $this->_namespace_callback;
	}

	/**
	 * Returns the last set of errors
	 */
	public function getErrors() {
		return $this->_errors;
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
	 * Returns a list of all registered tags
	 */
	public function getTags() {
		return $this->_registered_elements;
	}

	/**
	 * Returns the last result the parser output
	 */
	public function getResult() {
		return $this->_output;
	}

	/**
	 * Returns the tag for a given html element
	 * @return <SparkTest> will return SparkTest
	 */
	public function getTagName($tag) {
		$regex = '#</?(.*?)[ >]+#is';
		if (preg_match($regex, $tag, $matches)) {
			return $matches[1];
		}
		return $tag;
	}

	/**
	 * Run through a page and return the resulting markup
	 * 
	 * @param string $html The HTML to render
	 * @return The resulting markup
	 */
	public function run($html) {
		// Reset vars
		$this->_output = "";
		$this->_errors = array();
		$this->_tokens = array();
		$this->_embedded_tokens = array();

		// Breakup the tags
		$lines = $this->breakup($html);

		// Tokenise all namespaced tags
		$html = $this->tokenise($lines);

		// Replace tags with data
		$html = $this->replace($html);

		// Post-process and set output
		$this->_output = $this->postProcess($html);

		return $this->_output;
	}

	/**
	 * Breakup a page, ensures all tags are on a separate line.
	 * Then return an array of lines
	 * 
	 * @param string $html The HTML to break up
	 */
	private function breakup($html) {
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
				// Get the tagname
				$tagname = $this->getTagName($line);

				// Register the token
				$this->_tokens[$token] = array($tagname, $line);

				// Link the token to the previous item on the stack
				if (count($stack) > 0) {
					// List it as embedded, the parent is responsible for the output
					$ptr = end($stack);
					$ptr = $ptr[0];
					$this->_tokens[$ptr][] = "";
					$this->_embedded_tokens[$token] = array($ptr, count($this->_tokens[$ptr]) - 1);
				} else {
					// Tokenise it
					$html .= "<SPARKTOKEN" . $token . ">\n";
				}

				// Are we empty?
				$trimmed_line = trim($line);
				if (strpos($trimmed_line, "/>") !== strlen($trimmed_line) - 2) {
					// We are not an empty tag!
					// Add it to the stack
					$stack[] = array($token, $tagname);
				}

				$token++;
			} elseif (!empty($stack)) {
				// Do we have a closing tag?
				if (stripos($line, "</" . $this->_namespace) !== false) {
					// Get the tagname
					$tagname = $this->getTagName($line);

					// Pop off the stack if we are expecting this to be here
					$t = array_pop($stack);
					if ($t[1] != $tagname) {
						$stack[] = $t;
						$this->_errors[] = "Bad markup: Extra or misplaced closing tag found for element: " . $tagname;
					} else {
						$this->_tokens[$t[0]][] = $line;
					}
				} else {
					// Add the line to the current stack element
					$t = end($stack);
					$this->_tokens[$t[0]][] =  $line;
				}
			} else {
				// Do we have a closing tag? If so, throw an error
				if (stripos($line, "</" . $this->_namespace) !== false) {
					$tagname = $this->getTagName($line);
					$this->_errors[] = "Bad markup: Extra or misplaced closing tag found for element: " . $tagname;
				}

				$html .= $line . "\n";
			}
		}

		// Do we have anything still on the stack? (We shouldnt)
		foreach ($stack as $err) {
			$this->_errors[] = "Bad markup: No closing tag found for element: " . $err[1];

			$token = $err[0];

			// Remove that tag and push all the HTML back in
			array_shift($this->_tokens[$token]);

			$html = str_replace("<SPARKTOKEN" . $token . ">", implode("", $this->_tokens[$token]), $html);
			unset($this->_tokens[$token]);
		}

		// Return HTML to what it looked like before we broke it up
		$html = str_replace(array("\n<", ">\n"), array("<", ">"), $html);

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
			if (!isset($this->_tokens[$token])) continue;

			$data = $this->_tokens[$token];

			$tag = array_shift($data);
			$tag = substr($tag, $tlen - 1);

			if (isset($this->_namespace_callback) || isset($this->_registered_elements[$tag])) {
				$func = isset($this->_namespace_callback) ? $this->_namespace_callback : $this->_registered_elements[$tag];

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
			} else {
				$this->_errors[] = $this->_namespace . $tag . " is not a valid tag!";
				$html = str_replace("<SPARKTOKEN" . $token . ">", "", $html);
			}
		}

		return $html;
	}

	/**
	 * Post-processor to cleanup a page before output.
	 * This is really here just to be overridden
	 */
	protected function postProcess($html) {
		return trim($html);
	}
}
