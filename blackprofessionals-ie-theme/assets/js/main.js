(function () {
  'use strict';

  var toggle = document.querySelector('.primary-menu-toggle');
  var nav = document.querySelector('.primary-navigation');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  // Tap-to-open submenus on touch devices (hover doesn't apply).
  document.querySelectorAll('.menu-item-has-children > a').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var parentLi = link.parentElement;
      var isMobile = window.matchMedia('(max-width: 860px)').matches;
      if (isMobile) {
        e.preventDefault();
        parentLi.classList.toggle('is-open');
      }
    });
  });
})();
