<?php

namespace ExternalServices\Classes\Utilities;

class Notices {
	public static function getLoaderSpinnerHtml(String $message = null): string {
		$message = $message ?? 'Loading...';

		return '<div class="loader">
                <p class="loader-message">' . $message . '</p>
               <span class="loader-spinner"></span>
            </div>';
	}

	public static function displayJsAlert(String $message) {
		echo '<script type="text/javascript">alert(' . $message . ')</script>';
		echo '<script type="text/javascript">window.history.back()</script>';
		exit;
	}
}