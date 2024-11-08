        <div class="container">
            <div class="row">
                <?php
                foreach ($posts_data as $post_data) {
                    $post_url = site_url() . '/' . $term_slug . '/' . $post_data->post_name;
                    $thumbnail_id = get_post_meta($post_data->ID, '_thumbnail_id', true);
                    $image = wp_get_attachment_image_src($thumbnail_id, array(500, 500));
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card fixed-height-card">
                            <img class="card-img-top fixed-size-image" src="<?php echo $image[0]; ?>" alt="Card image cap">
                            <div class="card-body">

                                <h5 class="card-title text-dark"><a class="text-decoration-none " href="<?php echo $post_url; ?>"><?php echo $post_data->post_title ?></a></h5>
                                <p class="card-text text-justify"><?php echo $post_data->post_content; ?> </p>

                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a href="<?php echo $post_url; ?>" class="btn btn-primary ">View</a></div>
                            </div>
                            <div class="card-footer text-muted">
                                <span class="text-danger fw-bold">Published :</span> <?php echo $post_data->post_date; ?>
                                <br>
                                <span class="text-danger fw-bold">Author Name :</span> <?php echo get_post_meta($post_data->ID, 'author_name', true); ?>
                                <br>
                                <span class="text-danger fw-bold">Publisher Name :</span> <?php echo  get_post_meta($post_data->ID, 'publisher_name', true); ?>
                                <br>
                                <span class="text-danger fw-bold">Book Price:</span> <?php echo '₹ ' . get_post_meta($post_data->ID, 'book_price', true) . ' /-'; ?>

                            </div>

                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>