<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_t3m_campaigns"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:t3m/locallang_db.xml:tx_t3m_campaigns',		## WOP:[tables][1][title]
		'label' => 'name',	## WOP:[tables][1][header_field]
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',	## WOP:[tables][1][localization]
		'transOrigPointerField' => 'l18n_parent',	## WOP:[tables][1][localization]
		'transOrigDiffSourceField' => 'l18n_diffsource',	## WOP:[tables][1][localization]
		"sortby" => "sorting",	## WOP:[tables][1][sorting]
		"delete" => "deleted",	## WOP:[tables][1][add_deleted]
		"enablecolumns" => Array (		## WOP:[tables][1][add_hidden] / [tables][1][add_starttime] / [tables][1][add_endtime] / [tables][1][add_access]
			"disabled" => "hidden",	## WOP:[tables][1][add_hidden]
			"starttime" => "starttime",	## WOP:[tables][1][add_starttime]
			"endtime" => "endtime",	## WOP:[tables][1][add_endtime]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_t3m_campaigns.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, name, description",
	)
);

$TCA["tx_t3m_targetgroups"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups',		## WOP:[tables][2][title]
		'label' => 'name',	## WOP:[tables][2][header_field]
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',	## WOP:[tables][2][localization]
		'transOrigPointerField' => 'l18n_parent',	## WOP:[tables][2][localization]
		'transOrigDiffSourceField' => 'l18n_diffsource',	## WOP:[tables][2][localization]
		"sortby" => "sorting",	## WOP:[tables][2][sorting]
		"delete" => "deleted",	## WOP:[tables][2][add_deleted]
		"enablecolumns" => Array (		## WOP:[tables][2][add_hidden] / [tables][2][add_starttime] / [tables][2][add_endtime] / [tables][2][add_access]
			"disabled" => "hidden",	## WOP:[tables][2][add_hidden]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_t3m_targetgroups.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, name, description, gender, age_from, age_to, country, zone, zip, salutations_uid, categories_uid, calculated_receivers",
	)
);

$TCA["tx_t3m_categories"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:t3m/locallang_db.xml:tx_t3m_categories',		## WOP:[tables][4][title]
		'label' => 'name',	## WOP:[tables][4][header_field]
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',	## WOP:[tables][4][localization]
		'transOrigPointerField' => 'l18n_parent',	## WOP:[tables][4][localization]
		'transOrigDiffSourceField' => 'l18n_diffsource',	## WOP:[tables][4][localization]
		"sortby" => "sorting",	## WOP:[tables][4][sorting]
		"delete" => "deleted",	## WOP:[tables][4][add_deleted]
		"enablecolumns" => Array (		## WOP:[tables][4][add_hidden] / [tables][4][add_starttime] / [tables][4][add_endtime] / [tables][4][add_access]
			"disabled" => "hidden",	## WOP:[tables][4][add_hidden]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_t3m_categories.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, name, description, calculated_receivers, subcategories",
	)
);


## WOP:[tables][5][allow_ce_insert_records]
t3lib_extMgm::addToInsertRecords("tx_t3m_salutations");

$TCA["tx_t3m_salutations"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:t3m/locallang_db.xml:tx_t3m_salutations',		## WOP:[tables][5][title]
		'label' => 'name',	## WOP:[tables][5][header_field]
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',	## WOP:[tables][5][localization]
		'transOrigPointerField' => 'l18n_parent',	## WOP:[tables][5][localization]
		'transOrigDiffSourceField' => 'l18n_diffsource',	## WOP:[tables][5][localization]
		"sortby" => "sorting",	## WOP:[tables][5][sorting]
		"delete" => "deleted",	## WOP:[tables][5][add_deleted]
		"enablecolumns" => Array (		## WOP:[tables][5][add_hidden] / [tables][5][add_starttime] / [tables][5][add_endtime] / [tables][5][add_access]
			"disabled" => "hidden",	## WOP:[tables][5][add_hidden]
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_t3m_salutations.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, name, single_female, single_male, plural, include_first_name, include_last_name, append",
	)
);

