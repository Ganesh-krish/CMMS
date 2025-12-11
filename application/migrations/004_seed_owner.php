<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_owner extends CI_Migration
{
    public function up()
    {
        $email = 'admin@cmms.local';
        $password_plain = 'Admin@123';
        $password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

        $exists = $this->db->get_where(TABLE_OWNER, ['email' => $email])->row_array();
        if (!$exists) {
            $data = [
                'email' => $email,
                'password' => $password_hash,
                'name' => 'Super Admin',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert(TABLE_OWNER, $data);
        }
    }

    public function down()
    {
        $email = 'admin@cmms.local';
        $this->db->where('email', $email)->delete(TABLE_OWNER);
    }
}

