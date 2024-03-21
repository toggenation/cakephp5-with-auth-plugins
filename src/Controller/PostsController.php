<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Post;
use Cake\Form\Form;
use Cake\Http\Cookie\Cookie;
use stdClass;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 */
class PostsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Posts->find()
            ->contain(['Users']);
        $posts = $this->paginate($query);

        $this->set(compact('posts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $post = $this->Posts->get($id, contain: ['Users']);
        $this->set(compact('post'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEmptyEntity();

        $this->Authorization->authorize($post);

        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());

            $post->user_id = $this->request->getAttribute('identity')->getIdentifier();

            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $users = $this->Posts->Users->find('list', limit: 200)->all();
        $this->set(compact('post', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, contain: []);

        $this->Authorization->authorize($post);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity(
                $post,
                $this->request->getData(),
                [
                    // Added: Disable modification of user_id.
                    'accessibleFields' => ['user_id' => false]
                ]
            );

            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $users = $this->Posts->Users->find('list', limit: 200)->all();
        $this->set(compact('post', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);

        $this->Authorization->authorize($post);

        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function ajax()
    {
        $this->Authorization->skipAuthorization();

        if ($this->request->is('ajax')) {

            $data = [
                'email' => $this->getRequest()
                    ->getAttribute('identity')
                    ->getOriginalData()['email'],
                'method' => $this->request->getMethod()
            ];

            $this->viewBuilder()
                ->setClassName('Json')
                ->setOption('serialize', 'data');

            $this->set(compact('data'));
        }
    }

    public function testRedirect()
    {
        $this->Authorization->authorize($this->Posts->newEmptyEntity());
    }

    public function unauth()
    {
    }

    public function cookie()
    {
        $this->Authorization->authorize($this->Posts->newEmptyEntity());

        if ($this->request->is("POST")) {

            $data = $this->request->getData();

            $cookie = new Cookie('form', $data);

            if ($data['clear']) {
                $this->setResponse($this->getResponse()->withExpiredCookie($cookie));
            } else {
                $this->setResponse($this->getResponse()->withCookie($cookie));
            }

            return $this->redirect(['action' => 'cookie']);
        }

        $formValues = [];

        if ($this->getRequest()->getCookieCollection()->has('form')) {
            $formValues = $this->getRequest()->getCookieCollection()->get('form')->getValue();
        }

        $form = (new Form())->setData($formValues);

        $this->set('form', $form);
    }
}
