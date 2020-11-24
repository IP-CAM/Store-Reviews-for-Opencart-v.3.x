<?php

class ControllerExtensionModuleReviews extends Controller {

    public function index() {
        $this->load->language('extension/module/reviews');

        $this->load->model('setting/setting');
        $this->document->setTitle($this->language->get('heading_title'));

        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_reviews', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
        }
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/reviews', '&user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/reviews', 'user_token=' . $this->session->data['user_token'], true);
        $data['add']    = $this->url->link('extension/module/reviews/add', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if(isset($this->request->post['module_reviews_status'])) {
            $data['module_reviews_status'] = $this->request->post['module_reviews_status'];
        } else {
            $data['module_reviews_status'] = $this->config->get('module_reviews_status');
        }
        if(isset($this->request->post['module_reviews'])) {
            $data['module_reviews'] = $this->request->post['module_reviews'];
        } else {
            $data['module_reviews'] = $this->config->get('module_reviews');
        }

        $this->load->model('extension/module/reviews');
        $this->load->model('tool/image');

        $results = $this->model_extension_module_reviews->getReviews([]);

        foreach ($results as $result) {

            if(is_file(DIR_IMAGE . $result['image'])) {
                $thumb = $this->model_tool_image->resize($result['image'], 50, 50);
            } else {
                $thumb = $this->model_tool_image->resize('no_image.png', 50, 50);
            }
            $data['reviews'][] = array_merge([
                'thumb'  => $thumb,
                'edit'   => $this->url->link('extension/module/reviews/edit', 'user_token=' . $this->session->data['user_token'] . '&reviews_id=' . $result['reviews_id'], true),
                'remove' => $this->url->link('extension/module/reviews/remove', 'user_token=' . $this->session->data['user_token'] . '&reviews_id=' . $result['reviews_id'], true),
                    ], $result);
        }


        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/reviews', $data));
    }

    public function install() {
        $this->load->model('extension/module/reviews');
        $this->model_extension_module_reviews->install();
    }

    public function add() {
 
        $this->load->language('extension/module/reviews');
        $this->load->model('extension/module/reviews');
        $this->document->setTitle($this->language->get('text_add'));

        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_module_reviews->add($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/reviews', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }


        $data                = [
            'name'          => '',
            'subname'       => '',
            'text'          => '',
            'rating'        => '5',
            'image'         => 'no_image.png',
            'video'         => '',
            'heading_title' => $this->language->get('text_add'),
            'text_edit'     => $this->language->get('text_add'),
        ];
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/reviews', '&user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_add'),
            'href' => $this->url->link('extension/module/reviews/add', '&user_token=' . $this->session->data['user_token'], true)
        );
        $data['action']        = $this->url->link('extension/module/reviews/add', 'user_token=' . $this->session->data['user_token'], true);



        $this->getForm($data);
    }

    public function edit() {
        $this->load->language('extension/module/reviews');
        $this->load->model('extension/module/reviews');
        $this->document->setTitle($this->language->get('text_edit'));

        $review_id = $this->request->get['reviews_id'];

        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_module_reviews->edit($this->request->post, $review_id);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/reviews', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/reviews', '&user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_add'),
            'href' => $this->url->link('extension/module/reviews/edit', '&user_token=' . $this->session->data['user_token'], true)
        );
 

        $review_info = $this->model_extension_module_reviews->getReview($review_id);


        $data['action'] = $this->url->link('extension/module/reviews/edit', 'user_token=' . $this->session->data['user_token'] . '&reviews_id=' . $review_id, true);


        if($review_info) {
            $data['image']      = $review_info['image'];
            $data['video']      = $review_info['video'];
            $data['sort_order'] = $review_info['sort_order'];
            $data['rating'] = $review_info['rating'];

            $data['description'] = $this->model_extension_module_reviews->getReviewDescription($review_id);
        }


        $this->getForm($data);
    }

    private function getForm($data) {

        $data['cancel']      = $this->url->link('extension/module/reviews', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $this->load->model('localisation/language');
        $data['languages']   = $this->model_localisation_language->getLanguages();
        $data['language_id'] = $this->config->get('config_language_id');
        if(is_file(DIR_IMAGE . $data['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($data['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/reviews_form', $data));
    }

    public function validate() {

        return true;
    }

}
