<?php

########################################################################
# Extension Manager/Repository config file for ext: "t3m"
#
# Auto generated 07-12-2006 05:26
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'T3M [typo3-macher] E-Mail Marketing Tool',
	'description' => 'E-Mail Marketing Extension similar to Direct Mail. provided by typo3macher',
	'category' => 'be',
	'author' => 'Stefan Koch',
	'author_email' => 'typo3@stefkoch.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod0,mod1,mod2,mod3,mod4,mod5,mod6',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'typo3-macher',
	'version' => '0.1.10',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.0-',
			'typo3' => '4.0-',
			'cms' => '',
			'rs_userimp' => '1.0.1-',
			'sr_feuser_register'   => '2.5.1-',
			'static_info_tables' => '2.0.0-',
			'static_info_tables_de' => '2.0.0-',
			'erotea_date2cal' => '1.2.0-',
/** @todo: test these extensions for calendars */
// 			'kj_becalendar'  => '1.0.2',
// 			'date2cal' => '1.1.1-',
// 			'rlmp_dateselectlib' => '0.1.4-', //comment; this one needs programmers intervention if to be used
/** @todo: test if the cc_awstats or ics_awstats extensions can be used for statistics */
// 			'datachart'  =>  '0.1.2-',
// 			'jpgraph' => '0.3.10-',
// 			'mh_omdbchart' => '0.9.6-',
			'pbimagegraph' => '1.0.1-',
			'tcdirectmail' => '1.1.1-',
// 			'tcdmailstats' => '0.0.1-',
		),
		'conflicts' => array(
		),
		'suggests' => array( //@bug: install does not work on our test machine...
// 			'skingreyman' => '0.1.17-',
// 			'skincrystal' =>  '0.4.0-',
			't3mskin' =>  '0.0.1-',
// 			'germandates' => '0.1.0-', // changes all dates to german format, however it broke my register form
// 			'tcdmaildevel'  => '0.0.1-', // only for creating a new targetgroup definition
		),
	),
	'_md5_values_when_last_written' => '',
);

?>