<?php

class CategoriesController extends AppController {
	public $name = 'Categories';
	public $uses = array(
		'Category'
	);

	public function admin_index()
	{
		if (!isset($this->params->named['trash'])) {
			$this->paginate = array(
	            'order' => 'Category.created DESC',
	            'limit' => $this->pageLimit,
	            'conditions' => array(
	            	'Category.deleted_time' => '0000-00-00 00:00:00'
	            )
	        );
		} else {
			$this->paginate = array(
	            'order' => 'Category.created DESC',
	            'limit' => $this->pageLimit,
	            'conditions' => array(
	            	'Category.deleted_time !=' => '0000-00-00 00:00:00'
	            )
	        );
        }
        
		$this->request->data = $this->paginate('Category');
	}

	public function admin_add()
	{
        if ($this->request->is('post')) {
        		$this->request->data['Category']['slug'] = $this->slug($this->request->data['Category']['title']);
            if ($this->Category->save($this->request->data)) {
        		$old_article_file = VIEW_PATH . 'Articles' . DS . 'view.ctp';
            	$new_article_file = VIEW_PATH . 'Articles' . DS . $this->request->data['Category']['slug'] . '.ctp';
            	copy($old_article_file, $new_article_file);

        		$old_category_file = VIEW_PATH . 'Categories' . DS . 'view.ctp';
            	$new_category_file = VIEW_PATH . 'Categories' . DS . $this->request->data['Category']['slug'] . '.ctp';
            	copy($old_category_file, $new_category_file);

                $this->Session->setFlash('Your category has been added.', 'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your category.', 'flash_error');
            }
        } 
	}

	public function admin_edit($id = null)
	{
      	$this->Category->id = $id;

      	$this->paginate = array(
      		'conditions' => array(
      			'Article.deleted_time' => '0000-00-00 00:00:00',
      			'Article.category_id' => $id
      		),
      		'contain' => array(
      			'User'
      		),
      		'limit' => 7
      	);

      	$this->set('articles', $this->paginate('Article'));

      	$fields = $this->Category->Field->find('all', array(
      		'conditions' => array(
      			'Field.deleted_time' => '0000-00-00 00:00:00',
      			'Field.category_id' => $id
      		)
      	));

      	$this->set(compact('fields'));

	    if ($this->request->is('get')) {
	        $this->request->data = $this->Category->read();
	    } else {
	    	$this->request->data['Category']['slug'] = $this->slug($this->request->data['Category']['title']);

	        if ($this->Category->save($this->request->data)) {
	        	if ($this->request->data['Category']['title'] != $this->request->data['Category']['old_title']) {
	        		$old_slug = $this->slug($this->request->data['Category']['old_title']);

	        		$old_article_file = VIEW_PATH . 'Articles' . DS . $old_slug . '.ctp';
	            	$new_article_file = VIEW_PATH . 'Articles' . DS . $this->request->data['Category']['slug'] . '.ctp';
	            	rename($old_article_file, $new_article_file);

	        		$old_category_file = VIEW_PATH . 'Categories' . DS . $old_slug . '.ctp';
	            	$new_category_file = VIEW_PATH . 'Categories' . DS . $this->request->data['Category']['slug'] . '.ctp';
	            	rename($old_category_file, $new_category_file);
	            }

	            $this->Session->setFlash('Your category has been updated.', 'flash_success');
	            $this->redirect(array('action' => 'index'));
	        } else {
	            $this->Session->setFlash('Unable to update your category.', 'flash_error');
	        }
	    }
	}

	public function admin_delete($id = null, $title = null, $permanent = null)
	{
		if ($this->request->is('post')) {
	        throw new MethodNotAllowedException();
	    }

	    $this->Category->id = $id;

	    if (!empty($permanent)) {
	    	$delete = $this->Category->delete($id);

	    	$article_path = VIEW_PATH . 'Articles' . DS . $this->slug($title) . '.ctp';
	    	$categories_path = VIEW_PATH . 'Categories' . DS . $this->slug($title) . '.ctp';

	    	if (file_exists($article_path)) {
	    		unlink($article_path);
	    	}

	    	if (file_exists($categories_path)) {
	    		unlink($categories_path);
	    	}
	    } else {
	    	$delete = $this->Category->saveField('deleted_time', $this->Category->dateTime());
	    }

	    if ($delete) {
	        $this->Session->setFlash('The category `'.$title.'` has been deleted.', 'flash_success');
	    } else {
	    	$this->Session->setFlash('The category `'.$title.'` has NOT been deleted.', 'flash_error');
	    }

	    if (!empty($permanent)) {
	    	$this->redirect(array('action' => 'index', 'trash' => 1));
	    } else {
	    	$this->redirect(array('action' => 'index'));
	    }
	}

	public function admin_restore($id = null, $title = null)
	{
		if ($this->request->is('post')) {
	        throw new MethodNotAllowedException();
	    }

	    $this->Category->id = $id;

	    if ($this->Category->saveField('deleted_time', '0000-00-00 00:00:00')) {
	        $this->Session->setFlash('The category `'.$title.'` has been restored.', 'flash_success');
	        $this->redirect(array('action' => 'index'));
	    } else {
	    	$this->Session->setFlash('The category `'.$title.'` has NOT been restored.', 'flash_error');
	        $this->redirect(array('action' => 'index'));
	    }
	}

	public function view($slug = null)
	{
		$this->loadModel('SettingValue');
		
		if ($limit = $this->SettingValue->findByTitle('Number of Articles to list on Category Page')) {
			$limit = $limit['SettingValue']['data'];
		} else {
			$limit = 10;
		}

		$this->paginate = array(
			'order' => 'Article.created DESC',
			'conditions' => array(
				'Category.slug' => $slug,
				'Article.status' => 1,
				'Article.deleted_time' => '0000-00-00 00:00:00'
			),
			'contain' => array(
				'Category',
				'ArticleValue' => array(
					'Field',
					'File'
				)
				
			),
			'limit' => $limit
		);

		$this->request->data = $this->Category->Article->getAllRelatedArticles(
			$this->paginate('Article')
		);

		$this->set('title_for_layout', ucfirst($slug));

		if ($this->theme != "Default" && 
			file_exists(VIEW_PATH.'Themed/'.$this->theme.'/Categories/'.$slug.'.ctp') ||\
			file_exists(VIEW_PATH.'Categories/'.$slug.'.ctp')) {
			$this->render(implode('/', array($slug)));
		}
	}

	public function index()
	{
		$data = $this->Category->find('all', array(
			'conditions' => array(
				'Category.deleted_time' => '0000-00-00 00:00:00'
			),
			'fields' => array(
				'id', 'title', 'slug', 'created'
			)
		));

		$this->layout = '';
		$this->autoRender = false;

		echo json_encode($data);
	}
}