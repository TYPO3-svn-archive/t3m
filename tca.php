<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_t3m_campaigns"] = Array (
	"ctrl" => $TCA["tx_t3m_campaigns"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,name,description"
	),
	"feInterface" => $TCA["tx_t3m_campaigns"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		## WOP:[tables][1][localization]
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		## WOP:[tables][1][localization]
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_t3m_campaigns',
				'foreign_table_where' => 'AND tx_t3m_campaigns.pid=###CURRENT_PID### AND tx_t3m_campaigns.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		## WOP:[tables][1][localization]
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		## WOP:[tables][1][add_hidden]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		## WOP:[tables][1][add_starttime]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		## WOP:[tables][1][add_endtime]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"name" => Array (		## WOP:[tables][1][fields][1][fieldname]
			"exclude" => 0,		## WOP:[tables][1][fields][1][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_campaigns.name",		## WOP:[tables][1][fields][1][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][1][fields][1][type]
				"size" => "30",	## WOP:[tables][1][fields][1][conf_size]
				"eval" => "required",	## WOP:[tables][1][fields][1][conf_required]
			)
		),
		"description" => Array (		## WOP:[tables][1][fields][3][fieldname]
			"exclude" => 0,		## WOP:[tables][1][fields][3][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_campaigns.description",		## WOP:[tables][1][fields][3][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][1][fields][3][conf_cols]
				"rows" => "5",	## WOP:[tables][1][fields][3][conf_rows]
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, description")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime")
	)
);



