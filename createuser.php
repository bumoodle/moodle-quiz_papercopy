<?php

require_once('../../../../config.php');
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
    $params = array ('firstname' => $fname, 'lastname' => $sname, 'idnumber' => 'escert' . $ridnumber);
    $user = $DB->get_record('user', $params, $strictness=IGNORE_MISSING);
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
                         'idnumber' => 'escert' . $ridnumber,
                         'description' => "$from"
                     );

    $user['id'] = user_create_user($user);
    return $user['id'];
}


function enrol_user($userid, $courseid) {
    $enrolment = array (
                        'roleid' => 5,
                        'userid' => $userid,
                        'courseid' => $courseid,
                        'timestart' => time(),
                        'timeend' => time() + (24 * 60 * 60 *30)
                       );
    enrol_manual_external::enrol_users( array($enrolment));
}

