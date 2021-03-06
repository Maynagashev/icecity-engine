<?php

/*
Пресс-релизы / сми

Для создания списков выборки по месяцам, дням и тд ипспользуется m_cache

*/
class m_press extends class_model {
  var $tables = array(
    'press' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `ann` text,
      `text` text,
      `date` datetime default NULL,
      `copyright` varchar(255) NOT NULL default '',
      
      `status_id` tinyint(1) not null default '0',
      `cat_id` varchar(255) not null default '0',
      `views` int(11) NOT NULL default '0',
      `replycount` int(11) NOT NULL default '0',
        
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `refer` varchar(255) default NULL,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`status_id`),
      KEY (`date`)
    "
  );
  
  var $page_url = "/press_releases/";
  
  // cache var название записи в кэше
  var $cvar_months = "press_months";
  var $cache_interval_h = 0.5;
  
  var $status_ar = array(
    0 => "Черновик",
    1 => "Опубликована",
    2 => "Отключена"
    
  );
     
  var $cats = array(
    'press' => "Пресс-релизы",
    'smi' => "Публикации в СМИ"
  );
  
  var $editor_id = "press";
  
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['press'];
 
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit', 'create')
  ));      

  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  

  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Категория',
  'type' => 'varchar',   
  'input' => 'select',
  'belongs_to' => array('list' => $this->cats),
  'show_in' => array(),
  'write_in' => array('create', 'edit'), 
  ));    

  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Категория',
  'virtual' => 'cat_id',
  'show_in' => array('default'),
  'write_in' => array(), 
  ));    
   

  $this->init_field(array(
  'name' => 'ann',
  'title' => 'Текст анонса',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array(),
  'write_in' => array(), 
  ));    
  


  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотров',
  'type' => 'int',
  'len' => 5,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    

  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'input' => 'select',  
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('remove'),  
  'write_in' => array('create', 'edit')
  ));    
  
  


  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'show_in' => array( 'remove'),
  'write_in' => array()
  ));         
  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',
  'show_in' => array('remove'),
  'write_in' => array()
  ));         
  

  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',
  'show_in' => array('remove'),
  'write_in' => array()
  ));
  


  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',   
  'len'  => '100',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'), 
  'id' => 'full-text'
  ));    

  
  $this->init_field(array(
  'name' => 'copyright',
  'title' => 'Ссылка на источник',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    
     
  
}

function parse($d) {
  global $std;
  $d['f_date'] = $std->time->format($d['date'], 0.9, 1); 
  $d['f_date_rus'] = $std->time->format($d['date'], 1, 1);
  
  $d['url'] = $this->page_url."item/?id={$d['id']}";
  $d['edit_url'] = u($this->editor_id, 'edit', $d['id']);
  
  if (preg_match("#^http#si", $d['copyright'])) {
    $d['f_copyright'] = "<a href='{$d['copyright']}'>{$d['copyright']}</a>";
  }
  elseif(preg_match("#^www#si", $d['copyright'])) {
    $d['f_copyright'] = "<a href='http://{$d['copyright']}'>{$d['copyright']}</a>";
  }
  else {
    $d['f_copyright'] = $d['copyright'];
  }
  
  return $d;
}

// validations
function v_title($t) {
  
  $t = trim($t);
 
  return $t;
}

function df_title($t) {  
  return "<div align=left>{$t}</div>";
}

function last_v($p) {
  global $sv, $std, $db;
  
  if ($this->code=='create') {
    //$p['date'] = $sv->date_time;  
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
  }
  
  return $p;
}

// controllers
function c_edit() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  $s = $this->init_submit(); 
  
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {      
      header("Location: ".su($sv->act).$this->slave_url_addon);  exit();
    }
    $d = $this->get_current_record();     
  }
  
  $ret['s'] = $s;  
  $this->table_compact = 0;
  $this->table_width = "100%";
  $ret['form'] = $this->compile_edit_table($d);
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object($this->editor_id, $d['id'], $sv->user['session']['account_id']);
  $sv->parsed['admin_sidebar'] = "
    <div style='margin-top: 400px; border: 1px solid #dddddd;'>
      <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
      {$ret['attach']['form']}
    </div>";
  
// markitup
  $std->markitup->use_emoticons = 0;
  $std->markitup->width = '100%';
  $ret['markitup'] = $std->markitup->js("#full-text", "html");
    
  return $ret;
}

function c_attaches() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  if (!$d) die("Объект не найден.");
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object($this->editor_id, $d['id'], $sv->user['session']['account_id']);
  
  return $ret;
}

// public_controllers
/**
 * Обычный список по порядку
 *
 * @return unknown
 */
function c_public_list() {
  global $sv, $std, $db;
  
  $ret = $this->item_list_pls("`status_id`='1'", "`date` DESC", $this->per_page, 1, 0, $sv->view->safe_url."?page=");
  
  $ret['months'] = $this->month_list(0);
  
  return $ret;
}

/**
 * Подробный вид
 *
 * @return unknown
 */
function c_public_item() {
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  
  $sv->vars['p_title'] = $d['title'];
  $ret['d'] = $d;
  return $ret;
}

/**
 * Выборка за месяц (не тестировалось)
 *
 * @return unknown
 */
function c_public_month() {
  global $sv, $std, $db;
  
  $year = (isset($sv->_get['year'])) ? intval($sv->_get['year']) : 0;
  $month = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
     
  if ($month<1 || $month>12 || $year<1950 || $year>2100) {
    $sv->view->show_err_page("badrequest");
  }
 
  $ret = $this->item_list_pls( "year(`date`)='{$year}' AND month(`date`)='{$month}'", 
         "`date` asc", $this->per_page, 1, 0, $sv->view->safe_url."?id={$month}&year={$year}&page=");
         
  $ret['months'] = $this->month_list(0);
  $ret['month'] = $month;
  $ret['year'] = $year;       
  
  
  return $ret;
}


// actions

function month_list($force=0) {
  global $sv, $std, $db;
    
  $sv->load_model('cache');
  $ar = $sv->m['cache']->read($this->cvar_months, 1, 1, array());
  
  $c = $sv->m['cache']->d;  
  $exp = $sv->post_time - 60*60*$this->cache_interval_h; // 1 day
  
  // if expired update
  if ($c['time']<$exp || $force) {
    $ar = $this->parse_month_list();
    $sv->m['cache']->write($this->cvar_months, $ar, 1);
  }
        
  return $ar;
}

function parse_month_list() {
  global $sv, $std, $db; $ret = array();
  
  $ar = array();
  $db->q("SELECT year(`date`) as year, month(`date`) as month FROM {$this->t} ORDER by date asc", __FILE__, __LINE__);
  while ($d = $db->f()) {
    if (isset($ar[$d['year']][$d['month']])) {
      $ar[$d['year']][$d['month']]['count']++;
    }
    else {
      $p = array(
        'title' => $std->time->monthtorus($d['month']),
        'count' => 1        
      );
      $ar[$d['year']][$d['month']] = $p;
    }
  }
  
  return $ar;
}

//eoc
}

?>