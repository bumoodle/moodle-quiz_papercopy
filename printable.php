<?php

/**
 * PDF output generation for quiz paper copies
 * @author ktemkin
 */


//ini_set('display_errors', 1); 
//error_reporting(E_ALL);

require_once('../../../../config.php');
require_once('lib.php');

//two possible core identifiers: attempt ID, or coursemodule ID
$attempt_id = optional_param('attempt', 0, PARAM_INT);
$cm_id = optional_param('id', 0, PARAM_INT);

//supplementary identification, for printing within a coursemodule
//these identify what should be printed
$quba_id = optional_param('usage', 0, PARAM_INT);
$batch_id = optional_param('batch', 0, PARAM_INT);
$as_zip = optional_param('zip', 0, PARAM_INT);
$force_rerender = optional_param('rerender', 0, PARAM_INT);
$prerender = optional_param('prerender', 0, PARAM_INT);

//DEBUG FIXME
$force_rerender = 1;

//if an attempt ID was provided, use it first, as it's most specific
if($attempt_id) {

    //print the given attempt, if the user has the appropriate access
    printable_copy_helper::print_attempt($attempt_id);
}
//otherwise, fall back on the coursemoudule ID
elseif($cm_id)
{
    //create a new printable_copy_helper
    $printer = printable_copy_helper::create_from_coursemodule($cm_id);

    //retrieve optional arguments for the printer, which are only appropriate for the report view
    $key = optional_param('key', false, PARAM_BOOL);
    $key_only = optional_param('keyonly', false, PARAM_BOOL);

    //if a QUBA was specified, print it, and only it (the helper methods check permissions)
    if($quba_id) {
        $printer->print_question_usage_by_activity($quba_id, $key, $key_only);
    }
    //otherwise, use a batch, if provided
    else if($batch_id) {

        //disable purification on batch jobs, as it takes up too much RAM
        //(the print_batch function will handle purification)
        core_pdf_renderer::$do_not_purify = true;

        if(!$force_rerender) {
            //attempt to print an existing copy, if one exists
            $printed = $printer->print_prerendered_batch($batch_id);
            
            if($printed)
                exit();
        }

        if($prerender) {

            //if we didn't find an existing copy, start the interactive pre-renderer
            if(!$printed)
                $printer->interactive_prerender($batch_id, true);
            
            //never continue to the theme
            exit();

        } else {
            //print the entire batch
            $printer->print_batch($batch_id, $key, $key_only, $as_zip);
        }

        //if we just finished printing a zip,
        if($as_zip) {
            exit();
        }
    }

    //if neither was provided, we can't continue; print an error
    else
        print_error('missingparam', 'error', '', 'usage');
}
else
{
    //if neither was provided, throw a missing parameter error
    print_error('missingparam', 'error', '', 'id');
}


printable_copy_helper::render_pdf();



