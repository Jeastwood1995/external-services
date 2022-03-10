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
}