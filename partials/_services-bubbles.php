<?php
$args = array(
    'post_type' => 'services',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'post_status' => 'publish',
);
$services_query = new WP_Query($args);
?>

<div id="services_bubbles">
  <div id="canvas-container">
    <canvas id="canvas"></canvas>
  </div>
</div>

<ul id="services_list">
  <?php
  if ($services_query->have_posts()) {
      while ($services_query->have_posts()) {
          $services_query->the_post();
          $ui_color = uncut_esc_color(get_field('ui_color'));
          echo '<li style="background:' . esc_attr($ui_color) . '">' . esc_html(get_the_title()) . '</li>';
      }
      wp_reset_postdata();
  }
  ?>
</ul>
<a id="service_bubbles_link" href="<?php echo esc_url(uncut_get_services_page_url()); ?>">Services im Detail</a>
