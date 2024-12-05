<!-- Carousel (Plugin) -->
<div id="carousel" class="bg-slate-300 aspect-video relative overflow-hidden flex justify-center items-center">
	<div class="flex flex-col w-full h-full border border-red-500">
		<div id="navigation" class="flex w-full h-full">
			<div class="grow flex justify-start items-center">
				<svg id="previous" class="h-16 w-16 text-slate-500 hover:text-slate-900 hover:scale-125 transition ease-in-out delay-200 z-10"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
				</svg>
			</div>
			<div class="grow flex justify-center items-center">
				<div id="loader" class="mx-auto inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-surface motion-reduce:animate-[spin_1.5s_linear_infinite] dark:text-white" role="status">
					<span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]"></span>
				</div>
			</div>
			<div class="grow flex justify-end items-center">
				<svg id="next" class="h-16 w-16 text-slate-500 hover:text-slate-900 hover:scale-125 transition ease-in-out delay-200 z-10"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
				</svg>
			</div>
		</div>
		<!-- titles -->
		<div id="title" class="absolute bottom-0 w-full z-10 p-2">
			<?php
			$index=1;
			/** @var array $args */
			foreach ($args['data'] as $urls) {
				printf('<h2 id="title" data-index="%d" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 select-none opacity-0">', $index);
				printf('<a class="text-slate-50 line-clamp-2" href="%s">%s</a>', $urls['link'], $urls['title']);
				printf('</h2>');
				$index++;
			}
			?>
		</div>
	</div>
	<!-- images -->
	<?php
	$index=1;
	/** @var array $args */
	foreach ($args['data'] as $urls) {
		printf('<div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">');
		printf('<img alt="" title="%d" class="w-full aspect-video object-cover opacity-0" src="%s" />', $index, $urls['thumbnail']  );
		printf('</div>');
		$index++;
	}
	?>
</div>

<script>

    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('carousel');
        const previousButton = document.getElementById('previous');
        const nextButton = document.getElementById('next');
        const loader = document.getElementById('loader');
        const images = carousel.querySelectorAll('img');
        const titles = carousel.querySelectorAll('#title[data-index]');
        let currentIndex = 0;
        const totalItems = images.length;
        function hideAll() {
            images.forEach(img => {
                img.classList.add('opacity-0', 'absolute');

            });
            titles.forEach(title => {
                title.classList.add('opacity-0', '-z1')
            });
        }
        // Show specific image and title
        function showItem(index) {
            hideAll();
            // Show image
            images[index].classList.remove('opacity-0', 'absolute')
            images[index].classList.add('opacity-1', 'relative')

            // Show corresponding title
            titles[index].classList.remove('opacity-0','-z-1')
            titles[index].classList.add('opacity-1','z-10')


        }

        // Initial state - show first item after loading
        setTimeout(() => {
            loader.style.display = 'none';
            showItem(currentIndex);
        }, 250);

        // Next button functionality
        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % totalItems;
            showItem(currentIndex);
        });

        // Previous button functionality
        previousButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + totalItems) % totalItems;
            showItem(currentIndex);
        });
    });

</script>