$tempColumns = Array (
	"tx_t3m_country" => Array (		## WOP:[fields][2][fields][4][fieldname]
		"exclude" => 0,		## WOP:[fields][2][fields][4][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:fe_users.tx_t3m_country",		## WOP:[fields][2][fields][4][title]
		"config" => Array (
			"type" => "select",	## WOP:[fields][2][fields][4][conf_rel_type]
			## WOP:[fields][2][fields][4][conf_rel_dummyitem]
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "static_countries",	## WOP:[fields][2][fields][4][conf_rel_table]
			"foreign_table_where" => "ORDER BY static_countries.uid",	## WOP:[fields][2][fields][4][conf_rel_type]
			"size" => 1,	## WOP:[fields][2][fields][4][conf_relations_selsize]
			"minitems" => 0,
			"maxitems" => 1,	## WOP:[fields][2][fields][4][conf_relations]
		)
	),
	"tx_t3m_softbounces" => Array (		## WOP:[fields][2][fields][2][fieldname]
		"exclude" => 1,		## WOP:[fields][2][fields][2][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:fe_users.tx_t3m_softbounces",		## WOP:[fields][2][fields][2][title]
		"config" => Array (
			"type" => "input",
			"size" => "4",
			"max" => "4",
			"eval" => "int",
			"checkbox" => "0",
			"range" => Array (
				"upper" => "1000",
				"lower" => "10"
			),
			"default" => 0
		)
	),
	"tx_t3m_hardbounces" => Array (		## WOP:[fields][2][fields][3][fieldname]
		"exclude" => 1,		## WOP:[fields][2][fields][3][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:fe_users.tx_t3m_hardbounces",		## WOP:[fields][2][fields][3][title]
		"config" => Array (
			"type" => "input",
			"size" => "4",
			"max" => "4",
			"eval" => "int",
			"checkbox" => "0",
			"range" => Array (
				"upper" => "1000",
				"lower" => "10"
			),
			"default" => 0
		)
	),
	"tx_t3m_categories" => Array (		## WOP:[fields][2][fields][1][fieldname]
		"exclude" => 0,		## WOP:[fields][2][fields][1][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:fe_users.tx_t3m_categories",		## WOP:[fields][2][fields][1][title]
		"config" => Array (
			"type" => "select",	## WOP:[fields][2][fields][1][conf_rel_type]
			"foreign_table" => "tx_t3m_categories",	## WOP:[fields][2][fields][1][conf_rel_table]
			"foreign_table_where" => "ORDER BY tx_t3m_categories.uid",	## WOP:[fields][2][fields][1][conf_rel_type]
			"size" => 4,	## WOP:[fields][2][fields][1][conf_relations_selsize]
			"minitems" => 0,
			"maxitems" => 20,	## WOP:[fields][2][fields][1][conf_relations]
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				## WOP:[fields][2][fields][1][conf_wiz_addrec]
				"add" => Array(
					"type" => "script",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_t3m_categories",
						"pid" => "###CURRENT_PID###",
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
				),
				## WOP:[fields][2][fields][1][conf_wiz_listrec]
				"list" => Array(
					"type" => "script",
					"title" => "List",
					"icon" => "list.gif",
					"params" => Array(
						"table"=>"tx_t3m_categories",
						"pid" => "###CURRENT_PID###",
					),
					"script" => "wizard_list.php",
				),
				## WOP:[fields][2][fields][1][conf_wiz_editrec]
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		)
	),
	"tx_t3m_salutation" => Array (		## WOP:[fields][2][fields][5][fieldname]
		"exclude" => 0,		## WOP:[fields][2][fields][5][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:fe_users.tx_t3m_salutation",		## WOP:[fields][2][fields][5][title]
		"config" => Array (
			"type" => "select",	## WOP:[fields][2][fields][5][conf_rel_type]
			## WOP:[fields][2][fields][5][conf_rel_dummyitem]
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "tx_t3m_salutations",	## WOP:[fields][2][fields][5][conf_rel_table]
			"foreign_table_where" => "ORDER BY tx_t3m_salutations.uid",	## WOP:[fields][2][fields][5][conf_rel_type]
			"size" => 1,	## WOP:[fields][2][fields][5][conf_relations_selsize]
			"minitems" => 0,
			"maxitems" => 1,	## WOP:[fields][2][fields][5][conf_relations]
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				## WOP:[fields][2][fields][5][conf_wiz_addrec]
				"add" => Array(
					"type" => "script",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_t3m_salutations",
						"pid" => "###CURRENT_PID###",
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
				),
				## WOP:[fields][2][fields][5][conf_wiz_listrec]
				"list" => Array(
					"type" => "script",
					"title" => "List",
					"icon" => "list.gif",
					"params" => Array(
						"table"=>"tx_t3m_salutations",
						"pid" => "###CURRENT_PID###",
					),
					"script" => "wizard_list.php",
				),
				## WOP:[fields][2][fields][5][conf_wiz_editrec]
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		)
	),
);


