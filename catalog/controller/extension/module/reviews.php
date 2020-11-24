<?php

class ControllerExtensionModuleReviews extends Controller {

    public function index() {
        static $module = 0;
        $this->load->model('tool/image');
        $this->load->model('extension/module/reviews');

        $this->document->addStyle('catalog/view/javascript/jquery/slick/slick.css');
        $this->document->addScript('catalog/view/javascript/jquery/slick/slick.js', 'footer');

        $module_reviews = $this->config->get('module_reviews');

        $data    = [
            'title'  => html_entity_decode($module_reviews['title']),
            'module' => $module++
        ];
        $results = $this->model_extension_module_reviews->getReviews([
            'order' => 'ASC',
            'sort'  => 'sort_order',
        ]);


        foreach ($results as $result) {
            if(is_file(DIR_IMAGE . $result['image'])) {
                $thumb = $this->model_tool_image->resize($result['image'], $module_reviews['preview_w'], $module_reviews['preview_h'], 'w');
            } else {
                $thumb = $this->model_tool_image->resize('no_image.png', $module_reviews['preview_w'], $module_reviews['preview_h'], 'w');
            }
            $data['reviews'][] = array_merge($result, [
                'thumb'        => $thumb,
                'text'         => html_entity_decode($result['text']),
                'href'         => $this->url->link('extension/module/reviews/info', 'reviews_id=' . $result['reviews_id']),
                'rating_width' => 100 / 5 * $result['rating'],
            ]);
        }


        return $this->load->view('extension/module/reviews', $data);
    }

    public function info() {

        $this->load->model('tool/image');
        $this->load->model('extension/module/reviews');
        if(isset($this->request->get['reviews_id']) && $this->request->get['reviews_id']) {
            $review_id = $this->request->get['reviews_id'];
        } else {
            $results   = $this->model_extension_module_reviews->getReviews();
            $one       = array_shift($results);
            $review_id = $one['reviews_id'];
        }


        $module_reviews = $this->config->get('module_reviews');

        $review_info = $this->model_extension_module_reviews->getReview($review_id);


        if($review_info) {
            $this->document->setTitle($review_info['name']);
            if(is_file(DIR_IMAGE . $review_info['image'])) {
                $thumb = $this->model_tool_image->resize($review_info['image'], $module_reviews['full_w'], $module_reviews['full_h'], 'w');
            } else {
                $thumb = $this->model_tool_image->resize('no_image.png', $module_reviews['full_w'], $module_reviews['full_h'], 'w');
            }


            $data = [
                'name'         => html_entity_decode($review_info['name']),
                'subname'      => html_entity_decode($review_info['subname']),
                'text'         => html_entity_decode($review_info['text']),
                'href'         => $this->url->link('extension/module/review', '', true),
                'video'        => $review_info['video'],
                'rating'       => $review_info['rating'],
                'thumb'        => $thumb,
                'rating_width' => 100 / 5 * $review_info['rating'],
            ];
        }





        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text' => 'Отзывы',
            'href' => $this->url->link('extension/module/reviews/info'),
        );

        $data['column_left']    = $this->load->controller('common/column_left');
        $data['column_right']   = $this->load->controller('common/column_right');
        $data['content_top']    = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/reviews_info', $data));
    }

}
