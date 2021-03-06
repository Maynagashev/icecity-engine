<?php
/**
 * ADMIN - POST
 * !admin session required
 *
 */
DEFINE ("EXEC_TIME", microtime());

require($_SERVER['DOCUMENT_ROOT']."/path_cfg.php");

// DATABASE
require_once(LIB_DIR."class_mysql.php");	
$db = new mysql(PUBLIC_DIR."mysql_cfg.php");	
$db->connect();

// SV            
require(SOURCES_DIR."class_sv.php");
$sv = new class_sv();  	

// FUNCTIONS
require_once(LIB_DIR."functions.php");
require_once(LIB_DIR."class_func.php");	
$std = new func;  

// STD FUNCTIONS  
require_once(LIB_DIR."std/time.php");	
$std->time = new std_time;       
require_once(LIB_DIR."std/text.php");	
$std->text = new std_text;  	  	
require_once(LIB_DIR."std/file.php");	
$std->file = new std_file;
  	  	
//TEMPLATES
require(SMARTY_DIR.'Smarty.class.php');
$smarty = new Smarty;  
$smarty->template_dir = SOURCES_DIR.'templates/';
$smarty->compile_dir = SOURCES_DIR.'smarty/templates_c/';
$smarty->config_dir = SOURCES_DIR.'smarty/configs/';
$smarty->cache_dir = SOURCES_DIR.'smarty/cache/';
$smarty->caching = false;  
$smarty->register_function("u", "url");
$smarty->register_function("su", "sub_url");

$sv->init_class('model');

// SESSION
$sv->load_model('session');
$sv->m['session']->start();
if (intval($sv->user['session']['group_id'])!==3) {
  die("not_authorized");
}

// ACT
$acts = array('page_childs', 'tree_childs');

$act = (isset($_REQUEST['act'])) ? $_REQUEST['act'] : "default";
if (in_array($act, $acts)) {
  eval("act_{$act}();");
}
else {
  die("no action specified");
}

// ACTIONS  ===============================

function act_page_childs() {  
  global $sv;  
  $id = (isset($sv->_r['id'])) ? $sv->_r['id'] : 0;
  $sv->load_model('page');   
  $ret = $sv->m['page']->ajax_page_childs($id);    
  echo $ret;
}
  
function act_tree_childs() {  
  global $sv;  
  $model = (isset($sv->_r['model'])) ? $sv->_r['model'] : '';
  $model = preg_replace("#[^a-z0-9\_]#si", "", $model);
  $module = (isset($sv->_r['module'])) ? $sv->_r['module'] : '';
  $module = preg_replace("#[^a-z0-9\_]#si", "", $module);
  
  
  $id = (isset($sv->_r['id'])) ? $sv->_r['id'] : 0;
  $sv->load_model($model);   
  $ret = $sv->m[$model]->ajax_tree_childs($id, $module);
  echo $ret;
}
exit();
?>