t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_t3m_country;;;;1-1-1, tx_t3m_softbounces, tx_t3m_hardbounces, tx_t3m_categories, tx_t3m_salutation");

$tempColumns = Array (
	"tx_t3m_campaign" => Array (		## WOP:[fields][3][fields][1][fieldname]
		"exclude" => 0,		## WOP:[fields][3][fields][1][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:pages.tx_t3m_campaign",		## WOP:[fields][3][fields][1][title]
		"config" => Array (
			"type" => "select",	## WOP:[fields][3][fields][1][conf_rel_type]
			## WOP:[fields][3][fields][1][conf_rel_dummyitem]
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "tx_t3m_campaigns",	## WOP:[fields][3][fields][1][conf_rel_table]
			"foreign_table_where" => "ORDER BY tx_t3m_campaigns.uid",	## WOP:[fields][3][fields][1][conf_rel_type]
			"size" => 1,	## WOP:[fields][3][fields][1][conf_relations_selsize]
			"minitems" => 0,
			"maxitems" => 1,	## WOP:[fields][3][fields][1][conf_relations]
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				## WOP:[fields][3][fields][1][conf_wiz_addrec]
				"add" => Array(
					"type" => "script",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_t3m_campaigns",
						"pid" => "###CURRENT_PID###",
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
				),
				## WOP:[fields][3][fields][1][conf_wiz_listrec]
				"list" => Array(
					"type" => "script",
					"title" => "List",
					"icon" => "list.gif",
					"params" => Array(
						"table"=>"tx_t3m_campaigns",
						"pid" => "###CURRENT_PID###",
					),
					"script" => "wizard_list.php",
				),
				## WOP:[fields][3][fields][1][conf_wiz_editrec]
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		)
	),
	"tx_t3m_spam_score" => Array (		## WOP:[fields][3][fields][2][fieldname]
		"exclude" => 1,		## WOP:[fields][3][fields][2][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:pages.tx_t3m_spam_score",		## WOP:[fields][3][fields][2][title]
		"config" => Array (
			"type" => "input",	## WOP:[fields][3][fields][2][type]
			"size" => "10",	## WOP:[fields][3][fields][2][conf_size]
		)
	),
	"tx_t3m_personalized" => Array (		## WOP:[fields][3][fields][3][fieldname]
		"exclude" => 1,		## WOP:[fields][3][fields][3][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:pages.tx_t3m_personalized",		## WOP:[fields][3][fields][3][title]
		"config" => Array (
			"type" => "check",
			"default" => 1,	## WOP:[fields][3][fields][3][conf_check_default]
		)
	),
);


t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("pages","tx_t3m_campaign;;;;1-1-1, tx_t3m_spam_score, tx_t3m_personalized");

