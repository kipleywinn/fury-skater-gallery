/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

// Skater Gallery Filtering Script

document.addEventListener('DOMContentLoaded', () => {
  // Skater Gallery Filtering
	document.querySelectorAll('.fury-skater-gallery').forEach(gallery => {
		const select = gallery.querySelector('.teamSelect');
		const thumbnails = gallery.querySelectorAll('.photoCard');

		if (select) {
			select.addEventListener('change', () => {
				const selected = select.value;

				thumbnails.forEach(card => {
					if (selected === 'all' || card.classList.contains(selected)) {
						card.style.display = '';
					} else {
						card.style.display = 'none';
					}
				});
			});
		}
	});

  // Skater Modal Logic
  const modal = document.getElementById('skater-bio-modal');
  const modalInner = document.getElementById('skater-bio-modal-inner');
  const closeButton = modal?.querySelector('.skaterModalClose');
  const overlay = modal?.querySelector('.skaterModalOverlay');

  // Open modal on thumbnail click
  document.querySelectorAll('.photoCard').forEach(card => {
    card.addEventListener('click', () => {
      //console.log('Card clicked:', card);
      const key = card.dataset.skaterKey;
      const bioHtmlElement = document.querySelector(`.skater-bio-content[data-skater-key="${key}"]`);
      if (modal && modalInner && bioHtmlElement) {
        modalInner.innerHTML = bioHtmlElement.innerHTML;
        modal.classList.remove('hidden');
        document.body.classList.add('no-scroll'); // Prevent background scroll
      }
    });
  });

  // Close modal
  [closeButton, overlay].forEach(el => {
    el?.addEventListener('click', () => {
      modal?.classList.add('hidden');
      document.body.classList.remove('no-scroll'); // Allow background scroll again
    });
  });

  // Stop propagation inside modal content (so clicks inside it don’t close the modal)
  modal?.querySelector('.skaterModalContent')?.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  // Optional: close modal on click outside modal content
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.add('hidden');
      document.body.classList.remove('no-scroll');
    }
  });
});