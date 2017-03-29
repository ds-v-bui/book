<?php
App::uses('AppController', 'Controller');
/**
 * Writers Controller
 *
 * @property Writer $Writer
 */
class WritersController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Writer->recursive = 0;
		$this->set('writers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($slug = null) {
		$options = array(
			'conditions' => array('Writer.slug' => $slug),
			'recursive' => -1
			);
		$writer = $this->Writer->find('first', $options);
		if (empty($writer)) {
			throw new NotFoundException(__('Không tìm thấy tác giả này.'));
		}
		$this->set('writer', $writer);
		//phân trang dữ liệu books
		$this->paginate = array(
			'fields' => array('id','title','slug','image','sale_price'),
			'order' => array('created'=>'desc'),
			'limit' => 5,
			'contain' => array(
				'Writer' => array('name','slug')
				),
			'joins'=> array(
				array(
					'table' => 'books_writers',
					'alias' => 'BookWriter',
					'conditions' => 'BookWriter.book_id = Book.id'
					),
				array(
					'table' => 'writers',
					'alias' => 'Writer',
					'conditions' => 'BookWriter.writer_id = Writer.id'
					)
				),
			'conditions' => array(
				'published' => 1,
				'Writer.slug' => $slug
				),
			'paramType' => 'querystring'
			);
		$books = $this->paginate('Book');
		$this->set('books',$books);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Writer->create();
			if ($this->Writer->save($this->request->data)) {
				$this->Session->setFlash(__('The writer has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The writer could not be saved. Please, try again.'));
			}
		}
		$books = $this->Writer->Book->find('list');
		$this->set(compact('books'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Writer->exists($id)) {
			throw new NotFoundException(__('Invalid writer'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Writer->save($this->request->data)) {
				$this->Session->setFlash(__('The writer has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The writer could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Writer.' . $this->Writer->primaryKey => $id));
			$this->request->data = $this->Writer->find('first', $options);
		}
		$books = $this->Writer->Book->find('list');
		$this->set(compact('books'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Writer->id = $id;
		if (!$this->Writer->exists()) {
			throw new NotFoundException(__('Invalid writer'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Writer->delete()) {
			$this->Session->setFlash(__('Writer deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Writer was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
