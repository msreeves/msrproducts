<?php function the_breadcrumb() {
    echo '<div class="breadcrumbs">';
    echo '<div class="container">';
    if (!is_home()) {
        echo '<span><a href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo "</a></span>";
        if (is_category() || is_single()) {
            echo '<span>';
            the_category(' </span><span> ');
            if (is_single()) {
                echo "</span><span>";
                the_title();
                echo '</span>';
            }
        } elseif (is_page()) {
            echo '<span>';
            echo the_title();
            echo '</span>';
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<span>Archive for "; the_time('F jS, Y'); echo'</span>';}
    elseif (is_month()) {echo"<span>Archive for "; the_time('F, Y'); echo'</span>';}
    elseif (is_year()) {echo"<span>Archive for "; the_time('Y'); echo'</span>';}
    elseif (is_author()) {echo"<span>Author Archive"; echo'</span>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>Blog Archives"; echo'</span>';}
    elseif (is_search()) {echo"<span>Search Results"; echo'</span>';}
    echo '</div>';
    echo '</div>';
}