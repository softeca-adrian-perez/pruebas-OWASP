<?php

class ReviewRequest extends AppModel{
    public $useTable = 'review_requests';

    public function add($booking_id, $aag_region_id, $request_data, $response_data) {
        $fields = array(
            'ReviewRequest' => array(
                'booking_id',
                'aag_region_id',
                'request_data',
                'response_data',
            )
        );
        $review_requests['ReviewRequest']['booking_id'] = $booking_id;
        $review_requests['ReviewRequest']['aag_region_id'] = $aag_region_id;
        $review_requests['ReviewRequest']['request_data'] = $request_data;
        $review_requests['ReviewRequest']['response_data'] = $response_data;

        $this->create();
        $review_requests_bd = $this->guardar($review_requests, $fields);
        if (!$review_requests_bd) {
            return false;
        }
        $this->commit();
        return $review_requests_bd;
    }

    public function getByBookingId($booking_id) {
        return $this->find('all', [
            'conditions' => ['booking_id' => $booking_id],
        ]);
    }

    public function hasRequestedReview($booking_id) {
        return $this->find('count', [
            'conditions' => ['booking_id' => $booking_id],
        ]) > 0;
    }

}
