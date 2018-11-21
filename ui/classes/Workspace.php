<?php
class Workspace {
	private $currentScreen;
	public function __construct() {
		if (isset($_SESSION) && isset($_SESSION['currentScreen'])) $this->currentScreen = $_SESSION['currentScreen'];
		else $this->currentScreen = 0;
	} // construct()
	public function render() {
		switch ($this->currentScreen) {
			case 0: $this->dashboard();
		}
	} // render()
	public function setCurrentScreen($screen) {
		if (!is_numeric($screen) && !ctype_digit($screen)) return;
		if ($screen < 0 || $screen > 0) return;
		$this->currentScreen = $screen;
		$_SESSION['currentScreen'] = $screen;
		$this->render();
	}
	private function dashboard() {
		
	}
} // class Workspace
?>