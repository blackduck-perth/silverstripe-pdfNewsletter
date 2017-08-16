<?php

class NewsletterSiteConfig extends DataExtension {

    public static $db = array(
        'CurrentIssue' => 'Date'
    );

    public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Main',
			new LiteralField('','Newsletter Config<br>Set to 1st day of the month for the current issue.')
		);
		$fields->addFieldToTab('Root.Main',
			$ci = new DateField("CurrentIssue", "Current Issue (dd-mm-yyyy)")
		);
		$ci->setConfig('dateformat', 'dd-MM-yyyy');
		$ci->setConfig('showcalendar', true);
    }
}
