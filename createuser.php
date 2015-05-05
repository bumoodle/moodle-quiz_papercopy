<?php

// require_once('../../../../config.php');
global $CFG;
require_once($CFG->dirroot . '/enrol/manual/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');

function create_and_enrol_user($student_name, $ruserid, $courseid) {
    if (preg_match('/^\s*(.+?)\s*(\S+)\s*$/', $student_name, $names)) {
        $fname = $names[1];
        $sname = $names[2];
    } else {
        $fname = '';
        $sname = $student_name;
    }
    $userid = get_existing_user($ruserid, $fname, $sname);
    if (!$userid) {
        $userid = create_user($ruserid, $fname, $sname);
    }
    enrol_user($userid, $courseid);
}


function get_existing_user($ruserid, $fname, $sname) {
    global $DB;
    $params = array ('firstname' => $fname, 'lastname' => $sname, 'idnumber' => 'escert' . $ruserid);
    $userid = $DB->get_field('user', 'id', $params, $strictness=IGNORE_MISSING);
    return $userid;
}


function create_user($ridnumber, $fname, $sname) {
    if(!$ridnumber) {
        throw new coding_exception('scantron student idnumber (teex ID) is empty');
    }
    $authtype = 'nologin';
    $from = 'from certification office';
    $user = array(
                         'username' => 'escert' . $ridnumber,
                         'firstname' => $fname,
                         'lastname' => $sname,
                         'auth' => $authtype,
                         'idnumber' => $ridnumber,
                         'description' => "$from"
                     );

    $user['id'] = user_create_user($user);
    return $user['id'];
}


function enrol_user($userid, $courseid) {
    global $DB;

    if (!enrol_is_enabled('manual')) {
        return false;
    }

    if (!$enrol = enrol_get_plugin('manual')) {
        return false;
    }
    if (!$instances = $DB->get_records('enrol', array('enrol'=>'manual', 'courseid'=>$courseid, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder,id ASC')) {
        return false;
    }
    $instance = reset($instances);
    $enrol->enrol_user($instance, $userid, $instance->roleid, time(), time() + (24 * 60 * 60 *30));

    /*
    $enrolment = array (
                        'roleid' => 5,
                        'userid' => $userid,
                        'courseid' => $courseid,
                        'timestart' => time(),
                        'timeend' => time() + (24 * 60 * 60 *30)
                       );
    enrol_try_internal_enrol($courseid, $userid, $roleid = null, $timestart = 0, $timeend = 0) {
    enrol_manual_external::enrol_users( array($enrolment));
    function enrol_user(stdClass $instance, $userid, $roleid = null, $timestart = 0, $timeend = 0,
    */
}

