<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carousel</title>
    <link rel="stylesheet" href="../../themes/intranet/assets/css/style.css">
</head>
<body>
    <div class="container mx-auto">

        <h1 class="text-slate-500 text-2xl">Carousel</h1>

        <div id="carousel" class="bg-slate-300 aspect-video relative overflow-hidden flex justify-center items-center">
            <div class="flex flex-col w-full h-full">
                <div class="flex w-full h-full">
                    <div class="grow flex justify-start items-center">
                        <svg id="previous" class="h-16 w-16 text-slate-500 hover:text-slate-900 hover:scale-125 transition ease-in-out delay-200 z-10"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                    <div class="grow flex justify-center items-center">
                        <div class="mx-auto inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent
                                    align-[-0.125em] text-surface motion-reduce:animate-[spin_1.5s_linear_infinite] dark:text-white" role="status">
                            <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]"></span>
                        </div>
                    </div>
                    <div class="grow flex justify-end items-center">
                        <svg id="next" class="h-16 w-16 text-slate-500 hover:text-slate-900 hover:scale-125 transition ease-in-out delay-200 z-10"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 w-full z-10 p-2">
                    <!-- titres -->
                    <h2 data-index="1" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-1 select-none"><a class="text-slate-50 line-clamp-2" href="#1">Au bord de l'océan</a></h2>
                    <h2 data-index="2" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-0 select-none"><a class="text-slate-50 line-clamp-2" href="#2">Billes d'automne</a></h2>
                    <h2 data-index="3" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-0 select-none"><a class="text-slate-50 line-clamp-2" href="#3">Chardon d'éte</a></h2>
                    <h2 data-index="4" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-0 select-none"><a class="text-slate-50 line-clamp-2" href="#4">Un petit tour dans les montagne de Colorado <br/> U.S.A.</a></h2>
                    <h2 data-index="5" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-0 select-none"><a class="text-slate-50 line-clamp-2" href="#5">Allons acheter quelques souvenirs de voyage</a></h2>
                    <h2 data-index="6" class="absolute left-0 bottom-0 w-full text-2xl bg-gray-500/50 p-2 opacity-0 select-none"><a class="text-slate-50 line-clamp-2" href="#6">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aperiam assumenda et laboriosam laborum perspiciatis quae quibusdam recusandae unde vero? Adipisci consequatur illum laudantium molestias non odio officiis quis velit!</a></h2>
                </div>

            </div>
                <!-- images -->
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                    <img alt="1" data-index="1" class="w-full object-cover opacity-1" src="images/347-800x600.jpg" />
                </div>
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                    <img alt="2" data-index="2" class="w-full object-cover opacity-0" src="images/139-600x800.jpg"/>
                </div>
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                 <img alt="3" data-index="3" class="w-full object-cover opacity-0" src="images/35-300x400.jpg"/>
                </div>
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                    <img alt="4" data-index="4" class="w-full object-cover opacity-0" src="images/251-320x180.jpg"/>
                </div>
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                    <img alt="5" data-index="5" class="w-full object-cover opacity-0" src="images/1059-1920x1080.jpg"/>
                </div>
                <div class="aspect-video absolute left-0 right-0 select-none transition ease-in-out delay-1000">
                    <img alt="6" data-index="6" class="w-full object-cover opacity-0" src="images/508-1080x1920.jpg"/>
                </div>
        </div>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('carousel');
        const images = carousel.querySelectorAll('img');
        const titles = carousel.querySelectorAll('h2');
        const prevButton = carousel.querySelector('#previous');
        const nextButton = carousel.querySelector('#next');

        let currentIndex = 0;
        const totalImages = images.length;

        // Fonction pour mettre à jour l'affichage
        function updateCarousel(newIndex) {

            console.log('updateCarousel', newIndex)

            // Masquer l'image et le titre actuels
            images[currentIndex].classList.add('opacity-0');
            titles[currentIndex].classList.add('opacity-0');

            // Mettre à jour l'index
            currentIndex = (newIndex + totalImages) % totalImages;

            // Afficher la nouvelle image et le nouveau titre

            console.log(images[currentIndex])
            images[currentIndex].classList.remove('opacity-0');
            titles[currentIndex].classList.remove('opacity-0');
        }

        // Événement pour la flèche précédente
        prevButton.addEventListener('click', () => {
            updateCarousel(currentIndex - 1);
        });

        // Événement pour la flèche suivante
        nextButton.addEventListener('click', () => {
            updateCarousel(currentIndex + 1);
        });

        // Afficher la première image et le premier titre par défaut
        images[0].classList.remove('opacity-1');
        titles[0].classList.remove('opacity-1');
    });

</script>

</body>
</html>