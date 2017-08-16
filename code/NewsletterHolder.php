<?php
/**
 * SCA Silverstripe Module
 *
 * Newsletter Holder Page
 * Holds and displays newsletter dataobjects.
 * author Nathan Bullivant
 * 
 **/
class NewsletterHolder extends Page {
	
    public static $db = array(
        'CurrentIssue' => 'Date'
    );
	
	public static $has_many = array(
		'Newsletters'	=> 'Newsletter',
	);
	
    function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Newsleters',
			new LiteralField('','Newsletter Config<br>Set to 1st day of the month for the current issue.')
		);
		$fields->addFieldToTab('Root.Newsleters',
			$ci = new DateField("CurrentIssue", "Current Issue (dd-mm-yyyy)")
		);
		$ci->setConfig('dateformat', 'dd-MM-yyyy');
		$ci->setConfig('showcalendar', true);
		
		// Newsletters
		$newsletterConfig = GridFieldConfig_RecordEditor::create();
		if($this->exists()) {
			// Ensure that fields are generated with knowledge of the parent
			$editComponent = $newsletterConfig->getComponentByType('GridFieldDetailForm');
			$nl = new Newsletter();
			$nl->ParentID = $this->ID;
			$editComponent->setFields($nl->getCMSFields());
		}
		$fields->addFieldToTab('Root.Newsleters',	new GridField('Newsletters', 'Newsletters', $this->Newsletters(), $newsletterConfig));
		return $fields;
    }
	public function nextNewsletterDate($newIssue=NULL) {
		If ($newIssue > 0) {
			$this->CurrentIssue = $newIssue;
			$this->write(); //Write the updated page to draft
			//$this->publish('Stage', 'Live'); // publishes the page to live
		}
	}
	
	public function getNewslettersByYear() {
		$result = GroupedList::create($this->Newsletters()
			->Sort(Array(
				'IssueYear' => 'DESC',
				'IssueMonth' => 'ASC'
			)));
		return $result;
	}
	
	public function getCurrentNewsletter() {
		$result = $this->Newsletters()
			->Sort(Array(
				'IssueYear' => 'DESC',
				'IssueMonth' => 'DESC'
			))->first();
		return $result;
	}
}
class NewsletterHolder_Controller extends Page_Controller {
public static $allowed_actions = array ('download');

	public function download() {
		$object = $this->getCurrentNewsletter();
		$document = $object ? $object->IssuePDF() : null;

		if ( $document )
			return SS_HTTPRequest::send_file(file_get_contents($document->getFullPath()), $document->getFilename() );
		else
			return 'Vine processing error';
	}

}