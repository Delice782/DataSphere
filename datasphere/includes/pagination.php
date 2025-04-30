<?php
/**
 * Pagination Helper Class
 * This file provides consistent pagination functionality across the application
 */

class Pagination {
    /**
     * Generate pagination for database queries
     *
     * @param object $conn The database connection object
     * @param string $count_sql The SQL query to count total records (should return a count with 'total' alias)
     * @param array $count_params Parameters for the count SQL query
     * @param string $count_types Types string for bind_param for count query (e.g. "ssi")
     * @param int $per_page Number of items per page
     * @param int $current_page Current page number (from GET parameter)
     * @return array Returns pagination data including current page, total pages, and SQL limit/offset
     */
    public static function paginate($conn, $count_sql, $count_params, $count_types, $per_page = 10, $current_page = 1) {
        // Validate current page
        $page = max(1, intval($current_page));
        
        // Get total records
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_records = $count_row['total'];
        $count_stmt->close();
        
        // Calculate total pages
        $total_pages = ceil($total_records / $per_page);
        
        // Adjust page if it exceeds total
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
        }
        
        // Calculate offset for SQL query
        $offset = ($page - 1) * $per_page;
        
        return [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'per_page' => $per_page,
            'total_records' => $total_records,
            'offset' => $offset,
            'limit' => $per_page
        ];
    }
    
    /**
     * Render pagination links HTML
     *
     * @param int $current_page Current page number
     * @param int $total_pages Total number of pages
     * @param string $url_pattern URL pattern for pagination links (use %d for page number)
     * @return string HTML for pagination links
     */
    public static function renderLinks($current_page, $total_pages, $url_pattern = '?page=%d') {
        if ($total_pages <= 1) {
            return ''; // No pagination needed
        }
        
        $html = '<div class="pagination">';
        
        // First and Previous links
        if ($current_page > 1) {
            $first_url = sprintf($url_pattern, 1);
            $prev_url = sprintf($url_pattern, $current_page - 1);
            $html .= '<a href="' . $first_url . '" class="page-link first">&laquo; First</a>';
            $html .= '<a href="' . $prev_url . '" class="page-link prev">&lsaquo; Prev</a>';
        }
        
        // Page info
        $html .= '<span class="page-info">Page ' . $current_page . ' of ' . $total_pages . '</span>';
        
        // Next and Last links
        if ($current_page < $total_pages) {
            $next_url = sprintf($url_pattern, $current_page + 1);
            $last_url = sprintf($url_pattern, $total_pages);
            $html .= '<a href="' . $next_url . '" class="page-link next">Next &rsaquo;</a>';
            $html .= '<a href="' . $last_url . '" class="page-link last">Last &raquo;</a>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}