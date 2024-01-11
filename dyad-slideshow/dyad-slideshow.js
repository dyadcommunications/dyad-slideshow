/**
 * Init all heart slideshow blocks
 * @param newcontainer
 */
function initAllSlideshows(newcontainer) {
	const slideshows = document.querySelectorAll(".heart-slideshow.dyad-slideshow");
	for (const slideshow of slideshows) {
		const options = {
			slideshow: slideshow,
			transition: 3000,
			delay: 1000,
			loop: true,
			randomize: false,
			paused: false,
			swipe: true,
			buttons: true,
		};
		new HeartSlider(options);
	}
}
