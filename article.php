<?php

class UAMP_Article {
	public function __construct(WP_Post $post) {
		$this->id = $post->ID;
		$this->postObj = $post;
    $this->visibility = get_post_meta($this->id, "uamp_visibility", true);
	}

  public static function find($id) {
		if($result = get_post($id))
			return new UAMP_Article($result);
		return false;
	}

	public function isApproved() {
		global $wpdb;
		$wpdb->uamp_approves = "{$wpdb->prefix}uamp_approves";
		$sql = $wpdb->prepare(" SELECT count(id) as count FROM {$wpdb->uamp_approves} WHERE post_id=%d",$this->id);
		$result = $wpdb->get_results($sql);
		$count = (int) $result[0]->count;
		return ($count > 2);
	}

	public function getApproval($userID) {
		global $wpdb;
		$wpdb->uamp_approves = "{$wpdb->prefix}uamp_approves";
		$sql = $wpdb->prepare("SELECT * FROM {$wpdb->uamp_approves} WHERE post_id=%d and user_id=%d LIMIT 1", array($this->id, $userID));
		$result = $wpdb->get_results($sql);
		if(count($result)) {
			return $result[0];
		}
		return false;
	}	

	public function approve($userID, $desc) {
		if(!$this->getApproval($userID)) {
			global $wpdb;
			$wpdb->uamp_approves = "{$wpdb->prefix}uamp_approves";
			$sql = $wpdb->prepare("INSERT INTO {$wpdb->uamp_approves} (post_id, user_id, description) VALUES(%d, %d, %s)", array($this->id, $userID, $desc));
			$wpdb->query($sql);
		}
	}

	public function disapprove($userID) {
		global $wpdb;
		$wpdb->uamp_approves = "{$wpdb->prefix}uamp_approves";
		$sql = $wpdb->prepare("DELETE FROM {$wpdb->uamp_approves} WHERE post_id=%d AND user_id=%d", array($this->id, $userID, $desc));
		$wpdb->query($sql);	
	}
}
