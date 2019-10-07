<?php
/**
 * Created by PhpStorm.
 * User: stephan
 * Date: 28/08/2019
 * Time: 2:26 PM
 */
/**
 *Trigger this on Plugin unistal
 *
 *@package StephanPlugin
 *
 */

if(! defined('WP_UNINSTALL_PLUGIN')){//security to prevent other people outside wordpress to acces this file


    die;
}
//to clear databse stored data
/*

$books = get_posts(array('post_type'=> 'book','numberposts'=>-1));
foreach( $books as $book ) {
    wp_delete_post($book->ID,true);//false if you want to prevent delte from trash
}
*/

//Access the db via sql
//global $wpdb;

//$wpdb->query("DELETE FROM {$wpdb->prefix}_posts WHERE post_type= 'book'");//always use double quote
//if post_type using custom taxonomy
//$wpdb->query("DELETE FROM {$wpdb->prefix}_postmeta WEHRE post_id NOT IN (SELECT id FROM wp_posts");
//$wpdb->query("DELETE FROM {$wpdb->prefix}_term_retaionships WEHRE object_id NOT IN (SELECT id FROM wp_posts");

