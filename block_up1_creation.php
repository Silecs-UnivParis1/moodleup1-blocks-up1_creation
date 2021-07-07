<?php
/**
 * @package    block_up1_cration
 * @copyright  2021 Silecs & Université Paris1 Panthéon-Sorbonne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_up1_creation extends block_base {
	
	public $blockname = null;
	
	public function init() {
		$this->blockname = get_class($this);
        $this->title = get_string('title', $this->blockname);
	}
	  
    public function get_content() {
        if ($this->content !== null) {
			return $this->content;
        }
        
        $this->content = (object) [
            'items' => [],
            'icons' => [],
            'text' => null,
            'footer' => "",
        ];

		$this->content->text = $this->get_crswizard_navigation();
		
		return $this->content;
    }
	
	private function get_crswizard_navigation() {
		global $CFG, $USER, $PAGE, $COURSE, $OUTPUT;
		require_once($CFG->dirroot . '/local/crswizard/libaccess.php');
		
		$permcreator = wizard_has_permission('creator', $USER->id);
		$permvalidator = wizard_has_permission('validator', $USER->id);
		$permassistant = false;
		$permsuppression = false;
		$context = $PAGE->context;
		$archived = wizard_course_is_archived($context->instanceid, 'datearchivage');
		if ($context->contextlevel == 50 && $context->instanceid != 1) {
			$permassistant = wizard_update_has_permission($context->instanceid, $USER->id);
			$permsuppression = wizard_has_delete_course($context->instanceid, $USER->id);
			if ($permassistant) {
				$permassistant = wizard_update_course($context->instanceid);
			}
		}
		
		$navigation = '';
		$icon = $OUTPUT->pix_icon('i/navigationitem', '');
		
		if ($permcreator || $permvalidator) {
			$navigation .= html_writer::start_tag('ul', ['type' => 'none']);
			$navigation .= html_writer::start_tag('li');
			$navigation = html_writer::tag('span', get_string('assistant', $this->blockname));
			$navigation .= html_writer::start_tag('ul', ['type' => 'none']);
			if ($permcreator) {
				$lien = html_writer::link(new moodle_url('/local/crswizard/index.php'), $icon . get_string('create', $this->blockname));
				$navigation .= html_writer::tag('li', $lien);
			}
			if ($permvalidator) {
				$lien = html_writer::link(new moodle_url('/local/course_validated/index.php'), $icon . get_string('approve', $this->blockname));
				$navigation .= html_writer::tag('li', $lien);
			}
			if ($permassistant) {
				$url = new moodle_url('/local/crswizard/update/index.php', ['id' => $context->instanceid]);
				$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('update', $this->blockname)));
			}
			if ($permsuppression) {
				$url = new moodle_url('/local/crswizard/delete/index.php', ['id' => $context->instanceid]);
				$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('delete', $this->blockname)));
			}
			if ($permassistant && $archived == FALSE) {
				$url = new moodle_url('/local/crswizard/archive/index.php', ['id' => $context->instanceid]);
				$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('archive', $this->blockname)));
			}
			$navigation .= html_writer::end_tag('ul');
			$navigation .= html_writer::end_tag('li');
			
		} elseif ($permassistant) {
			
			$navigation .= html_writer::start_tag('ul', ['type' => 'none']);
			$navigation .= html_writer::start_tag('li');
			$navigation = html_writer::tag('span', get_string('assistant', $this->blockname));
			$navigation .= html_writer::start_tag('ul');
			$url = new moodle_url('/local/course_validated/index.php', ['id' => $context->instanceid]);
			$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('update', $this->blockname)));
			
			if ($permsuppression) {
				$url = new moodle_url('/local/crswizard/delete/index.php', ['id' => $context->instanceid]);
				$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('delete', $this->blockname)));
			}
			if ($archived == FALSE) {
				$url = new moodle_url('/local/crswizard/archive/index.php', ['id' => $context->instanceid]);
				$navigation .= html_writer::tag('li', html_writer::link($url, $icon . get_string('archive', $this->blockname)));
			}
			$navigation .= html_writer::end_tag('ul');
			$navigation .= html_writer::end_tag('li');
		}
		
		if ($navigation != '') {
			$navigation .= html_writer::end_tag('ul');
		}
		
		return $navigation;
	}
}
