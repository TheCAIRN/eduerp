<?php
/*
 * TODO:
 * Allow the logobar template to be defined in either a theme file or the database.
 */
class Logobar {
	private $logopath;
	private $sitename;
	public function __construct() {
		$this->logopath = 'images/logo.jpg';
		$this->sitename = isset($_SESSION['sitename'])?$_SESSION['sitename']:'eduERP';
	}
	public function render() {
		$html = '';
		$html .= '<IMG id="logoimage" src="'.$this->logopath.'" alt="Site logo" title="'.$this->sitename.'" />';
		$html .= '<DIV id="sitename">'.$this->sitename.'</DIV>';
		echo $html;
	}
} // class Logobar
?>
