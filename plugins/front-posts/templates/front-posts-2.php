<div class="relative w-full mx-auto">
	<!-- Carousel wrapper -->
	<div class="overflow-hidden relative">
		<div class="flex transition-transform duration-500 ease-in-out transform aspect-video" id="carousel" >
            <?php
            if ( $args ) {
	            foreach ($args['data'] as $urls) {
		            get_template_part( 'template-parts/carousel', 'item', ['url' => $urls]);
	            }
            }
            ?>
		</div>
	</div>
	<!-- Navigation buttons -->
	<span id="previous"  class="absolute top-1/2 left-2 transform -translate-y-1/2 p-3 bg-gray-700 bg-opacity-50 rounded-full text-white hover:bg-opacity-75 focus:outline-none"
	      onclick="scrollCarousel(-1)" >
            <svg class="h-6 w-6 text-slate-50 hover:text-slate-900 hover:scale-125"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
        </span>
	<span id="next" class="absolute top-1/2 right-2 transform -translate-y-1/2 p-3 bg-gray-700 bg-opacity-50 rounded-full text-white hover:bg-opacity-75 focus:outline-none"
	      onclick="scrollCarousel(1)" >
            <svg class="h-6 w-6 text-slate-50 hover:text-slate-900 hover:scale-125"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
	<!-- /Navigation buttons -->
</div>

<script>
    let currentIndex = 0;
    function scrollCarousel(direction) {
        const carousel = document.getElementById("carousel");
        const totalSlides = carousel.children.length;
        currentIndex = (currentIndex + direction + totalSlides) % totalSlides;
        carousel.style.transform = `translateX(-${ currentIndex * 100}%)`;
    }
</script>