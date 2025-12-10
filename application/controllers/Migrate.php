<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('migration');
    }

    /**
     * Run all migrations up to the latest version.
     *
     * Access via CLI: php index.php migrate
     * Access via web: GET /migrate (only if ENVIRONMENT != 'production')
     */
    public function index()
    {
        

        if ($this->migration->latest() === FALSE) {
            show_error($this->migration->error_string());
            return;
        }

        echo "Migrations ran successfully.\n";
    }
}

