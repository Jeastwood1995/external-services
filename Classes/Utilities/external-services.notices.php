<?php

namespace ExternalServices\Classes\Utilities;

class Notices {

	/**
	 * @var string $message
	 */
    private $message;

	/**
     * Constructor
     *
	 * @param $message
	 */
    public function __construct(string $message = "") {
        if (!empty($this->message)) {
            $this->message = $message;
        }
    }

	public function dbInstallError() {
		if (!empty($this->message)): ?>
			<div class="error notice">
				<p><?php _e( 'There has been an error whilst installing the database tables: ' . $this->message); ?></p>
			</div>
		<?php endif;
	}
}