$TCA["tx_t3m_targetgroups"] = Array (
	"ctrl" => $TCA["tx_t3m_targetgroups"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,name,description,gender,age_from,age_to,country,zone,zip,salutations_uid,categories_uid,calculated_receivers"
	),
	"feInterface" => $TCA["tx_t3m_targetgroups"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		## WOP:[tables][2][localization]
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		## WOP:[tables][2][localization]
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_t3m_targetgroups',
				'foreign_table_where' => 'AND tx_t3m_targetgroups.pid=###CURRENT_PID### AND tx_t3m_targetgroups.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		## WOP:[tables][2][localization]
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		## WOP:[tables][2][add_hidden]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		## WOP:[tables][2][fields][1][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][1][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.name",		## WOP:[tables][2][fields][1][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][2][fields][1][type]
				"size" => "30",	## WOP:[tables][2][fields][1][conf_size]
				"eval" => "required",	## WOP:[tables][2][fields][1][conf_required]
			)
		),
		"description" => Array (		## WOP:[tables][2][fields][10][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][10][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.description",		## WOP:[tables][2][fields][10][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][2][fields][10][conf_cols]
				"rows" => "5",	## WOP:[tables][2][fields][10][conf_rows]
			)
		),
		"gender" => Array (		## WOP:[tables][2][fields][2][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][2][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.gender",		## WOP:[tables][2][fields][2][title]
			"config" => Array (
				"type" => "select",
				## WOP:[tables][2][fields][2][conf_select_items]
				"items" => Array (
					Array("LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.gender.I.0", "0"),
					Array("LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.gender.I.1", "1"),
					Array("LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.gender.I.2", "2"),
				),
				"size" => 1,	## WOP:[tables][2][fields][2][conf_relations_selsize]
				"maxitems" => 1,	## WOP:[tables][2][fields][2][conf_relations]
			)
		),
		"age_from" => Array (		## WOP:[tables][2][fields][3][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][3][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.age_from",		## WOP:[tables][2][fields][3][title]
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
		"age_to" => Array (		## WOP:[tables][2][fields][9][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][9][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.age_to",		## WOP:[tables][2][fields][9][title]
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
		"country" => Array (		## WOP:[tables][2][fields][4][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][4][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.country",		## WOP:[tables][2][fields][4][title]
			"config" => Array (
				"type" => "select",	## WOP:[tables][2][fields][4][conf_rel_type]
				## WOP:[tables][2][fields][4][conf_rel_dummyitem]
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "static_countries",	## WOP:[tables][2][fields][4][conf_rel_table]
				"foreign_table_where" => "ORDER BY static_countries.uid",	## WOP:[tables][2][fields][4][conf_rel_type]
				"size" => 1,	## WOP:[tables][2][fields][4][conf_relations_selsize]
				"minitems" => 0,
				"maxitems" => 1,	## WOP:[tables][2][fields][4][conf_relations]
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					## WOP:[tables][2][fields][4][conf_wiz_listrec]
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"static_countries",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
				),
			)
		),
		"zone" => Array (		## WOP:[tables][2][fields][5][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][5][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.zone",		## WOP:[tables][2][fields][5][title]
			"config" => Array (
				"type" => "select",	## WOP:[tables][2][fields][5][conf_rel_type]
				## WOP:[tables][2][fields][5][conf_rel_dummyitem]
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "static_country_zones",	## WOP:[tables][2][fields][5][conf_rel_table]
				"foreign_table_where" => "ORDER BY static_country_zones.uid",	## WOP:[tables][2][fields][5][conf_rel_type]
				"size" => 1,	## WOP:[tables][2][fields][5][conf_relations_selsize]
				"minitems" => 0,
				"maxitems" => 1,	## WOP:[tables][2][fields][5][conf_relations]
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					## WOP:[tables][2][fields][5][conf_wiz_listrec]
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"static_country_zones",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
				),
			)
		),
		"zip" => Array (		## WOP:[tables][2][fields][6][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][6][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.zip",		## WOP:[tables][2][fields][6][title]
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
		"salutations_uid" => Array (		## WOP:[tables][2][fields][7][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][7][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.salutations_uid",		## WOP:[tables][2][fields][7][title]
			"config" => Array (
				"type" => "select",	## WOP:[tables][2][fields][7][conf_rel_type]
				## WOP:[tables][2][fields][7][conf_rel_dummyitem]
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_t3m_salutations",	## WOP:[tables][2][fields][7][conf_rel_table]
				"foreign_table_where" => "ORDER BY tx_t3m_salutations.uid",	## WOP:[tables][2][fields][7][conf_rel_type]
				"size" => 1,	## WOP:[tables][2][fields][7][conf_relations_selsize]
				"minitems" => 0,
				"maxitems" => 1,	## WOP:[tables][2][fields][7][conf_relations]
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					## WOP:[tables][2][fields][7][conf_wiz_addrec]
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
					## WOP:[tables][2][fields][7][conf_wiz_listrec]
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
					## WOP:[tables][2][fields][7][conf_wiz_editrec]
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
		"categories_uid" => Array (		## WOP:[tables][2][fields][8][fieldname]
			"exclude" => 0,		## WOP:[tables][2][fields][8][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.categories_uid",		## WOP:[tables][2][fields][8][title]
			"config" => Array (
				"type" => "select",	## WOP:[tables][2][fields][8][conf_rel_type]
				"foreign_table" => "tx_t3m_categories",	## WOP:[tables][2][fields][8][conf_rel_table]
				"foreign_table_where" => "ORDER BY tx_t3m_categories.uid",	## WOP:[tables][2][fields][8][conf_rel_type]
				"size" => 5,	## WOP:[tables][2][fields][8][conf_relations_selsize]
				"minitems" => 0,
				"maxitems" => 10,	## WOP:[tables][2][fields][8][conf_relations]
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					## WOP:[tables][2][fields][8][conf_wiz_addrec]
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
					## WOP:[tables][2][fields][8][conf_wiz_listrec]
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
					## WOP:[tables][2][fields][8][conf_wiz_editrec]
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
		"calculated_receivers" => Array (		## WOP:[tables][2][fields][11][fieldname]
			"exclude" => 1,		## WOP:[tables][2][fields][11][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_targetgroups.calculated_receivers",		## WOP:[tables][2][fields][11][title]
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
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, description, gender, age_from, age_to, country, zone, zip, salutations_uid, categories_uid, calculated_receivers")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_t3m_directmails"] = Array (
	"ctrl" => $TCA["tx_t3m_directmails"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,name,directmail"
	),
	"feInterface" => $TCA["tx_t3m_directmails"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		## WOP:[tables][3][add_hidden]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		## WOP:[tables][3][fields][1][fieldname]
			"exclude" => 0,		## WOP:[tables][3][fields][1][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_directmails.name",		## WOP:[tables][3][fields][1][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][3][fields][1][type]
				"size" => "30",	## WOP:[tables][3][fields][1][conf_size]
			)
		),
		"directmail" => Array (		## WOP:[tables][3][fields][2][fieldname]
			"exclude" => 0,		## WOP:[tables][3][fields][2][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_directmails.directmail",		## WOP:[tables][3][fields][2][title]
			"config" => Array (
				"type" => "group",	## WOP:[tables][3][fields][2][conf_rel_type]
				"internal_type" => "db",	## WOP:[tables][3][fields][2][conf_rel_type]
				"allowed" => "pages",	## WOP:[tables][3][fields][2][conf_rel_table]
				"size" => 1,	## WOP:[tables][3][fields][2][conf_relations_selsize]
				"minitems" => 0,
				"maxitems" => 1,	## WOP:[tables][3][fields][2][conf_relations]
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, name, directmail")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_t3m_categories"] = Array (
	"ctrl" => $TCA["tx_t3m_categories"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,name,description,calculated_receivers"
	),
	"feInterface" => $TCA["tx_t3m_categories"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		## WOP:[tables][4][localization]
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		## WOP:[tables][4][localization]
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_t3m_categories',
				'foreign_table_where' => 'AND tx_t3m_categories.pid=###CURRENT_PID### AND tx_t3m_categories.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		## WOP:[tables][4][localization]
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		## WOP:[tables][4][add_hidden]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		## WOP:[tables][4][fields][2][fieldname]
			"exclude" => 0,		## WOP:[tables][4][fields][2][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_categories.name",		## WOP:[tables][4][fields][2][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][4][fields][2][type]
				"size" => "30",	## WOP:[tables][4][fields][2][conf_size]
				"eval" => "required",	## WOP:[tables][4][fields][2][conf_required]
			)
		),
		"description" => Array (		## WOP:[tables][4][fields][1][fieldname]
			"exclude" => 0,		## WOP:[tables][4][fields][1][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_categories.description",		## WOP:[tables][4][fields][1][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][4][fields][1][conf_cols]
				"rows" => "5",	## WOP:[tables][4][fields][1][conf_rows]
			)
		),
		"calculated_receivers" => Array (		## WOP:[tables][4][fields][3][fieldname]
			"exclude" => 1,		## WOP:[tables][4][fields][3][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_categories.calculated_receivers",		## WOP:[tables][4][fields][3][title]
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
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, description, calculated_receivers")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_t3m_salutations"] = Array (
	"ctrl" => $TCA["tx_t3m_salutations"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,name,single_female,single_male,plural"
	),
	"feInterface" => $TCA["tx_t3m_salutations"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		## WOP:[tables][5][localization]
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		## WOP:[tables][5][localization]
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_t3m_salutations',
				'foreign_table_where' => 'AND tx_t3m_salutations.pid=###CURRENT_PID### AND tx_t3m_salutations.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		## WOP:[tables][5][localization]
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		## WOP:[tables][5][add_hidden]
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (		## WOP:[tables][5][fields][1][fieldname]
			"exclude" => 0,		## WOP:[tables][5][fields][1][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_salutations.name",		## WOP:[tables][5][fields][1][title]
			"config" => Array (
				"type" => "input",	## WOP:[tables][5][fields][1][type]
				"size" => "30",	## WOP:[tables][5][fields][1][conf_size]
			)
		),
		"single_female" => Array (		## WOP:[tables][5][fields][2][fieldname]
			"exclude" => 0,		## WOP:[tables][5][fields][2][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_salutations.single_female",		## WOP:[tables][5][fields][2][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][5][fields][2][conf_cols]
				"rows" => "5",	## WOP:[tables][5][fields][2][conf_rows]
			)
		),
		"single_male" => Array (		## WOP:[tables][5][fields][3][fieldname]
			"exclude" => 0,		## WOP:[tables][5][fields][3][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_salutations.single_male",		## WOP:[tables][5][fields][3][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][5][fields][3][conf_cols]
				"rows" => "5",	## WOP:[tables][5][fields][3][conf_rows]
			)
		),
		"plural" => Array (		## WOP:[tables][5][fields][4][fieldname]
			"exclude" => 0,		## WOP:[tables][5][fields][4][excludeField]
			"label" => "LLL:EXT:t3m/locallang_db.xml:tx_t3m_salutations.plural",		## WOP:[tables][5][fields][4][title]
			"config" => Array (
				"type" => "text",
				"cols" => "30",	## WOP:[tables][5][fields][4][conf_cols]
				"rows" => "5",	## WOP:[tables][5][fields][4][conf_rows]
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, single_female, single_male, plural")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>