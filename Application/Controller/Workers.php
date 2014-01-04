<?php
	
	requires(
		'Random',
		'/Model/Worker',
		'/Model/WorkerGroup'
	);
	
	class Controller_Workers extends Controller_Application {
		
		public $requireAuthorization = true;
		
		public function index() {
			$this->groups = WorkerGroup::findAll();
			return $this->render('workers/index.tpl');
		}
		
		public function project() {
			/*if (Request::get('t')) {
				$this->View->assign('service', $this->State->getService(Request::get('t')));
			}*/
			
			//$this->View->assign('workers', $this->User->findAll(array('Ticket' => array('limit' => 1)/*, 'ServiceLogEntry'*/), 'role = ? AND last_seen IS NOT NULL AND AGE(last_seen) < ?', array('worker', '1 week'), 'name'));
			return $this->render('workers/project.tpl');
		}
		
		public function create_group() {
			$this->form();
			
			$group = new WorkerGroup($this->form->getValues());
			$group['token'] = Random::friendly(32);
			$group['secret'] = Random::friendly(32);
			
			if ($this->form->wasSubmitted() and $group->save()) {
				$this->flash('Worker group created');
				return $this->redirect('workers', 'index');
			}
			
			return $this->render('workers/group/edit.tpl');
		}
		
		public function edit_group(array $arguments) {
			if (!$this->group = WorkerGroup::find($arguments['id'])) {
				throw new EntryNotFoundException();
			}
			
			$this->form();
			
			if ($this->form->wasSubmitted()) {
				if ($this->form->getValue('create_secret')) {
					$this->group['secret'] = Random::friendly(32);
				}
				
				if ($this->group->save($this->form->getValues())) {
					if ($this->form->getValue('create_secret')) {
						$this->flashNow('New secret created');
					} else {
						$this->flash('Worker group updated');
						return $this->redirect('workers', 'index');
					}
				}
			}
			
			return $this->render('workers/group/edit.tpl');
		}
		
		public function delete_group(array $arguments) {
			if (!WorkerGroup::delete($arguments['id'])) {
				throw new EntryNotFoundException();
			}
			
			$this->flash('Worker group deleted');
			return $this->redirect('workers', 'index');
		}
		
	}
	
?>