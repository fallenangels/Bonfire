<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2011 Lonnie Ezell

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

/*
	Class: Activities Reports Context
	
	Allows the administrator to view the activity logs.
*/
class Reports extends Admin_Controller {
	
	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->auth->restrict('Site.Developer.View');
		$this->auth->restrict('Bonfire.Logs.View');
		
		Template::set('toolbar_title', 'Site Activities');
		
		Assets::add_js($this->load->view('reports/activities_js', null, true), 'inline');
		
		$this->load->helper('ui/ui');
		
		$this->lang->load('activities');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: index()
		
		Lists all log files and allows you to change the log_threshold.
	*/
	public function index() 
	{	
	
		Template::set('users', $this->user_model->find_all());
		Template::set('modules', module_list());
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: user()
		
		Shows the activites for the specified user.
		
		Parameter: 
			$activity_userid - the userid to search for
	*/
	public function user($activity_userid='') 
	{
		if (empty($activity_userid))
		{
			$activity_userid = (is_numeric($this->uri->segment(5))) ? $this->uri->segment(5) : $this->auth->user_id();
		}
		
		if (empty($activity_userid))
		{			
			Template::set_message('No log file provided.', 'error');
			Template::redirect(SITE_AREA .'/reports/activities');
		}
		
		Template::set('activity_user', $this->user_model->find($activity_userid));
		Template::set('activity_content', $this->activity_model->find_all_by('user_id',$activity_userid));
		Template::set_view('reports/view_user');
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: module()
		
		Shows the activites for the specified module.
		
		Parameter: 
			$activity_module	- the full name of the file to view (including extension).
	*/
	public function module($activity_module='') 
	{
		if (empty($module))
		{
			$activity_module = ($this->uri->segment(5) != '') ? $this->uri->segment(5) : 'activities';
		}
		
		if (empty($activity_module))
		{			
			Template::set_message('No log file provided.', 'error');
			Template::redirect(SITE_AREA .'/developer/activities');
		}

		Template::set('modules', module_list());				
		Template::set('activity_module', module_config($activity_module));
		Template::set('activity_content', $this->activity_model->find_all_by('module',$activity_module));
		Template::set_view('developer/view_module');
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: delete()
		
		Deletes the entries in the activity log for the specified area.
		
		Parameter: 
			$file	- the full name of the file to view (including extension).
	*/
	public function delete() 
	{
		$action = $this->uri->segment(5);
		$which  = $this->uri->segment(6);
		
		if (empty($action))
		{			
			Template::set_message('Delete action not specified', 'error');
			Template::redirect(SITE_AREA .'/developer/activities');
		}
				
		if (empty($which))
		{			
			Template::set_message('Specific user/module to delete not specified', 'error');
			Template::redirect(SITE_AREA .'/developer/activities');
		}
		
		if ($this->activity_model->delete_where(array($action => $which)))
		{
			Template::set_message('Deleted!','success');
			Template::redirect(SITE_AREA .'/developer/activities');			
		}
		else
		{
			Template::set_message('Something went tits up. Error : '.$this->activity_model->error, 'error');
			Template::redirect(SITE_AREA .'/developer/activities');
		}
		
		// what are you doing here?
		Template::set_message('You should not be here.', 'info');
		Template::redirect(SITE_AREA .'/developer/activities');
	}
	
	//--------------------------------------------------------------------
	
}