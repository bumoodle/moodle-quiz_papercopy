<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/engine/lib.php');


class question_display_options_pdf extends question_display_options
{
    /**
     * Creates a new question_display_options objects, then overrides the defaults to reflect
     * values appropriate for PDF generation.
     */
    public function __construct()
    {
        //hide all grading information 
        //FIXME respect usage / etc, information, and possibly display max marks?
        $this->marks = question_display_options::HIDDEN;
        $this->feedback = question_display_options::HIDDEN;
        $this->generalfeedback = question_display_options::HIDDEN;
        $this->correctness = question_display_options::HIDDEN;
        $this->flags = question_display_options::HIDDEN;
        $this->numpartscorrect = question_display_options::HIDDEN;
        $this->manualcomment = question_display_options::HIDDEN;
        $this->manualcommentlink = question_display_options::HIDDEN;
        $this->quesitonreviewlink = question_display_options::HIDDEN;
        $this->history= question_display_options::HIDDEN;
        $this->rightanswer= question_display_options::HIDDEN;
        //$this->= question_display_options::HIDDEN;
    }
}
