<?php
/**
 *@author: Nathan Bullivant
 *@description: Newsletter Object
 * Contains a single issue of newsletter preferably as pdf file.
 * Uploads to the /assets/<URLSegment of parent>
 * Renames file to the next month and year from parentPage->CurrentIssue
 * If this is the first upload assumes current month and year.
 *
 * Child of NewsletterHolder Page Type
 **/

 class Newsletter extends DataObject {

	private static $db = array(
		'IssueMonth'	=> 'Int', //Issue month and year
		'IssueYear'		=> 'Int',
	);
	
	public static $has_one = array(
		'IssuePDF'	=> 'File',	//PDF of this issue
		'Parent' => 'NewsletterHolder', //Reciprocated link back to holder page
	);
	
	private static $summary_fields = array(
		'IssueYear'	=> 'IssueYear',
		'IssueMonth'	=> 'IssueMonth'
    );
	
	static $default_sort = "IssueYear DESC, IssueMonth DESC";

	function getCMSFields() {
		$fields = New FieldList(
			$uploadfield = new UploadField('IssuePDF', 'Vine Upload', $this->IssuePDF),
			new LiteralField('','Folder: '.$this->Parent()->URLSegment)
		);
		$uploadfield->setFolderName($this->Parent()->URLSegment);
		//$uploadfield->setAllowedExtensions('pdf');
		$uploadfield->setCanAttachExisting(false);


        return $fields;
    }

	function onAfterWrite() {
		parent::onAfterWrite();
		// If this is a new upload set the month and year then update current issue in parent.
		if($this->IssueMonth  == NULL) {
			//Get current issue date from Parent Container Page
			$LastIssue = $this->Parent()->CurrentIssue;
            if ($LastIssue == 0) {$LastIssue = date('Y/m/d', strtotime("-1 months"));}
			//Create Month and date for this issue from the LastIssue variable in parent container.
			$ThisIssueMonth = date('n', strtotime($LastIssue))+1;
			$ThisIssueYear = date('Y', strtotime($LastIssue));
			if ($ThisIssueMonth > 12) {
				$ThisIssueMonth = 1;
				++$ThisIssueYear;
			}
			$ThisIssue = "$ThisIssueYear-$ThisIssueMonth";
			$this->IssueMonth = $ThisIssueMonth;
			$this->IssueYear = $ThisIssueYear;
			$this->write();
			//Update container CurrentIssue
			$this->Parent()->nextNewsletterDate("01-{$ThisIssueMonth}-{$ThisIssueYear}");
		}
		//Rename uploaded file to maintain consistent file names.
		$ext1=$this->IssuePDF()->getExtension();
		$this->IssuePDF()->Name=$this->Parent()->URLSegment.'-'.$this->IssueYear.'-'.$this->IssueMonth.'.'.$ext1;
		$this->IssuePDF()->write();
	}
	
	function IssueMonthName () {
		$dateObj   = DateTime::createFromFormat('!m', $this->IssueMonth);
		return $dateObj->format('M'); 
	}
	
// Permissions: Set permissions based on parent. Allows non-admins to edit this dataobject.
	function canView($member=NULL) {
		return $this->Parent()->canView($member);
	}
	function canEdit($member=NULL) {
		return $this->Parent()->canEdit($member);
	}
	function canCreate($member=NULL) {
		return true;
	}
	/*function canDelete($member=NULL) {
		return $this->Parent()->canEdit($member);
	}
	*/
	
}
