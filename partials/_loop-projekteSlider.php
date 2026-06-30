<div class="projects_slider">
    <?php
    $projekte_query = new WP_Query(array(
        'post_type' => 'projekte',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => 'publish',
    ));

    if ($projekte_query->have_posts()) {
        while ($projekte_query->have_posts()) {
            $projekte_query->the_post();

            if (!has_post_thumbnail()) {
                continue;
            }

            $uberschriftenlabel = get_field('uberschriftenlabel');
            $label_url = $uberschriftenlabel ? esc_url($uberschriftenlabel['url']) : '';
            ?>
            <div class="carousel-cell projekt-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('full', array(
                        'sizes' => '(max-width: 768px) 100vw, (min-width: 1024px) 25vw, 50vw',
                    )); ?>
                    <h2<?php echo $label_url ? ' style="background-image:url(\'' . $label_url . '\')"' : ''; ?>><?php the_title(); ?></h2>
                </a>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No projects found.</p>';
    }
    ?>
</div>
