<?php

	/* Template Name: Homepage */




	get_header();

?>

	<main id="nu__homepage" role="main" aria-label="content" class="smooth">
		<div id="nu__seo-content"><h1><?=get_the_content()?></h1></div>
        <div class="nu__fullwrap">
            <div class="nu__hero">
                <div class="nu__txtbox"><h1>Building knowledge and innovative strategies to create cleaner, safer, smarter coastal communities.</h1><p style="padding-top: 15px;"><a href="" class="nu__button" title="Learn More">Learn More</a></p></div>
                <article class="nu__imgbox" style="background-color: #777;">
                    <a href="#" title="Click here now to learn more">Research</a>
                    <div class="nu__panel-content">
                        <div>
                            <p>Exploration that delivers actionable results</p>
                            <h2><span>Research</span></h2>
                        </div>
                    </div>
                    <div class="gradient"></div>
                </article>
            </div>
            <div class="nu__stories">
                <article style="background-color: #777;">
                    <a href="#" title="Click here now to learn more">Partnerships</a>
                    <div class="nu__panel-content">
                        <div>
                            <p>Collaboration among the world's best minds</p>
                            <h2><span>Partnerships</span></h2>
                        </div>
                    </div>
                    <div class="gradient"></div>
                </article>
                <article style="background-color: #777;">
                    <a href="#" title="Click here now to learn more">People</a>
                    <div class="nu__panel-content">
                        <div>
                            <p>Premier faculty conquering complex coastal threats</p>
                            <h2><span>People</span></h2>
                        </div>
                    </div>
                    <div class="gradient"></div>
                </article>
                <article style="background-color: #777;">
                    <a href="#" title="Click here now to learn more">Resources</a>
                    <div class="nu__panel-content">
                        <div>
                            <p>A state-of-the-art coastal hub near Boston</p>
                            <h2><span>Resources</span></h2>
                        </div>
                    </div>
                    <div class="gradient"></div>
                </article>
                
            </div>
            <div class="nu__quote">
                <h1>We're committed to producing innovative, interdisciplinary research that yields sustainable solutions to the biggest challenges conrfonting the world's coastal ecosystems.</h1>
                <p>Geoff Trussell, executive director, Coastal Sustainability Institute</p>
            </div>
            <div class="nu__callouts">
                <div class="nu__inner-wrap">
                    <h3>Coastal Imperative</h3>
                    <div>
                        <div><h2>40%</h2><p>of the world's population lives within 100 km of a coast</p></div>
                        <div><h2>$1.5T</h2><p>Amount oceans contribute annually in value-add to the world economy</p></div>
                        <div><h2>275M</h2><p>Estimated number of people living in areas that will be flooded due to global</p></div>
                    </div>
                </div>
            </div>
        </div>
		<!--<div id="nu__stories">
			<?php get_template_part('loops/loop-homepagestories'); ?>

		</div>-->

	</main>

<?php get_footer(); ?>
