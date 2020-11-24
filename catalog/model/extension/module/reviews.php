<?php
 
class ModelExtensionModuleReviews extends Model {
 

    public function getReview($reviews_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "reviews_description rd ON (r.reviews_id = rd.reviews_id)  WHERE r.reviews_id = '" . (int) $reviews_id . "' AND rd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getReviews($data = array()) {
        if($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "reviews_description rd ON (r.reviews_id = rd.reviews_id)   WHERE  rd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

            $sort_data = array(
                'name',
                'sort_order'
            );

            if(isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY sort_order";
            }

            if(isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if(isset($data['start']) || isset($data['limit'])) {
                if($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
//            $review_data = $this->cache->get('review.' . (int) $this->config->get('config_store_id') . '.' . (int) $this->config->get('config_language_id'));

//            if(!$review_data) {

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "reviews_description rd ON (r.reviews_id = rd.reviews_id) WHERE  rd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name");

                $review_data = $query->rows;
 

                $this->cache->set('review.' . (int) $this->config->get('config_store_id') . '.' . (int) $this->config->get('config_language_id'), $review_data);
//            }
            return $review_data;
        }
    }

}