$tempColumns = Array (
	"tx_t3m_target" => Array (		## WOP:[fields][1][fields][1][fieldname]
		"exclude" => 1,		## WOP:[fields][1][fields][1][excludeField]
		"label" => "LLL:EXT:t3m/locallang_db.xml:tx_tcdirectmail_targets.tx_t3m_target",		## WOP:[fields][1][fields][1][title]
		"config" => Array (
			"type" => "select",	## WOP:[fields][1][fields][1][conf_rel_type]
			"foreign_table" => "tx_t3m_targetgroups",	## WOP:[fields][1][fields][1][conf_rel_table]
			"foreign_table_where" => "ORDER BY tx_t3m_targetgroups.uid",	## WOP:[fields][1][fields][1][conf_rel_type]
			"size" => 5,	## WOP:[fields][1][fields][1][conf_relations_selsize]
			"minitems" => 0,
			"maxitems" => 10,	## WOP:[fields][1][fields][1][conf_relations]
			"wizards" => Array(
				"_PADDING" => 2,
				"_VERTICAL" => 1,
				## WOP:[fields][1][fields][1][conf_wiz_addrec]
				"add" => Array(
					"type" => "script",
					"title" => "Create new record",
					"icon" => "add.gif",
					"params" => Array(
						"table"=>"tx_t3m_targetgroups",
						"pid" => "###CURRENT_PID###",
						"setValue" => "prepend"
					),
					"script" => "wizard_add.php",
				),
				## WOP:[fields][1][fields][1][conf_wiz_listrec]
				"list" => Array(
					"type" => "script",
					"title" => "List",
					"icon" => "list.gif",
					"params" => Array(
						"table"=>"tx_t3m_targetgroups",
						"pid" => "###CURRENT_PID###",
					),
					"script" => "wizard_list.php",
				),
				## WOP:[fields][1][fields][1][conf_wiz_editrec]
				"edit" => Array(
					"type" => "popup",
					"title" => "Edit",
					"script" => "wizard_edit.php",
					"popup_onlyOpenIfSelected" => 1,
					"icon" => "edit2.gif",
					"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
				),
			),
		)
	),
);

## WOP:[module][1]
if (TYPO3_MODE=="BE")	{
	## 1. and 2. parameter is WOP:[module][1][position] , 3. parameter is WOP:[module][1][subpos]
	t3lib_extMgm::addModule("txt3mM0","","before:web",t3lib_extMgm::extPath($_EXTKEY)."mod0/");
	t3lib_extMgm::addModule("txt3mM0","txt3mM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
	t3lib_extMgm::addModule("txt3mM0","txt3mM2","",t3lib_extMgm::extPath($_EXTKEY)."mod2/");
	t3lib_extMgm::addModule("txt3mM0","txt3mM3","",t3lib_extMgm::extPath($_EXTKEY)."mod3/");
	t3lib_extMgm::addModule("txt3mM0","txt3mM4","",t3lib_extMgm::extPath($_EXTKEY)."mod4/");
	t3lib_extMgm::addModule("txt3mM0","txt3mM5","",t3lib_extMgm::extPath($_EXTKEY)."mod5/");
// 	t3lib_extMgm::addModule("txt3mM0","txt3mM6","",t3lib_extMgm::extPath($_EXTKEY)."mod6/");
}

t3lib_div::loadTCA("tc_directmail_targets");
t3lib_extMgm::addTCAcolumns("tx_tcdirectmail_targets",$tempColumns,1);
$TCA["tx_tcdirectmail_targets"]["types"]["tx_t3m_target1"]["showitem"] = "hidden;;1;;1-1-1, title, plain_only, targettype, tx_t3m_target, ;;;;2-2-2, calculated_receivers;;;;1-1-1";
$TCA["tx_tcdirectmail_targets"]["columns"]["targettype"]["config"]["items"][] = array("LLL:EXT:t3m/locallang_db.xml:tx_tcdirectmail_targets.optt3m_target1", "tx_t3m_target1");


## WOP:[pi][1][addType]
t3lib_extMgm::addPlugin(array('LLL:EXT:t3m/locallang_db.xml:tt_content.header_layout_pi1', $_EXTKEY.'_pi1'),'header_layout');
?>