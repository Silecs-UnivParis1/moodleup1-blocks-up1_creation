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
			global $PAGE;
			$PAGE->requires->js('/blocks/up1_creation/javascript/up1creation.js');
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
		$aramlinktoogle = ['class' => 'linktoogle', 'onclick' => 'togglecollapseall("assistantcrsw");', 'id' => 'assistantcrsw'];
		$arambloctoogle = ['type' => 'none', 'class' => 'bloctoogle', 'id' => 'blocassistantcrsw'];
		$iconeslink = $OUTPUT->pix_icon('t/expanded', '', 'moodle') . $OUTPUT->pix_icon('t/collapsed', '', 'moodle', ['class' => 'hidden']);
		
		if ($permcreator || $permvalidator) {
			$navigation .= html_writer::start_tag('ul', ['type' => 'none']);
			$navigation .= html_writer::start_tag('li', $aramlinktoogle);
			$navigation .= $iconeslink;
			$navigation .= html_writer::tag('span', get_string('assistant', $this->blockname));
			$navigation .= html_writer::end_tag('li'); 
			
			$navigation .= html_writer::start_tag('ul', $arambloctoogle);
			if ($permcreator) {
				$url = new moodle_url('/local/crswizard/index.php');
				$navigation .= $this->get_item_crswizard_navigation('li', 'create', $url, 't/add');
			}
			if ($permvalidator) {
				$url = new moodle_url('/local/course_validated/index.php');
				$navigation .= $this->get_item_crswizard_navigation('li', 'approve', $url, 't/approve');
			}
			if ($permassistant) {
				$url = new moodle_url('/local/crswizard/update/index.php', ['id' => $context->instanceid]);
				$navigation .= $this->get_item_crswizard_navigation('li', 'update', $url, 'i/edit');
			}
			if ($permsuppression) {
				$url = new moodle_url('/local/crswizard/delete/index.php', ['id' => $context->instanceid]);
				$navigation .= $this->get_item_crswizard_navigation('li', 'delete', $url, 'i/trash');
			}
			if ($permassistant && $archived == FALSE) {
				$url = new moodle_url('/local/crswizard/archive/index.php', ['id' => $context->instanceid]);
				$navigation .= $this->get_item_crswizard_navigation('li', 'archive', $url, 'i/backup');
			}
			$navigation .= html_writer::end_tag('ul'); 
			
		} elseif ($permassistant) {
			
			$navigation .= html_writer::start_tag('ul', ['type' => 'none']);
			$navigation .= html_writer::start_tag('li', $aramlinktoogle);
			$navigation .= $iconeslink;
			$navigation .= html_writer::tag('span', get_string('assistant', $this->blockname));
			$navigation .= html_writer::end_tag('li'); 
			$navigation .= html_writer::start_tag('ul', $arambloctoogle);
			
			$url = new moodle_url('/local/course_validated/index.php');
			$navigation .= $this->get_item_crswizard_navigation('li', 'approve', $url, 't/approve');
			if ($permsuppression) {
				$url = new moodle_url('/local/crswizard/delete/index.php', ['id' => $context->instanceid]);
				$navigation .= $this->get_item_crswizard_navigation('li', 'delete', $url, 'i/trash');
			}
			if ($archived == FALSE) {
				$url = new moodle_url('/local/crswizard/archive/index.php', ['id' => $context->instanceid]);
				$navigation .= $this->get_item_crswizard_navigation('li', 'archive', $url, 'i/backup');
			}
			$navigation .= html_writer::end_tag('ul');
		}
		
		if ($navigation != '') {
			$navigation .= html_writer::end_tag('ul');
		}
		
		return $navigation;
	}
	
	/**
	 * Construit 
	 * @param string $htmltag tag html
	 * @param string $support identifiant chaine de caractère du fichier de langue du block
	 * @param moodle_url $url
	 * @param string $icone url pix
	 * @return string HTML
	 */
	private function get_item_crswizard_navigation($htmltag, $support, $url, $icone) {
		global $OUTPUT;
		return  html_writer::tag($htmltag, html_writer::link($url, $OUTPUT->pix_icon($icone, '', 'moodle') . get_string($support, $this->blockname)));
	}
}
