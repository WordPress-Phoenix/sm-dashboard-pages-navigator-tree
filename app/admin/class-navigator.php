<?php
/**
 * Navigator
 *
 * @package    WordPress
 * @subpackage Sm_dashboard_pages_navigator_tree
 */

namespace SM\Pages_Navigator\Admin;

class Navigator {
	/**
	 * Initialize by registering dashboard widgets into core.
	 */
	public static function register_widgets() {
		wp_add_dashboard_widget( 'sm-pagetree', 'Page Navigator', [ get_called_class(), 'list_sm_pagetree' ] );
	}

	public static function list_sm_pagetree() {
		// get and combine child pages and revision s
		$memstart2 = \memory_get_usage();
		$output    = '';
		$output    .= '<div id="smPagetree"><p><a href="#" id="expand">Expand All</a> | <a href="#" id="collapse">Collapse All</a></p>' . static::get_sm_pagetreee( 0, 0 ) . '</div>' . PHP_EOL;
		$memend2   = \memory_get_usage();
		$mem_usage = (float) ( $memend2 - $memstart2 );
		if(defined('WP_DEBUG') && 'true' === WP_DEBUG ) {
			$output    .= '<span id="sm_nav_memory_used">Memory Used: ' . static::meg( $mem_usage ) . ' of ' . static::meg( $memend2 ) . '</span>';
		}
		echo $output;
	}

	/**
	 * BUILD PAGE TREE - PRIMARY FUNCTION
	 *
	 * @param $parentId
	 * @param $lvl
	 *
	 * @return string
	 */
	public static function get_sm_pagetreee( $parentId, $lvl ) {
		$output        = $childCount = '';
		$pages         = get_pages( [
			'child_of'    => $parentId,
			'parent'      => $parentId,
			'post_type'   => 'page',
			'post_status' => [ 'publish', 'pending', 'draft', 'private' ],
		] );
		$postRevisions = get_posts( [
			'post_parent' => $parentId,
			'post_type'   => 'revision',
			'post_status' => 'pending',
		] );
		$pages         = array_merge( (array) $postRevisions, (array) $pages );

		if ( $pages ) {
			if ( $lvl < 1 ) {
				$output .= "<ul id=\"simpletree\" class='level" . $lvl ++ . "'>" . PHP_EOL;
			} else {
				$output .= "<ul class='treebranch level" . $lvl ++ . "'>" . PHP_EOL;
			}

			// loop through pages and add them to treebranch
			foreach ( $pages as $page ) {
				$children = [];

				//if branch has children branches, create a new treebranch, otherwise create a treeleaf
				if ( $childCount > 0 ) {
					$output .= "<li id=\"$page->ID\" class=\"treebranch\">" . PHP_EOL;
				} else {
					$output .= "<li id=\"$page->ID\" class=\"treeleaf\">" . PHP_EOL;
				}

				//begin setting up treeleaf leaflet content
				$output .= "<div class='treeleaflet'>" . PHP_EOL;
				$output .= "<span class=\"leafname\">$page->post_title</span>";

				// show child count if there are children
				if ( $childCount > 0 ) {
					$output .= '<span class="childCount"> (' . $childCount . ')</span> ';
				}

				// if its not a revision
				if ( $page->post_type != 'revision' ) {

					// display status
					$output .= " <span class=\"status $page->post_status\">$page->post_status</span>";

					// show excluded if it is
					if ( get_post_meta( $page->ID, '_sm_sitemap_exclude_completely', true ) == 'yes' && $page->post_status == 'publish' ) {
						$output .= " <span class=\"status excluded\">no sitemap</span>";
					}
					$output .= "<span class=\"action-links\">  - ";

					//view link
					if ( empty( $pageTemplate ) || $pageTemplate != 'tpl-404.php' ) {
						$output .= "<a class=\"viewPage\" href=\"" . get_permalink( $page->ID ) . "\">view</a> " . PHP_EOL;
					} else {
						$output .= "Placeholder Page ";
					}

					$revAuthorID = $page->post_author;
					// if current user not revision editor do not allow to make changes
					if ( $revAuthorID == $GLOBALS['current_user']->ID && ! current_user_can( 'edit_others_revisions' ) ) {
					}
					$post_type_object = get_post_type_object( $page->post_type );

					if ( current_user_can( 'edit_others_pages' ) || ( $revAuthorID == $GLOBALS['current_user']->ID && current_user_can( 'edit_pages' ) ) ) {
						$output .= "| <a class=\"editPage\" href=\"" . admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $page->ID ) ) . "\">edit</a> " . PHP_EOL;
					}

					$output .= "</span>";
					$output .= "</div>" . PHP_EOL;

				}// if($page->post_type != 'revision')

				// if its a revision
				elseif ( $page->post_type == 'revision' ) {

					//display revision status
					$output .= " <span class=\"status $page->post_type\">$page->post_type</span>";
					$output .= "<span class=\"action-links\"> - ";
					$output .= "<a class=\"viewPage\" href=\"/?p=$page->ID&amp;post_type=revision&amp;preview=true\">preview</a>" . PHP_EOL;

					$revAuthorID = $page->post_author;

					$current_user  = wp_get_current_user();
					$currentUserID = $current_user->ID;

					// if current user not revision editor do not allow to make changes
					if ( $revAuthorID == $currentUserID && current_user_can( 'edit_others_revisions' ) ) {
						$output .= " | <a class=\"editPage\" href=\"/wp-admin/admin.php?page=rvy-revisions&amp;revision=$page->ID&amp;action=edit\">edit</a>" . PHP_EOL;
					}

					$output .= "</span>";
				}

				// recall function to see if child pages have children
				unset( $pages );
				$output .= static::get_sm_pagetreee( $page->ID, $lvl );
				$output .= "</li>" . PHP_EOL;
			}
			$output .= "</ul>" . PHP_EOL;
		}

		return $output;
	}

	/**
	 * Converts bytes to Megabtypes
	 *
	 * @param $mem_usage
	 *
	 * @return string
	 */
	public static function meg( $mem_usage ) {
		$output = '';
		if ( $mem_usage < 1024 ) {
			$output .= $mem_usage . " bytes";
		} elseif ( $mem_usage < 1048576 ) {
			$output .= round( $mem_usage / 1024, 2 ) . " kilobytes";
		} else {
			$output .= round( $mem_usage / 1048576, 2 ) . " megabytes";
		}

		return $output;
	}
}