<?php

namespace ExternalServices\Classes\Utilities;

class Form_Controller {
    public function processAddServicePostData() {
        wp_redirect( admin_url( '/admin.php?page=external-services-configure' ) );
        exit;
    }
}