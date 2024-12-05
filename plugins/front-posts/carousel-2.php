<!doctype html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Carousel</title>
	<link rel="stylesheet" href="../../themes/intranet/assets/css/style.css">
</head>
<body>
<div class="container mx-auto">

	<h1 class="text-slate-500 text-2xl">Carousel 2</h1>

    <div class="relative w-full mx-auto">
        <!-- Carousel wrapper -->
        <div class="overflow-hidden relative">
            <div class="flex transition-transform duration-500 ease-in-out transform aspect-video" id="carousel" >
                <!-- Slide -->
                <div class="min-w-full">
                    <img src=images/900x1200.jpg" alt="Slide 1" class="relative w-full h-full object-cover" />
                    <div class="absolute left-2 right-2 bottom-2 bg-red-500 p-2 bg-opacity-50 rounded-lg">
                        <a href="#1">
                            <h1 class="text-2xl text-slate-50">Slide 1</h1>
                            <p>Une image de chat en 900x1200</p>
                            <p>Une image de chat en 900x1200</p>
                            <p>Une image de chat en 900x1200</p>
                            <p>Une image de chat en 900x1200</p>
                            <p>Une image de chat en 900x1200</p>
                            <p>Une image de chat en 900x1200</p>
                        </a>
                    </div>
                </div>
                <!-- Slide -->
                <div class="min-w-full">
                    <img src="images/347-800x600.jpg" alt="Slide 2" class="relative w-full h-full object-cover" />
                    <a class="absolute w-full bottom-2 bg-slate-500/50 p-2" href="#">
                        <p>Slide 2</p>
                        <p>Une image en 800x600</p>
                    </a>
                </div>
                <!-- Slide -->
                <div class="min-w-full">
                    <img src="images/139-600x800.jpg" alt="Slide 3" class="relative w-full h-full object-cover" />
                    <a class="absolute w-full bottom-2 bg-slate-500/50 p-2" href="#">
                        <p>Slide 3</p>
                        <p>Une image en 600x800</p>
                    </a>
                </div>
                <!-- Slide -->
                <div class="min-w-full">
                    <img src="images/35-300x400.jpg" alt="Slide 4" class="relative w-full h-full object-cover" />
                    <a class="absolute w-full bottom-2 bg-slate-500/50 p-2" href="#">
                        <p>Slide 4</p>
                        <p>Une image en 300x400</p>
                    </a>
                </div>
                <!-- Slide -->
                <div class="min-w-full">
                    <img src="images/1059-1920x1080.jpg" alt="Slide 5" class="relative w-full h-full object-cover" />
                    <a class="absolute w-full bottom-2 bg-slate-500/50 p-2" href="#">
                        <p>Slide 5</p>
                        <p>Une image en 1920x1080</p>
                    </a>
                </div>
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

</body>
</html>

