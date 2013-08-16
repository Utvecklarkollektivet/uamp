<?php
 /*
  *Template Name: UAMP Article Template
  */
get_header();

?>

<div id="primary">
	<div id="content" role="main">
	<?php
		$loop = new WP_Query(array("post_type" => "uamp_article"));

		while($loop->have_posts() ): $loop->the_post();
	?>
	<?php
		$article = UAMP_Article::find($post->ID);
		if( is_user_logged_in() || ( $article->visibility == "Public" && $article->isApproved() ) ):
	?>
	<article id="post-<?php the_ID() ?>" <?php post_class(); ?>>
	<header class="entry-header">

                <!-- Display featured image in right-aligned floating div -->
                <div style="float: right; margin: 10px">
                    <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                </div>

                <!-- Display Title and Author Name -->
                <strong>Title: </strong><?php the_title(); ?><br />
								<?php if(is_user_logged_in()): ?>
										<?php
											echo $article->visibility."<br/>";
											if($article->isApproved())
												echo "<strong>Approved</strong>";
											else
                        echo "<strong>Not Approved</strong>";
										?>

								<?php endif; ?>
            </header>

            <!-- Display movie review contents -->
            <div class="entry-content"><?php the_content(); ?></div>
        </article>


	<?php
			endif;
		endwhile;
	?>
