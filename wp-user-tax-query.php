<?php

namespace Tomodomo\Plugin\UserTaxQuery;

add_action('pre_get_users', __NAMESPACE__ . '\\modifyQuery');

/**
 * Alters the user query to support a taxonomy query
 * by manually fetching potential IDs and using them
 *
 * @param \WP_User_Query $query
 *
 * @return void
 */
function modifyQuery(\WP_User_Query $query)
{
    $taxQuery = $query->query_vars['tax_query'] ?? false;

    // Return early if there is no tax query
    if (!$taxQuery) {
        return;
    }

    // Loop through the queries in the taxonomy
    foreach ($taxQuery as $taxGroup) {
        // Get IDs for each term
        $termQuery = new WP_Term_Query([
            'taxonomy'   => $taxGroup['taxonomy'],
            'name'       => $taxGroup['terms'],
            'hide_empty' => false,
            'fields'     => 'tt_ids',
        ]);

        // Get the user IDs in each term for the given taxonomy
        foreach ($termQuery->terms as $termId) {
            $userIdSets[] = get_objects_in_term($termId, $taxGroup['taxonomy']);
        }
    }

    // Allow continuing to use `include` as a query var in user queries
    // by adding it as one of the sets to intersect. Any IDs that are
    // not already in this set will be excluded (because that's how
    // array_intersect works)
    if (!empty($query->query_vars['include'])) {
        $userIdSets[] = $query->query_vars['include'];
    }

    // Intersect all the arrays to only get user IDs that are in all sets
    $userIds = (count($userIdSets) > 1)
        ? call_user_func_array('array_intersect', $userIdSets)
        : $userIdSets[0];

    // If set and empty, there are no results so force no results
    $query->query_vars['include'] = empty($userIds) ? [PHP_INT_MAX] : $userIds;

    // Unset the taxQuery from the query_vars
    unset($query->query_vars['tax_query']);

    return;
}
