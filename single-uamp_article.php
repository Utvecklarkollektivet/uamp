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
		if(is_user_logged_in())
			$userID = get_current_user_id();
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
							echo "<strong>Approved</strong><br/>";
						else
                        	echo "<strong>Not Approved</strong><br/>";

                    	$approval = $article->getApproval($userID);

                        if(!$approval && isset($_POST["uamp_approval_save"])) {
        					$desc = $_POST["uamp_approval_desc"];
        					$article->approve($userID, $desc);
                            $approval = $article->getApproval($userID);
            			}
                        if($approval && isset($_GET["disapprove"])) {
                            $article->disapprove($userID);
                            $approval = $article->getApproval($userID);
                        }  

                    	if($approval) {
                    		echo "You have approved this article.<br/>";
                    		echo "<blockqoute>".$approval->description."</blockqoute><br/>";
                            echo "<a href=\"?disapprove\">Disapprove</a><br/>";
                    	}
                    	else {
                		?>
                    		<form action="" method="post" />
                    			<strong>Motivation for approval</strong><br/>
                    			<textarea name="uamp_approval_desc"></textarea></br>
                				<input type="submit" name="uamp_approval_save" value="Approve"/>
                    		</form>
                		<?php
                    	}
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
