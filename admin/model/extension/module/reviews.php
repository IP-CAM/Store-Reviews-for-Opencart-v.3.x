<?php

class ModelExtensionModuleReviews extends Model {

    public function add($data) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "reviews SET video = '" . $this->db->escape($data['video']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . (int) $data['sort_order'] . "', rating = '" . (int) $data['rating'] . "'");

        $reviews_id = $this->db->getLastId();

        foreach ($data['description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "reviews_description SET reviews_id = '" . (int) $reviews_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', subname = '" . $this->db->escape($value['subname']) . "', text = '" . $this->db->escape($value['text']) . "'");
        }

        return $reviews_id;
    }

    public function edit($data, $reviews_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "reviews SET video = '" . $this->db->escape($data['video']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . (int) $data['sort_order'] . "', rating = '" . (int) $data['rating'] . "' WHERE reviews_id = '" . (int) $reviews_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "reviews_description WHERE reviews_id = '" . (int) $reviews_id . "'");

        foreach ($data['description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "reviews_description SET reviews_id = '" . (int) $reviews_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', subname = '" . $this->db->escape($value['subname']) . "', text = '" . $this->db->escape($value['text']) . "'");
        }
    }

    public function delete($reviews_id) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "reviews WHERE reviews_id = '" . (int) $reviews_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "reviews_description WHERE reviews_id = '" . (int) $reviews_id . "'");
    }

    public function getReviews($data) {
        $sql = "SELECT * FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "reviews_description rd ON (r.reviews_id = rd.reviews_id) WHERE rd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if(!empty($data['filter_name'])) {
            $sql .= " AND rd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'rd.name',
            'a.sort_order'
        );

        if(isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY rd.name";
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
    }

    public function getReview($review_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "reviews r LEFT JOIN " . DB_PREFIX . "reviews_description rd ON (r.reviews_id = rd.reviews_id) WHERE r.reviews_id = '" . (int) $review_id . "' AND rd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getReviewDescription($review_id) {
        $reviews_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "reviews_description WHERE reviews_id = '" . (int) $review_id . "'");

        foreach ($query->rows as $result) {
            $reviews_data[$result['language_id']] = $result;
        }

        return $reviews_data;
    }

    public function install() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "reviews`; \n");

        $this->db->query(" CREATE TABLE `" . DB_PREFIX . "reviews` ( \n"
                . " `reviews_id` int(11) NOT NULL, \n"
                . " `rating` int(11) NOT NULL, \n"
                . " `image` varchar(255) NOT NULL, \n"
                . " `video` varchar(255) NOT NULL, \n"
                . " `sort_order` varchar(255) NOT NULL, \n"
                . " `text` text NOT NULL\n"
                . " ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query(" ALTER TABLE `" . DB_PREFIX . "reviews`  \n ADD PRIMARY KEY (`reviews_id`);");
        $this->db->query(" ALTER TABLE `" . DB_PREFIX . "reviews` MODIFY `reviews_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");



        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "reviews_description`; \n");

        $this->db->query(" CREATE TABLE `" . DB_PREFIX . "reviews_description` ( \n"
                . " `id` int(11) NOT NULL, \n"
                . " `language_id` int(11) NOT NULL, \n"
                . " `reviews_id` int(11) NOT NULL, \n"
                . " `name` varchar(255) NOT NULL, \n"
                . " `subname` varchar(255) NOT NULL, \n"
                . " `text` text NOT NULL\n"
                . " ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query(" ALTER TABLE `" . DB_PREFIX . "reviews_description`  \n ADD PRIMARY KEY (`id`);");
        $this->db->query(" ALTER TABLE `" . DB_PREFIX . "reviews_description` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
    }

}

?>