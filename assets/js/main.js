(function () {
	'use strict';

	/* Mobile menu */
	var btn = document.querySelector('.nn-menu-btn');
	var nav = document.getElementById('nn-nav');
	if (btn && nav) {
		btn.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	/* Copy link */
	document.querySelectorAll('[data-copy]').forEach(function (el) {
		el.addEventListener('click', function () {
			if (!navigator.clipboard) { return; }
			var label = el.textContent;
			navigator.clipboard.writeText(el.getAttribute('data-copy')).then(function () {
				el.textContent = el.getAttribute('data-copied') || label;
				setTimeout(function () { el.textContent = label; }, 1600);
			});
		});
	});

	/* Pinterest "Save" button on photos in posts (the top photo and every photo in the text):
	   saves that exact image with the post's link.
	   Skips small images and any image with data-pin-nopin. */
	var content = document.querySelector('.nn-content[data-pin-url]');
	if (content) {
		var pageUrl = content.getAttribute('data-pin-url');
		var pageTitle = content.getAttribute('data-pin-title') || document.title;
		document.querySelectorAll('.nn-article__photo img, .nn-content[data-pin-url] img').forEach(function (img) {
			if (img.hasAttribute('data-pin-nopin') || img.closest('a[href*="pinterest."]')) { return; }
			var add = function () {
				if ((img.naturalWidth || img.width) < 300) { return; }
				var holder = img.closest('figure') || img.parentNode;
				holder.classList.add('nn-pinnable');
				var src = img.currentSrc || img.src;
				// Prefer the full-size file over a resized one (…-683x1024.jpg → ….jpg).
				var full = src.replace(/-\d+x\d+(\.[a-z]+)(\?.*)?$/i, '$1');
				var desc = img.getAttribute('alt') || pageTitle;
				var a = document.createElement('a');
				a.className = 'nn-pin-save';
				a.href = 'https://www.pinterest.com/pin/create/button/?url=' + encodeURIComponent(pageUrl) + '&media=' + encodeURIComponent(full) + '&description=' + encodeURIComponent(desc);
				a.target = '_blank';
				a.rel = 'noopener nofollow';
				a.setAttribute('aria-label', 'Save this photo to Pinterest');
				a.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.9-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.3-.9 3.5-.2 1.1.5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.6-1.8-4.4-4.5-4.4-3 0-4.8 2.3-4.8 4.6 0 .9.3 1.9.8 2.4.1.1.1.2.1.3l-.3 1.2c0 .2-.2.3-.4.2-1.4-.6-2.2-2.6-2.2-4.2 0-3.4 2.5-6.6 7.2-6.6 3.8 0 6.7 2.7 6.7 6.3 0 3.8-2.4 6.8-5.7 6.8-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.3 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2z"/></svg><span>Save</span>';
				holder.appendChild(a);
			};
			if (img.complete) { add(); } else { img.addEventListener('load', add, { once: true }); }
		});
	}
})();
