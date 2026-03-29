<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Health extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('', true);
    }

    public function check()
    {
        $status = [
            'status' => 'UP',
            'timestamp' => date('Y-m-d H:i:s'),
            'checks' => []
        ];
        $has_error = false;

        try {
            $this->db->db_debug = false;

            if ($this->db->initialize()) {
                $status['checks']['database'] = 'OK';
            } else {
                throw new Exception("Database connection failed");
            }
        } catch (Exception $e) {
            $status['checks']['database'] = 'DOWN';
            $has_error = true;
        }

        if ($this->load->driver('cache', ['adapter' => 'redis'])) {
            $status['checks']['cache'] = $this->cache->redis->is_supported() ? 'OK' : 'DOWN';
        }

        if ($has_error) {
            $status['status'] = 'DOWN';
            $this->output->set_status_header(503);
        } else {
            $this->output->set_status_header(200);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($status));
    }
}
