<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lightweight RBAC helper for unified CMMS.
 * Centralises role/session checks for owner (super admin), faculty, and students.
 */
class Rbac
{
    /** @var CI_Controller */
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }

    /** Super admin (owner) session */
    public function owner()
    {
        return $this->CI->session->userdata('owner');
    }

    /** Enforce owner session */
    public function require_owner()
    {
        $user = $this->owner();
        if (!$user) {
            redirect(base_url('OAuth'));
        }
        return $user;
    }

    /** Faculty session, keyed by college slug */
    public function faculty($collegeSlug)
    {
        return $this->CI->session->userdata($collegeSlug);
    }

    /**
     * Enforce faculty session and optional allowed designations.
     *
     * @param string $collegeSlug
     * @param array|string|null $allowedDesignations DESIGNATION_* constants
     */
    public function require_faculty($collegeSlug, $allowedDesignations = null)
    {
        $session = $this->faculty($collegeSlug);
        if (!$session) {
            redirect(base_url("$collegeSlug/login/faculty"));
        }

        if ($allowedDesignations) {
            $allowed = is_array($allowedDesignations) ? $allowedDesignations : [$allowedDesignations];
            if (!in_array($session['designation'], $allowed, true)) {
                redirect(base_url("$collegeSlug/login/faculty"));
            }
        }
        return $session;
    }

    /** Student session, keyed by college slug + `_student` */
    public function student($collegeSlug)
    {
        return $this->CI->session->userdata($collegeSlug . '_student');
    }

    /** Enforce student session */
    public function require_student($collegeSlug)
    {
        $student = $this->student($collegeSlug);
        if (!$student) {
            redirect(base_url("$collegeSlug/student/login"));
        }
        return $student;
    }
}

