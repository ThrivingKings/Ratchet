<?php

/**
 * REST request class
 *
 * A simple solution for making REST requests
 *
 * @author Daniel Raftery
 * @version 1.0
*/

class REST {

	var $headers;
	var $content_type;
	var $curl_opts;
	var $ch;
	var $code;

	/**
	 * Sets the Content-Type for the upcoming request. Must be set before a request is made.
	 *
	 * @param string The HTTP specific content type
	*/
	function Content($type) {
		
		$this->headers[] = "Content-Type: $type";

		// Set the type on the object, for reference later
		$this->content_type = $type;
	}

	/**
	 * Sets the Accept-Type for the upcoming request. Must be set before a request is made.
	 *
	 * @param string The HTTP specific accept type
	*/
	function Accept($type) {
		
		$this->headers[] = "Accept-Type: $type";
	}

	/**
	 * Allows for additional or custom curl options to be set for the upcoming request.
	 * Refer to http://www.php.net/manual/en/function.curl-setopt.php for all options.
	 *
	 * @param [mixed] Array of curl options
	*/
	function Opts($opts_array) {
		
		$this->curl_opts = $opts_array;
	}

	/**
	 * A POST request
	 *
	 * @param string The URL for the request
	 * @param [mixed] Array of parameters for the request (does not encode JSON)
	 *
	 * @return [mixed] The response from the requested URL
	*/
	function POST($url, $params) {

		// initialize the request
		$this->ch = curl_init($url);

		// set the POST fields
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);

		return $this->call();
	}

	/**
	 * A GET request
	 *
	 * @param string The URL for the request
	 * @param [mixed] Array of parameters for the request (does not encode JSON)
	 *
	 * @return [mixed] The response from the requested URL
	*/
	function GET($url, $params) {

		// Build the query, if params exist
		if($params) {
		
			$url .= "?".http_build_query($params);
		}

		// initialize the request
		$this->ch = curl_init($url);

		return $this->call();
	}

	/**
	 * [private] The actual CURL request. The response code is set on the object
	 *
	 * @return [mixed] The CURL response
	*/
	private function call() {

		// Set the headers
		if($this->headers) {
		
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
		}

		// Set the additional curl options
		if($this->curl_opts) {
		
			curl_setopt_array($this->ch, $this->curl_opts);
		}

		// We want a return
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

		// Execute the request
		$response = curl_exec($this->ch);

		// Set the HTTP code on the object
		$this->code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

		// Shut 'er down
		curl_close($this->ch);

		return $response;
	}
}

?>