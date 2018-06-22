		</div>

		<?php $prefooter = (get_field('use_pre-footer',get_the_ID(),false) == 1?true:false); ?>

		<footer id="nu__global-footer" class="<?=($prefooter?'addprefooter ':'')?><?=(trim($_SERVER['REQUEST_URI']) === '/'?'collapse absolute':'')?>">

			<?php

				if($prefooter){ get_template_part('loops/loop-prefooter'); }
			?>

			<?php wp_footer(); ?>

			<!-- <div class="nu__footer">
				<div class="nu__footer-hideshow js_footer-hideshow" title="Click to show/hide the footer"></div>
				<div>

					<?php get_template_part('loops/loop-footercampuses'); ?>

					<div>
                        <p>Coastal Sustainability Institute</p>
                        <?php nudev_nav(); ?>
						<p style="padding-left: 40px;">Connect</p>
						<ul>
							<li><a href="//www.facebook.com/northeastern/" title="Friend us on Facebook" target="_blank"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><defs><style>.cls-1{}</style></defs><title>Friend us on Facebook</title><circle class="cls-1" cx="256" cy="256" r="256"/><path d="M301.6,151.2h36.3V97H286.5c-29.6,0-68.1,19-68.1,74.2v43H169.3v56.1h49.1v146H277v-146h48.8l8.1-56.1H277V178.5C277,160.1,286,151.2,301.6,151.2Z"/></svg></a></li>
							<li><a href="//twitter.com/Northeastern" title="Follow us on Twitter" target="_blank"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 510.57 510.57"><defs><style>.cls-1{}</style></defs><title>Follow us on Twitter</title><path class="cls-1" d="M255.29,1.43C114.3,1.43,0,115.75,0,256.73S114.3,512,255.29,512,510.57,397.72,510.57,256.73,396.28,1.43,255.29,1.43Zm128.6,203.82c.13,2.76.19,5.53.19,8.33,0,85-64.72,183-183,183a182.12,182.12,0,0,1-98.62-28.89,131.78,131.78,0,0,0,15.34.89,129.07,129.07,0,0,0,79.9-27.53,64.43,64.43,0,0,1-60.09-44.7,62.61,62.61,0,0,0,12.1,1.15,63.61,63.61,0,0,0,17-2.25A64.39,64.39,0,0,1,115,232.22c0-.28,0-.55,0-.83a64.25,64.25,0,0,0,29.15,8.06,64.42,64.42,0,0,1-19.92-85.91,182.61,182.61,0,0,0,132.62,67.21,64.38,64.38,0,0,1,109.62-58.68,128.49,128.49,0,0,0,40.86-15.61A64.52,64.52,0,0,1,379,182.05a128.53,128.53,0,0,0,37-10.13A130.11,130.11,0,0,1,383.89,205.25Z" transform="translate(0 -1.43)"/></svg></a></li>
							<li><a href="//www.youtube.com/user/Northeastern" title="Check us out on YouTube" target="_blank"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><defs><style>.cls-1{}</style></defs><title>Check us out on YouTube</title><g id="Layer_1" data-name="Layer 1"><polygon class="cls-1" points="221.28 308.14 308.19 256 221.28 203.86 221.28 308.14"/><path class="cls-1" d="M256,0C114.62,0,0,114.61,0,256S114.62,512,256,512,512,397.38,512,256,397.38,0,256,0ZM395,267.22a476.39,476.39,0,0,1-2.79,48s-2.72,20.44-11,29.42c-10.57,11.81-22.41,11.87-27.85,12.58-38.91,3-97.33,3.07-97.33,3.07s-72.29-.71-94.53-3c-6.19-1.23-20.06-.88-30.65-12.67-8.34-9-11-29.42-11-29.42a476.12,476.12,0,0,1-2.79-48v-22.5a475.73,475.73,0,0,1,2.79-48s2.72-20.45,11-29.46c10.57-11.82,22.41-11.89,27.85-12.56,38.9-3,97.27-3,97.27-3h.12s58.37,0,97.27,3c5.42.67,17.28.74,27.85,12.54,8.34,9,11,29.46,11,29.46a476.39,476.39,0,0,1,2.79,48Z"/></g></svg></a></li>
						</ul>
					</div>

					<p>
						430 Nahant Road, Nahant, MA 01908. <a href="tel:7815817370" title="Give us a call">781.581.7370</a></p>

				</div>
			</div> -->
		</footer>
	</body>
</html>
