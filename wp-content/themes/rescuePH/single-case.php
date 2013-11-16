<?php
/*
This is the custom post type post template.
If you edit the post type name, you've got
to change the name of this template to
reflect that name change.

i.e. if your custom post type is called
register_post_type( 'bookmarks',
then your single template should be
single-bookmarks.php

*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap clearfix">
					<!-- Changed to full width -->
						<div id="main" class="twelvecol first clearfix" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
								<header class="article-header">
									<div class="single-title custom-post-type-title">											<div class="date">
										<a href="<?php the_permalink(); ?>"><?php the_time(get_option('date_format')); ?></a>
									<?php if($status = get_field('status')) { ?>
										<?php
										foreach ($status as $term) {
										    $term_link = get_term_link( $term, 'status' );
										    if( is_wp_error( $term_link ) )
										        continue;
										    echo ' - <a href="' . $term_link . '">' . $term->name . '</a>';
										}
										?>
									<?php } ?>
									</div>
									<h1><?php the_title(); ?></h1>
                  <?php if(has_post_thumbnail()) { ?>
                    <?php the_post_thumbnail(array(150,150)); ?>
                  <?php } else { ?>
                    <img src="http://placehold.it/150&text=No+Image" alt="">
                  <?php } ?>
									<h2><?php if($summary = get_field('summary')) { ?>
										<?php echo $summary; ?>
									<?php } ?></h2>
									</div>
								</header>

								<section class="case-details">
									<div class="case-status">
										<?php $priority = get_field('priority'); ?>
										<div class="priority <?php echo $priority[0]->slug; ?>">
											<?php echo $priority[0]->name; ?>
										</div>

										<?php $status = get_field('status'); ?>
										<?php $t = get_the_title(); ?>
										<div class="status <?php echo $status[0]->slug; ?>">
											<?php echo $status[0]->name; ?>
										</div>
										<div class="map" id="loc" style="height:400px; width:400px">
										</div>
										<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
											<script>
												function createPinIcon(pinColor) {
													return new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
												        new google.maps.Size(21, 34),
												        new google.maps.Point(0,0),
												        new google.maps.Point(10, 34));
												}

											    var map;
											    var myLatLang = new google.maps.LatLng( <?php the_field('wp_gp_latitude'); ?> , <?php the_field('wp_gp_longitude'); ?>);
											    var myPinColor = "FE7569";
												var nearbyPinColor = "FEB869";

											    function initialize() {
											        var mapOptions = {
											            zoom: 6,
											            center: myLatLang,
											            mapTypeId: google.maps.MapTypeId.ROADMAP
											        };
											        map = new google.maps.Map(document.getElementById('loc'),
											        mapOptions);

											        var infoWindow = new google.maps.InfoWindow({
														maxWidth: 200
													});
											        
											        var marker = new google.maps.Marker({
											            position: myLatLang,
											            map: map,
											            icon: createPinIcon(myPinColor),
											            title: <?php echo '"'.$t.'"'?>,
											        });
											        <?php
											        $infoContent = "<div class='infoWindow'><h1>" . get_the_title() . '</h1>';
											        if ($summary) {
											        	$infoContent .= "<div>{$summary}</div>";
											        }
											        $infoContent .= '</div>';
													?>
									                google.maps.event.addListener(marker, 'click', function() {
									                	infoWindow.setContent("<?php echo $infoContent ?>");
									                	infoWindow.open(map,this);
									        		});
											        
											        <?php
											        ob_start();
													$query = new WP_GeoQuery(array(
													  'latitude' => the_field('wp_gp_latitude'), // Post's Latitude (optional)
													  'longitude' => the_field('wp_gp_longitude'),// Post's Longitude (optional)
													  'radius' => 50, // Radius to select for in miles (optional)
													  'posts_per_page' => 50, // Any regular options available via WP_Query
													)); 
													$throwAway = ob_get_contents();
													ob_end_clean();
													?>
													var nearbyIcon = createPinIcon(nearbyPinColor);
													<?php 
													$i =0; 
													$infoContent = array();
													foreach($query->posts as $near) :?>
													
													<?php if(get_post_meta( $near->ID, "wp_gp_latitude", true ) && $near->post_title != $t) :?>
														<?php
														$permalink = get_permalink($near->ID);
														$customFields = get_post_custom($near->ID);
														$infoContent[$i] = "<div class='infoWindow'><h1><a href='$permalink'>{$near->post_title}</a></h1>";
														if (isset($customFields['summary'][0])) {
															$infoContent[$i] .= '<div>' . $customFields['summary'][0] . '</div>';
														}
														$infoContent[$i] .= '</div>';
														?>
														var marker<?php echo $i; ?> = new google.maps.Marker({
															position: 	new google.maps.LatLng( <?php  echo get_post_meta( $near->ID, "wp_gp_latitude", true )?> , <?php echo get_post_meta( $near->ID, "wp_gp_longitude", true ); ?>),
															map : map,
															icon: nearbyIcon,
															title: <?php echo '"'.$near->post_title.'"'; ?>
														});

												        google.maps.event.addListener(marker<?php echo $i; ?>, 'click', function() {
												        	infoWindow.setContent("<?php echo $infoContent[$i] ?>");
												        	infoWindow.open(map,this);
														});
													<?php $i=$i+1; ?>
													<?php endif ?>
													<?php endforeach ?>
											    };

											    google.maps.event.addDomListener(window, 'load', initialize);

											</script>

											<?php
											// foreach ($query->posts as $post) {
											// 	var_dump(get_post_custom($post->ID));
											// }
											// var_dump($query->posts);
											// var_dumpget_post_custom
											// die;
											#?>
									</div>
								</section>

								<section class="entry-content clearfix">
									<div class="summary">
									<?php if($priority = get_field('priority')) { ?>
										<h2>Priority</h2>
										<?php
										echo '<ul>';
										foreach ($priority as $term) {
										    $term_link = get_term_link( $term, 'priority' );
										    if( is_wp_error( $term_link ) )
										        continue;
										    echo '<li><a href="' . $term_link . '">' . $term->name . '</a></li>';
										}
										echo '</ul>'; ?>
									<?php } ?>
									<?php if($type = get_field('type')) { ?>
										<h2>Type</h2>
										<?php
										echo '<ul>';
										foreach ($type as $term) {
										    $term_link = get_term_link( $term, 'type' );
										    if( is_wp_error( $term_link ) )
										        continue;
										    echo '<li><a href="' . $term_link . '">' . $term->name . '</a></li>';
										}
										echo '</ul>'; ?>
									<?php } ?>


									<h1>Request made by</h1>
									<?php if($requester_name = get_field('requester_name')) { ?>
										<h2>Name</h2>
										<?php echo $requester_name; ?>
									<?php } ?>
									<?php if($requester_contact_info = get_field('requester_contact_info')) { ?>
										<h2>Contact Information</h2>
										<?php echo $requester_contact_info; ?>
									<?php } ?>
									<?php if($request_source = get_field('request_source')) { ?>
										<h2>Request Source</h2>
										<?php echo $request_source; ?>
									<?php } ?>


									<?php if($type[0]->slug == 'rescue') { ?>
										<h1>Assistance Request Information</h1>
										<?php if($name = get_field('name')) { ?>
											<h2>Name</h2>
											<?php echo $name; ?>
										<?php } ?>
										<?php if($contact = get_field('contact')) { ?>
											<h2>Contact Information</h2>
											<?php echo $contact; ?>
										<?php } ?>
										<?php if($rescue_location = get_field('request_location')) { ?>
											<h2>Location</h2>
											<?php echo $rescue_location['address']; ?>
											<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
											<script>
											    var map;
											    var myLatLang = new google.maps.LatLng( <?php echo $rescue_location['coordinates']; ?>);
											    function initialize() {
											        var mapOptions = {
											            zoom: 6,
											            center: myLatLang,
											            mapTypeId: google.maps.MapTypeId.ROADMAP
											        };
											        map = new google.maps.Map(document.getElementById('map-canvas'),
											        mapOptions);
											        var marker = new google.maps.Marker({
											            position: myLatLang,
											            map: map,
											            title:"Rescue Location"
											        });
											    };
											    google.maps.event.addDomListener(window, 'load', initialize);
											</script>
											<div id="map-canvas"></div>
										<?php } // End Rescue location ?>
									<?php } // End check for rescue type ?>
									
									<?php if($type[0]->slug == 'tracing') { ?>
										<h1>Tracing Request Information</h1>
										<?php if($tracing_name = get_field('tracing_name')) { ?>
											<h2>Name of Missing Person</h2>
											<?php echo $tracing_name; ?>
										<?php } ?>
										<?php if($tracing_location = get_field('tracing_location')) { ?>
											<h2>Last Seen</h2>
											<?php echo $tracing_location['address']; ?>
											<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
											<script>
											    var map;
											    var myLatLang = new google.maps.LatLng( <?php echo $tracing_location['coordinates']; ?>);
											    function initialize() {
											        var mapOptions = {
											            zoom: 6,
											            center: myLatLang,
											            mapTypeId: google.maps.MapTypeId.ROADMAP
											        };
											        map = new google.maps.Map(document.getElementById('map-canvas'),
											        mapOptions);
											        var marker = new google.maps.Marker({
											            position: myLatLang,
											            map: map,
											            title:"Last Seen Location"
											        });
											    };
											    google.maps.event.addDomListener(window, 'load', initialize);
											</script>
											<div id="map-canvas"></div>
										<?php } // End Tracing location ?>
										
										<?php if($tracing_information = get_field('tracing_information')) { ?>
											<h2>Additional Information</h2>
											<?php echo $tracing_information; ?>
										<?php } ?>
									<?php  } // End check for tracing type ?>

									<h2>More details</h2>
									<?php the_content(); ?>
									</div>
								</section>

								<footer class="article-footer">

								</footer>

								<?php comments_template(); ?>

							</article>

							<?php endwhile; ?>

							<?php else : ?>

									<article id="post-not-found" class="hentry clearfix">
										<header class="article-header">
										<!-- Amended Not Found Messages -->
											<h1><?php _e( 'Case Not Found!', 'ResuePH' ); ?></h1>
										</header>
										<section class="entry-content">
											<p><?php _e( 'Something is missing. Try double checking things.', 'ResuePH' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the single-custom_type.php template.', 'ResuePH' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</div>

						<!-- Removed Sidebar	<?php get_sidebar(); ?> -->

				</div>

			</div>

<?php get_footer(); ?>
